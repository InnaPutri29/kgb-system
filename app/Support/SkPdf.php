<?php

namespace App\Support;

use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Membuat PDF SK KGB yang selalu muat dalam satu halaman.
 * Jika isi SK (misalnya nama atau jabatan yang panjang) membuat SK menjadi dua halaman,
 * tata letak dirapatkan bertahap sampai muat, dengan ruang stempel TTE di bawah tetap terjaga.
 */
class SkPdf
{
    private const TINGKAT_MAKSIMAL = 3;

    /**
     * @param  array  $data  Data untuk view admin.kgb.sk-pdf (riwayat, pegawai, instansi, pejabatTerdahulu)
     * @param  callable|null  $ubahHtml  Penyesuaian HTML sebelum dicetak, misalnya tanda SPESIMEN untuk data uji
     * @return string Isi berkas PDF
     */
    public static function buat(array $data, ?callable $ubahHtml = null): string
    {
        for ($tingkat = 0; $tingkat <= self::TINGKAT_MAKSIMAL; $tingkat++) {
            $html = view('admin.kgb.sk-pdf', $data + ['kompak' => $tingkat])->render();
            if ($ubahHtml) {
                $html = $ubahHtml($html);
            }

            $dompdf = Pdf::loadHTML($html)->setPaper('f4', 'portrait')->getDomPDF();
            $dompdf->render();

            if ($dompdf->getCanvas()->get_page_count() <= 1 || $tingkat === self::TINGKAT_MAKSIMAL) {
                return $dompdf->output();
            }
        }

        return '';
    }
}
