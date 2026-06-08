<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplatePegawaiExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'nip',
            'nama_lengkap',
            'email',
            'pangkat',
            'golongan',
            'jabatan',
            'kantor_tempat_kerja',
            'tmt_gaji_terakhir',
            'masa_kerja_tahun',
            'masa_kerja_bulan',
            'gaji_pokok_terakhir'
        ];
    }

    public function array(): array
    {
        return [
            [
                "'198406072009021001", // Kutip tunggal memaksa Excel membaca sebagai teks agar NIP tidak rusak
                "Budi Santoso",
                "budi@contoh.com",
                "Penata Muda",
                "III/a",
                "Perawat Pelaksana",
                "RSD Sidawangi",
                "2023-01-01",
                "5",
                "0",
                "2500000"
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1    => ['font' => ['bold' => true]],
        ];
    }
}
