<?php

namespace App\Services;

class CsvExport
{
    /**
     * Build CSV string with RFC-4180 quoting (fputcsv).
     *
     * @param  array<int, string>  $header
     * @param  iterable<int, array<int|string, mixed>>  $rows
     */
    public static function csv(array $header, iterable $rows): string
    {
        $stream = fopen('php://temp', 'rb+');

        if ($stream === false) {
            return '';
        }

        fputcsv($stream, $header);

        foreach ($rows as $row) {
            fputcsv($stream, array_map(self::escapeFormula(...), array_map('strval', $row)));
        }

        $output = stream_get_contents($stream);
        fclose($stream);

        return $output === false ? '' : $output;
    }

    /**
     * Hindari formula injection di Excel: nilai yang berawalan =, +, -, @,
     * paling awal dan bukan angka, diberi awalan apostrophe.
     */
    private static function escapeFormula(string $value): string
    {
        if (is_numeric($value)) {
            return $value;
        }

        return preg_match('/^[=+\-@]/', $value) === 1 ? "'".$value : $value;
    }
}
