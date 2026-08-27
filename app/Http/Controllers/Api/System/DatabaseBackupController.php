<?php

namespace App\Http\Controllers\Api\System;

use App\Http\Controllers\Controller;
use App\Services\System\DatabaseBackupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DatabaseBackupController extends Controller
{
    protected DatabaseBackupService $backupService;

    public function __construct(DatabaseBackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Tampilkan halaman GUI manajemen backup database di web admin.
     */
    public function viewIndex()
    {
        $backups = $this->backupService->listBackups();
        return view('admin.settings.backup.index', [
            'page' => 'Pencadangan Basis Data (Database Backup)',
            'backups' => $backups
        ]);
    }

    /**
     * Mengambil daftar file backup (JSON).
     */
    public function index(): JsonResponse
    {
        $backups = $this->backupService->listBackups();
        return response()->json([
            'status'  => true,
            'total'   => count($backups),
            'backups' => $backups,
        ]);
    }

    /**
     * Trigger pembuatan backup database baru.
     */
    public function create(): JsonResponse
    {
        $result = $this->backupService->createBackup();
        $statusCode = $result['status'] ? 201 : 500;
        return response()->json($result, $statusCode);
    }

    /**
     * Mengunduh berkas backup database secara aman.
     */
    public function download(string $filename)
    {
        $safeName = basename($filename);
        $filepath = storage_path('app/backups' . DIRECTORY_SEPARATOR . $safeName);

        if (!file_exists($filepath)) {
            return response()->json([
                'status'  => false,
                'message' => 'Berkas backup tidak ditemukan.'
            ], 404);
        }

        return response()->download($filepath, $safeName, [
            'Content-Type' => 'application/sql',
        ]);
    }

    /**
     * Menghapus file backup tertentu.
     */
    public function destroy(string $filename): JsonResponse
    {
        $deleted = $this->backupService->deleteBackup($filename);
        if ($deleted) {
            return response()->json([
                'status'  => true,
                'message' => "Berkas backup {$filename} berhasil dihapus."
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Berkas backup tidak ditemukan atau gagal dihapus.'
        ], 404);
    }
}
