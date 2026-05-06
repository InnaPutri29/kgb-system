<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SK KGB - {{ $pegawai->nama }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.5; margin: 2cm; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .underline { text-decoration: underline; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mt-4 { margin-top: 1rem; }
        .mt-8 { margin-top: 2rem; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 2px 4px; }
        .label { width: 40%; }
        .colon { width: 2%; }
        .value { width: 58%; }
        
        .header { text-align: center; border-bottom: 3px solid black; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { font-size: 14pt; margin: 0; }
        .header h2 { font-size: 12pt; margin: 0; font-weight: normal; }
        .header p { font-size: 10pt; margin: 0; }
        
        .signature-area { width: 300px; float: right; margin-top: 40px; text-align: center; }
        .clear { clear: both; }
    </style>
</head>
<body>



    <div class="header">
        @if($instansi && $instansi->logo)
            <!-- Implementasi logo opsional jika perlu, sementara fokus ke teks -->
        @endif
        <h1>{{ $instansi->nama_instansi ?? 'NAMA INSTANSI BELUM DISET' }}</h1>
        <p>{{ $instansi->alamat ?? 'Alamat instansi belum diset' }}</p>
    </div>

    <div class="text-center mb-6">
        <div class="font-bold uppercase underline mb-2">SURAT KEPUTUSAN KENAIKAN GAJI BERKALA</div>
        <div>Nomor: {{ $riwayat->nomor_sk_baru }}</div>
    </div>

    <p style="text-align: justify; margin-bottom: 15px;">
        Berdasarkan Peraturan Pemerintah Nomor 15 Tahun 2019 tentang Perubahan Kedelapan Belas Atas Peraturan Pemerintah Nomor 7 Tahun 1977 tentang Peraturan Gaji Pegawai Negeri Sipil, dengan ini diberikan Kenaikan Gaji Berkala kepada:
    </p>

    <table class="mb-4">
        <tr>
            <td class="label">1. Nama</td>
            <td class="colon">:</td>
            <td class="value font-bold">{{ $pegawai->nama }}</td>
        </tr>
        <tr>
            <td class="label">2. NIP</td>
            <td class="colon">:</td>
            <td class="value">{{ $pegawai->nip }}</td>
        </tr>
        <tr>
            <td class="label">3. Pangkat / Gol. Ruang</td>
            <td class="colon">:</td>
            <td class="value">{{ $pegawai->pangkat_golongan }}</td>
        </tr>
        <tr>
            <td class="label">4. Jabatan</td>
            <td class="colon">:</td>
            <td class="value">{{ $pegawai->jabatan }}</td>
        </tr>
        <tr>
            <td class="label">5. Unit Kerja</td>
            <td class="colon">:</td>
            <td class="value">{{ $pegawai->unit_kerja }}</td>
        </tr>
    </table>

    <p style="margin-bottom: 10px;">(Atas dasar SK. Terakhir tentang gaji/pangkat yang ditetapkan):</p>
    <table class="mb-4">
        <tr>
            <td class="label">a. Oleh Pejabat</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ $pejabat->nama_jabatan }}</strong></td>
        </tr>
        <tr>
            <td class="label">b. Nomor SK</td>
            <td class="colon">:</td>
            <td class="value">{{ $riwayat->nomor_sk_lama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">c. Tanggal SK</td>
            <td class="colon">:</td>
            <td class="value">{{ $riwayat->tanggal_sk_lama ? \Carbon\Carbon::parse($riwayat->tanggal_sk_lama)->translatedFormat('d F Y') : '-' }}</td>
        </tr>
    </table>

    <p style="margin-bottom: 10px;">Diberikan Gaji Pokok Baru sebesar:</p>
    <table class="mb-4">
        <tr>
            <td class="label"><strong>Gaji Pokok Baru</strong></td>
            <td class="colon"><strong>:</strong></td>
            <td class="value font-bold">Rp {{ number_format($riwayat->gaji_pokok_baru, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Terhitung Mulai Tanggal (TMT)</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ \Carbon\Carbon::parse($riwayat->tmt_kgb_baru)->translatedFormat('d F Y') }}</strong></td>
        </tr>
        <tr>
            <td class="label">Masa Kerja Golongan</td>
            <td class="colon">:</td>
            <td class="value">{{ $riwayat->masa_kerja_tahun_baru }} Tahun {{ $riwayat->masa_kerja_bulan_baru }} Bulan</td>
        </tr>
    </table>

    <p style="text-align: justify;">
        Kenaikan Gaji Berkala berikutnya akan diberikan pada tanggal {{ \Carbon\Carbon::parse($riwayat->tmt_kgb_baru)->addYears(2)->translatedFormat('d F Y') }}, apabila memenuhi syarat yang ditentukan.
    </p>
    <p>Surat Keputusan ini diberikan kepada yang bersangkutan untuk diketahui dan dipergunakan sebagaimana mestinya.</p>

    <div class="signature-area">
        <p class="mb-2">Ditetapkan di: Cirebon</p>
        <p style="margin-bottom: 80px;">Pada tanggal: {{ \Carbon\Carbon::parse($riwayat->tanggal_sk_baru)->translatedFormat('d F Y') }}</p>
        
        <p class="font-bold underline" style="margin-bottom: 0;">{{ $instansi->nama_direktur }}</p>
        <p style="margin-top: 0;">{{ $instansi->pangkat_direktur }}</p>
        <p style="margin-top: 0;">NIP. {{ $instansi->nip_direktur }}</p>
    </div>
    
    <div class="clear"></div>

</body>
</html>
