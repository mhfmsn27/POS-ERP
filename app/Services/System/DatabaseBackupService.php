<?php

namespace App\Services\System;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DatabaseBackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    /**
     * Membuat snapshot cadangan basis data dalam format SQL.
     *
     * @return array
     */
    public function createBackup(): array
    {
        try {
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "backup_poshub_{$timestamp}.sql";
            $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

            $tables = DB::select('SHOW TABLES');
            $dbNameKey = 'Tables_in_' . DB::getDatabaseName();

            $sqlDump = "-- ====================================================\n";
            $sqlDump .= "-- POSHUB ACCOUNTING STANDALONE DATABASE BACKUP\n";
            $sqlDump .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
            $sqlDump .= "-- Database: " . DB::getDatabaseName() . "\n";
            $sqlDump .= "-- ====================================================\n\n";
            $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\nSET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n";

            foreach ($tables as $t) {
                $tableName = $t->$dbNameKey ?? current((array)$t);
                if (empty($tableName)) continue;

                // Create Table DDL
                $createTableResult = DB::select("SHOW CREATE TABLE `{$tableName}`");
                if (!empty($createTableResult)) {
                    $row = (array)$createTableResult[0];
                    $createSql = $row['Create Table'] ?? '';
                    $sqlDump .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                    $sqlDump .= $createSql . ";\n\n";
                }

                // Table Data Rows
                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $sqlDump .= "INSERT INTO `{$tableName}` VALUES \n";
                    $valueRows = [];
                    foreach ($rows as $r) {
                        $values = array_map(function ($val) {
                            if (is_null($val)) return "NULL";
                            return "'" . addslashes((string)$val) . "'";
                        }, (array)$r);
                        $valueRows[] = "(" . implode(', ', $values) . ")";
                    }
                    $sqlDump .= implode(",\n", $valueRows) . ";\n\n";
                }
            }

            $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

            File::put($filepath, $sqlDump);
            $fileSize = filesize($filepath);

            // Auto prune files older than 30 days
            $this->pruneOldBackups(30);

            Log::info("Database backup created successfully: {$filename} ({$fileSize} bytes)");

            return [
                'status'    => true,
                'filename'  => $filename,
                'size'      => $this->formatBytes($fileSize),
                'size_raw'  => $fileSize,
                'created_at'=> date('Y-m-d H:i:s'),
                'message'   => 'Snapshot database berhasil dibuat.'
            ];
        } catch (\Throwable $e) {
            Log::error("Database backup failed: " . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Gagal membuat backup database: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Mengambil daftar seluruh berkas backup yang tersedia.
     *
     * @return array
     */
    public function listBackups(): array
    {
        $files = File::files($this->backupDir);
        $backups = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'sql' || $file->getExtension() === 'gz') {
                $backups[] = [
                    'filename'   => $file->getFilename(),
                    'size'       => $this->formatBytes($file->getSize()),
                    'size_bytes' => $file->getSize(),
                    'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                    'path'       => $file->getRealPath(),
                ];
            }
        }

        // Urutkan dari yang terbaru
        usort($backups, function ($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });

        return $backups;
    }

    /**
     * Menghapus file backup tertentu.
     *
     * @param string $filename
     * @return bool
     */
    public function deleteBackup(string $filename): bool
    {
        $safeName = basename($filename);
        $filepath = $this->backupDir . DIRECTORY_SEPARATOR . $safeName;

        if (File::exists($filepath)) {
            return File::delete($filepath);
        }
        return false;
    }

    /**
     * Menghapus backup yang usianya lebih dari $days hari.
     *
     * @param int $days
     * @return int Jumlah file yang dihapus
     */
    public function pruneOldBackups(int $days = 30): int
    {
        $cutoff = time() - ($days * 86400);
        $deleted = 0;
        $files = File::files($this->backupDir);

        foreach ($files as $file) {
            if ($file->getMTime() < $cutoff) {
                File::delete($file->getRealPath());
                $deleted++;
            }
        }

        return $deleted;
    }

    /**
     * Format byte ke satuan yang mudah dibaca (KB, MB, GB).
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
