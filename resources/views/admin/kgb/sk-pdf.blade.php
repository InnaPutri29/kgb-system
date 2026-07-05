<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SK KGB - {{ $pegawai->nama_lengkap }}</title>
    <style>
        @page {
            size: 215.9mm 330mm;
            margin: 2cm 2cm 2cm 3cm;
        }
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 12pt; 
            line-height: 1.15; 
        }
        .header {
            width: 100%;
            margin-bottom: 5px;
            border-bottom: 4px double black;
            padding-bottom: 10px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .header td {
            vertical-align: middle;
        }
        .header .logo {
            width: 80px;
            text-align: left;
        }
        .header .text-center {
            text-align: center;
        }
        .header h1 {
            font-size: 14pt;
            margin: 0;
            font-weight: normal;
        }
        .header h2 {
            font-size: 16pt;
            margin: 0;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .header p {
            font-size: 11pt;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12pt;
        }
        .align-top { vertical-align: top; }
        .text-justify { text-align: justify; }
        .mt-2 { margin-top: 10px; }
        .mb-2 { margin-bottom: 10px; }
        
        .content-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .number-col {
            width: 5%;
            text-align: right;
            padding-right: 10px;
        }
        .label-col {
            width: 35%;
        }
        .colon-col {
            width: 2%;
        }
        .value-col {
            width: 58%;
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
                        <img src="{{ public_path('storage/' . $instansi->logo) }}" alt="Logo" style="width: 80px; max-height: 100px;">
                    @else
                        <!-- Logo Placeholder jika tidak ada logo -->
                    @endif
                </td>
                <td class="text-center">
                    <h1>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h1>
                    <h2>D I N A S K E S E H A T A N</h2>
                    <h2 style="font-size: 14pt; font-weight: bold;">{{ strtoupper($instansi->nama_instansi ?? 'RUMAH SAKIT DAERAH SIDAWANGI') }}</h2>
                    <p>{!! $instansi->alamat ? nl2br(e($instansi->alamat)) : 'Jalan Pangeran Kejaksan Sumber Telepon (0231) 8330707 <i>Fax</i> : (0231) 8330747<br><i>Website</i>: www.rsp.jabarprov.go.id <i>e-mail</i> : rsp@jabarprov.go.id<br>C I R E B O N - 4 5 6 1 1' !!}</p>
                </td>
            </tr>
        </table>
    </div>

    <table style="width: 100%; margin-top: 15px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <table>
                    <tr>
                        <td style="width: 65px;">Nomor</td>
                        <td style="width: 10px;">:</td>
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
                        <td><u>Kenaikan Gaji Berkala</u></td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div>Cirebon, {{ \Carbon\Carbon::parse($riwayat->tanggal_ditetapkan)->translatedFormat('d F Y') }}</div>
                <div>Kepada Yth,</div>
                <div>Kepala Badan Pengelolaan Keuangan dan Aset Daerah Provinsi Jawa Barat</div>
                <div>di-</div>
                <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>B a n d u n g</u></div>
            </td>
        </tr>
    </table>

    <div class="text-justify mt-2 mb-2" style="margin-top: 20px;">
        Dengan ini kami beritahukan, bahwa berhubung dengan telah dipenuhinya masa kerja dan syarat-syarat lainnya kepada :
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

    <div class="text-justify mt-2" style="margin-top: 15px;">
        Diharap agar sesuai dengan Peraturan Pemerintah RI. No. 5 Tahun 2024 kepada Pegawai tersebut dapat dibayarkan penghasilannya berdasarkan gaji pokok yang baru.
    </div>

<div class="signature-box" style="float: right; width: 450px; margin-top: 30px; font-family: 'Times New Roman', Times, serif;">
        <div style="text-align: center; font-size: 14pt; line-height: 1.2; margin-bottom: 15px;">
            DIREKTUR RUMAH SAKIT DAERAH SIDAWANGI<br>
            PROVINSI JAWA BARAT,
        </div>
        
        <div class="signature-inner" style="border: 2px solid black; border-radius: 25px; padding: 15px; font-family: Arial, sans-serif;">
            <table style="width: 100%; border-collapse: collapse; border: none;">
                <tr>
                    <td style="width: 90px; text-align: center; vertical-align: middle; padding-right: 15px;">
                        <img src="{{ public_path('images/tte_logo.png') }}" alt="TTE Logo" style="width: 85px;">
                    </td>
                    
                    <td style="vertical-align: middle; font-size: 11pt; line-height: 1.3;">
                        Ditandatangani secara elektronik oleh:<br>
                        DIREKTUR RUMAH SAKIT DAERAH<br>
                        SIDAWANGI PROVINSI JAWA BARAT,<br><br>
                        <strong>{{ $instansi->nama_direktur ?? 'dr. YOGA PRAMADIA' }}</strong><br>
                        {{ $instansi->pangkat_direktur ?? 'Pembina Tk.I' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    <div style="clear: both;"></div>
</body>
</html>
