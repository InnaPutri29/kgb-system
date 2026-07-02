<?php

namespace App\Exports;

use App\Models\RiwayatKgb;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KgbExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $tahunAwal;
    protected $tahunAkhir;
    protected $rowNumber = 0;

    public function __construct($tahunAwal, $tahunAkhir)
    {
        $this->tahunAwal = $tahunAwal;
        $this->tahunAkhir = $tahunAkhir;
    }

    public function query()
    {
        $query = RiwayatKgb::query()->with('pegawai');

        if ($this->tahunAwal && $this->tahunAkhir) {
            $query->whereYear('tmt_baru', '>=', $this->tahunAwal)
                  ->whereYear('tmt_baru', '<=', $this->tahunAkhir);
        }

        return $query->orderBy('tmt_baru', 'desc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Pegawai',
            'NIP',
            'Golongan / Pangkat',
            'Nomor SK Baru',
            'Tanggal Ditetapkan',
            'TMT Baru',
            'Masa Kerja Golongan',
            'Gaji Pokok Lama (Rp)',
            'Gaji Pokok Baru (Rp)',
            'Pejabat Penetap',
        ];
    }

    public function map($riwayat): array
    {
        $this->rowNumber++;
        $pegawai = $riwayat->pegawai;

        return [
            $this->rowNumber,
            $pegawai ? $pegawai->nama_lengkap : '-',
            $pegawai ? ' ' . $pegawai->nip : '-',
            $pegawai ? $pegawai->golongan : '-',
            $riwayat->nomor_sk_baru,
            $riwayat->tanggal_ditetapkan ? $riwayat->tanggal_ditetapkan->format('d-m-Y') : '-',
            $riwayat->tmt_baru ? $riwayat->tmt_baru->format('d-m-Y') : '-',
            $riwayat->masa_kerja_tahun_baru . ' Tahun ' . $riwayat->masa_kerja_bulan_baru . ' Bulan',
            number_format($riwayat->gaji_pokok_lama, 0, ',', '.'),
            number_format($riwayat->gaji_pokok_baru, 0, ',', '.'),
            $riwayat->pejabat_penetap,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
