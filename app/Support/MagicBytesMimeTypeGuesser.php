<?php

namespace App\Support;

use Symfony\Component\Mime\MimeTypeGuesserInterface;
use ZipArchive;

/**
 * Pengenal jenis berkas dari beberapa byte awal isinya.
 * Dipakai hanya jika ekstensi fileinfo PHP tidak tersedia (seperti di hosting),
 * untuk jenis berkas yang diunggah ke sistem: gambar, PDF, dan Excel.
 */
class MagicBytesMimeTypeGuesser implements MimeTypeGuesserInterface
{
    public function isGuesserSupported(): bool
    {
        return true;
    }

    public function guessMimeType(string $path): ?string
    {
        if (!is_file($path) || !is_readable($path)) {
            return null;
        }

        $awal = (string) file_get_contents($path, false, null, 0, 16);

        return match (true) {
            str_starts_with($awal, "\xFF\xD8\xFF") => 'image/jpeg',
            str_starts_with($awal, "\x89PNG\r\n\x1A\n") => 'image/png',
            str_starts_with($awal, 'GIF8') => 'image/gif',
            str_starts_with($awal, 'RIFF') && substr($awal, 8, 4) === 'WEBP' => 'image/webp',
            str_starts_with($awal, '%PDF') => 'application/pdf',
            str_starts_with($awal, "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1") => 'application/vnd.ms-excel',
            str_starts_with($awal, "PK\x03\x04") => $this->jenisZip($path),
            default => null,
        };
    }

    private function jenisZip(string $path): string
    {
        if (class_exists(ZipArchive::class)) {
            $zip = new ZipArchive();
            if ($zip->open($path) === true) {
                $excel = $zip->locateName('xl/workbook.xml') !== false;
                $zip->close();
                if ($excel) {
                    return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                }
            }
        } elseif (str_contains((string) file_get_contents($path, false, null, 0, 4096), 'xl/')) {
            return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        }

        return 'application/zip';
    }
}
