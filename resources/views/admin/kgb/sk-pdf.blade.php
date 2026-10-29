<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SK KGB - {{ $pegawai->nama_lengkap }}</title>
    @php
        // Tingkat kerapatan: 0 = normal. Naik otomatis (lihat AppSupportSkPdf) jika isi SK melebihi satu halaman.
        $k = max(0, min(3, (int) ($kompak ?? 0)));
        // Ukuran huruf mengikuti format SK resmi; yang dirapatkan lebih dulu adalah jarak dan logo.
        // Hanya tingkat terakhir yang sedikit mengecilkan isi surat.
        $u = [
            'huruf'  => [12, 12, 12, 11.5][$k],
            'baris'  => [1.15, 1.1, 1.05, 1.0][$k],
            'logo'   => [33, 30, 27, 24][$k],
            'jarak'  => [15, 10, 6, 4][$k],
            'kotak'  => [12, 10, 8, 6][$k],
            'ttd'    => [27, 22, 10, 6][$k], // jarak paragraf penutup ke jabatan penandatangan
        ];
        // Book Antiqua dipakai bila berkas hurufnya ada di resources/fonts.
        // Berkas itu tidak ikut repositori karena berlisensi Monotype; tanpa berkas, SK memakai Times New Roman.
        $hurufAntiqua = array_filter([
            ['BKANT.TTF', 'normal', 'normal'],
            ['ANTQUAB.TTF', 'bold', 'normal'],
            ['ANTQUAI.TTF', 'normal', 'italic'],
            ['ANTQUABI.TTF', 'bold', 'italic'],
        ], fn ($h) => is_file(resource_path('fonts/' . $h[0])));
        $adaAntiqua = is_file(resource_path('fonts/BKANT.TTF'));
        $hurufIsi = ($adaAntiqua ? "'Book Antiqua', " : '') . "'Times New Roman', Times, serif";
    @endphp
    <style>
        @if($adaAntiqua)
            @foreach($hurufAntiqua as [$berkas, $tebal, $gaya])
        @font-face {
            font-family: 'Book Antiqua';
            src: url('{{ str_replace('\\', '/', resource_path('fonts/' . $berkas)) }}') format('truetype');
            font-weight: {{ $tebal }};
            font-style: {{ $gaya }};
        }
            @endforeach
        @endif
        @page {
            size: 215.9mm 330mm;
            /* Bawah 4,5 cm: ruang untuk kode QR dan keterangan BSrE yang ditempel otomatis saat TTE di SIDEBAR */
            margin: 1.5cm 2cm 4.5cm 2cm;
        }
        body { 
            font-family: {!! $hurufIsi !!};
            font-size: {{ $u['huruf'] }}pt;
            line-height: {{ $u['baris'] }}; 
        }
        .header {
            width: 100%;
            font-family: Arial, sans-serif;
        }
        .garis-kop {
            border-top: 3px solid black;
            border-bottom: 1px solid black;
            height: 2px;
            margin-top: 10px;
            margin-bottom: 10px;
            width: 100%;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .header td {
            vertical-align: middle;
        }
        /* Ukuran kop mengikuti kop surat resmi RSD Sidawangi */
        .header .logo {
            width: 29mm;
            text-align: left;
            padding-left: 0;
        }
        .header .logo img {
            height: {{ $u['logo'] }}mm;
            width: auto;
        }
        .header .text-center {
            text-align: center;
        }
        .header h1, .header h2 { line-height: 1.3; }
        .header h1 {
            font-size: 14pt;
            margin: 0;
            font-weight: normal;
        }
        .header h2 {
            font-size: 18pt;
            margin: 0;
            font-weight: normal;
            letter-spacing: 1px;
        }
        .header h2.nama-instansi {
            font-size: 18pt;
            font-weight: bold;
            letter-spacing: normal;
        }
        .header p {
            font-size: 11pt;
            margin: 0;
            margin-top: 2px;
            line-height: 1.35;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: {{ $u['huruf'] }}pt;
        }
        .align-top { vertical-align: top; }
        .text-justify { text-align: justify; }
        .mt-2 { margin-top: 10px; }
        .mb-2 { margin-bottom: 10px; }
        
        .content-table td {
            vertical-align: top;
            padding: 1px 0;
        }
        /* Selektor dibuat lebih spesifik agar jarak tidak tertimpa padding .content-table td */
        .content-table td.number-col {
            width: 5.5%;
            text-align: right;
            padding-right: 12px;
        }
        .label-col {
            width: 37%;
        }
        .colon-col {
            width: 2%;
        }
        .value-col {
            width: 55.5%;
        }
        .signature-box {
            float: right;
            width: 380px;
            margin-top: 30px;
        }
        .signature-inner {
            border: 1px solid black;
            border-radius: 10px;
            padding: 10px;
            font-size: 11pt;
        }
        /* Pengganti <u>: garis bawah dompdf memotong huruf berekor (j, g) pada Book Antiqua */
        .garis-bawah { display: inline-block; border-bottom: 1px solid black; line-height: 1.05; }
        .clear { clear: both; }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td class="logo">
                    {{-- Logo Daerah di kiri --}}
                    @if($instansi && $instansi->logo)
                        <img src="{{ public_path('storage/' . $instansi->logo) }}" alt="Logo">
                    @else
                        <!-- Logo Placeholder jika tidak ada logo -->
                    @endif
                </td>
                <td class="text-center">
                    <h1>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h1>
                    <h2>D I N A S&nbsp;&nbsp;K E S E H A T A N</h2>
                    <h2 class="nama-instansi">{{ strtoupper($instansi?->nama_instansi ?? 'RUMAH SAKIT DAERAH SIDAWANGI') }}</h2>
                    <p>{!! $instansi?->alamat ? nl2br(e($instansi->alamat)) : 'Jalan Pangeran Kejaksan Sumber Telepon (0231) 8330707 <i>Fax :</i> (0231) 8330747<br><i>Website:</i> www.rsp.jabarprov.go.id <i>e-mail:</i> rsp@jabarprov.go.id<br>C I R E B O N - 4 5 6 1 1' !!}</p>
                </td>
            </tr>
        </table>
    </div>
    <div class="garis-kop"></div>

    <table style="width: 100%; margin-top: {{ $u['jarak'] }}px;">
        <tr>
            {{-- Turun satu baris agar "Nomor" sejajar dengan "Kepada Yth," seperti SK asli --}}
            <td style="width: 55%; vertical-align: top; padding-top: 14pt;">
                <table>
                    <tr>
                        <td style="width: 19.5mm;">Nomor</td>
                        <td style="width: 2.5mm;">:</td>
                        <td>{{ $riwayat->nomor_sk_baru }}</td>
                    </tr>
                    <tr>
                        <td>Lampiran</td>
                        <td>:</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>Perihal</td>
                        <td>:</td>
                        <td><span class="garis-bawah">Kenaikan Gaji Berkala</span></td>
                    </tr>
                </table>
            </td>
            <td style="width: 45%; vertical-align: top;">
                <div>Cirebon, {{ \Carbon\Carbon::parse($riwayat->tanggal_ditetapkan)->translatedFormat('d F Y') }}</div>
                <div>Kepada Yth,</div>
                <div>Kepala Badan Pengelolaan Keuangan dan Aset Daerah Provinsi Jawa Barat</div>
                <div>di-</div>
                <div style="margin-top: 3px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="garis-bawah">B a n d u n g</span></div>
            </td>
        </tr>
    </table>

    {{-- Isi surat menjorok ke dalam dibanding blok Nomor/Lampiran/Perihal, seperti SK asli --}}
    <div style="padding-left: 12mm;">
    <div class="text-justify mt-2 mb-2" style="margin-top: {{ $u['jarak'] + 5 }}px;">
        {{-- "syarat-syarat" tidak boleh terpotong di tanda hubung --}}
        Dengan ini kami beritahukan, bahwa berhubung dengan telah dipenuhinya masa kerja dan <span style="white-space: nowrap;">syarat-syarat</span> lainnya kepada :
    </div>

    <table class="content-table">
        <tr>
            <td class="number-col">1.</td>
            <td class="label-col">Nama</td>
            <td class="colon-col">:</td>
            <td class="value-col"><strong>{{ $pegawai->nama_lengkap }}</strong></td>
        </tr>
        <tr>
            <td class="number-col">2.</td>
            <td class="label-col">Nomor Induk Pegawai</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $pegawai->nip }}</td>
        </tr>
        <tr>
            <td class="number-col">3.</td>
            <td class="label-col">Pangkat/Golongan</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $pegawai->pangkat }}, {{ $pegawai->golongan }}</td>
        </tr>
        <tr>
            <td class="number-col">4.</td>
            <td class="label-col">Jabatan</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $pegawai->jabatan }}</td>
        </tr>
        <tr>
            <td class="number-col">5.</td>
            <td class="label-col">Kantor tempat kerja</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $pegawai->kantor_tempat_kerja }}</td>
        </tr>
        <tr>
            <td class="number-col">6.</td>
            <td class="label-col">Gaji Pokok Lama</td>
            <td class="colon-col">:</td>
            <td class="value-col">Rp {{ number_format($riwayat->gaji_pokok_lama, 0, ',', '.') }},-</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="3">(Atas dasar SK. Terakhir tentang gaji/pangkat yang ditetapkan)</td>
        </tr>
        <tr>
            <td></td>
            <td class="label-col" style="padding-left: 15px;">a. Oleh Pejabat</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $pejabatTerdahulu->nama_jabatan ?? '-' }}</td>
        </tr>
        <tr>
            <td></td>
            <td class="label-col" style="padding-left: 15px;">b. Tanggal/Nomor</td>
            <td class="colon-col">:</td>
            <td class="value-col">Tgl. {{ $pegawai->tanggal_sk_terakhir ? \Carbon\Carbon::parse($pegawai->tanggal_sk_terakhir)->format('d-m-Y') : '-' }}/No. {{ $pegawai->nomor_sk_terakhir }}</td>
        </tr>
        <tr>
            <td></td>
            <td class="label-col" style="padding-left: 15px;">c. Tanggal mulai berlakunya<br>&nbsp;&nbsp;&nbsp;&nbsp;gaji tersebut</td>
            <td class="colon-col"><br>:</td>
            <td class="value-col"><br>{{ \Carbon\Carbon::parse($riwayat->tmt_baru)->subYears(2)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td></td>
            <td class="label-col" style="padding-left: 15px;">d. Masa kerja golongan<br>&nbsp;&nbsp;&nbsp;&nbsp;pada tanggal tersebut</td>
            <td class="colon-col"><br>:</td>
            <td class="value-col"><br>{{ max(0, $riwayat->masa_kerja_tahun_baru - 2) }} Tahun {{ sprintf('%02d', $riwayat->masa_kerja_bulan_baru) }} bulan</td>
        </tr>
        <tr>
            <td colspan="4" class="font-bold">DIBERIKAN GAJI BERKALA HINGGA MEMPEROLEH:</td>
        </tr>
        <tr>
            <td class="number-col">7.</td>
            <td class="label-col">Gaji Pokok Baru</td>
            <td class="colon-col">:</td>
            <td class="value-col"><strong>Rp {{ number_format($riwayat->gaji_pokok_baru, 0, ',', '.') }},-</strong></td>
        </tr>
        <tr>
            <td class="number-col">8.</td>
            <td class="label-col">Berdasarkan masa kerja</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $riwayat->masa_kerja_tahun_baru }} Tahun {{ sprintf('%02d', $riwayat->masa_kerja_bulan_baru) }} bulan</td>
        </tr>
        <tr>
            <td class="number-col">9.</td>
            <td class="label-col">Dalam Golongan</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $pegawai->golongan }}</td>
        </tr>
        <tr>
            <td class="number-col">10.</td>
            <td class="label-col">Mulai Tanggal</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ \Carbon\Carbon::parse($riwayat->tmt_baru)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td class="number-col">11.</td>
            <td class="label-col">Kenaikan gaji berkala Y.a.d</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ \Carbon\Carbon::parse($riwayat->tmt_yad)->format('d-m-Y') }}</td>
        </tr>
    </table>

    <div class="text-justify mt-2" style="margin-top: {{ $u['jarak'] }}px;">
        Diharap agar sesuai dengan Peraturan Pemerintah RI. No. 5 Tahun 2024 kepada Pegawai tersebut dapat dibayarkan penghasilannya berdasarkan gaji pokok yang baru.
    </div>
    </div>

<div class="signature-box" style="float: right; width: 450px; margin-top: {{ $u['ttd'] }}px; font-family: {!! $hurufIsi !!};">
        <div style="text-align: center; font-size: 12pt; line-height: 1.2; margin-bottom: {{ $u['jarak'] }}px;">
            DIREKTUR RUMAH SAKIT DAERAH SIDAWANGI<br>
            PROVINSI JAWA BARAT.
        </div>
        
        <div class="signature-inner" style="border: 2px solid black; border-radius: 25px; padding: {{ $u['kotak'] }}px; font-family: Arial, sans-serif;">
            <table style="width: 100%; border-collapse: collapse; border: none;">
                <tr>
                    <td style="width: 90px; text-align: center; vertical-align: middle; padding-right: 15px;">
                        <img src="{{ public_path('images/tte_logo.png') }}" alt="TTE Logo" style="width: 85px;">
                    </td>
                    
                    <td style="vertical-align: middle; font-size: 10pt; line-height: 1.3;">
                        Ditandatangani secara elektronik oleh:<br>
                        DIREKTUR RUMAH SAKIT DAERAH SIDAWANGI<br>
                        PROVINSI JAWA BARAT,<br><br><br>
                        {{ rtrim($instansi?->nama_direktur ?? 'dr. YOGA PRAMADIA', '.') }}.<br>
                        {{ $instansi?->pangkat_direktur ?? 'Pembina Tk.I' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    <div style="clear: both;"></div>
</body>
</html>
