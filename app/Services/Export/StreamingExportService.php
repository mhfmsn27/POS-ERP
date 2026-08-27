<?php

namespace App\Services\Export;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamingExportService
{
    /**
     * Stream an Eloquent query directly into a CSV download.
     *
     * @param Builder $query
     * @param array $headers
     * @param callable $rowCallback (function($row): array)
     * @param string $filename
     * @return StreamedResponse
     */
    public function streamCsv(Builder $query, array $headers, callable $rowCallback, string $filename = 'export.csv'): StreamedResponse
    {
        $responseHeaders = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return new StreamedResponse(function () use ($query, $headers, $rowCallback) {
            $handle = fopen('php://output', 'w');

            // Tambahkan UTF-8 BOM agar terbaca sempurna di Microsoft Excel
            fputs($handle, "\xEF\xBB\xBF");

            // Tulis baris header CSV
            fputcsv($handle, $headers);

            // Gunakan cursor() untuk streaming record satu per satu (Memory footprint < 5MB)
            foreach ($query->cursor() as $record) {
                $row = $rowCallback($record);
                if (is_array($row)) {
                    fputcsv($handle, $row);
                }
            }

            fclose($handle);
        }, 200, $responseHeaders);
    }
}
