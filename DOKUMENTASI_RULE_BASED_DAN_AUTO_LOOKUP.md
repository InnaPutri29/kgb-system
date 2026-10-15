# Dokumentasi Rule-Based dan Auto-Lookup pada Sistem KGB

Dokumen ini menjelaskan tata letak, fungsi, dan mekanisme dari komponen **Rule-Based Logic** (aturan bisnis/kualifikasi) dan **Auto-Lookup Logic** (pencarian & kalkulasi data otomatis) yang diimplementasikan pada sistem Kenaikan Gaji Berkala (KGB).

---

## 1. Rule-Based Logic (Aturan Bisnis & Kualifikasi KGB)

Komponen *Rule-Based* bertanggung jawab untuk memverifikasi apakah seorang pegawai berhak mendapatkan Kenaikan Gaji Berkala, memfilter daftar jatuh tempo, serta menghitung periode TMT dan masa kerja baru secara otomatis.

### A. Lokasi Utama Kode
📄 [`app/Http/Controllers/Admin/KgbController.php`](file:///d:/Downloads/PROYEK%20MANDIRI/kgb-system/app/Http/Controllers/Admin/KgbController.php)

### B. Aturan-Aturan Bisnis (Rules)

#### 1. Rule Filter Nominatif Jatuh Tempo (`nominatif`)
* **Metode**: `KgbController::nominatif()`
* **Deskripsi**: Menyeleksi secara otomatis pegawai yang akan jatuh tempo KGB dalam kurun waktu **60 hari ke depan** dari hari ini.
* **Kriteria Query**:
  ```php
  Pegawai::whereNotNull('tmt_gaji_terakhir')
      ->where('is_sedang_hukuman_disiplin', false)
      ->whereRaw('DATE_ADD(tmt_gaji_terakhir, INTERVAL 2 YEAR) <= ?', [$batasTanggal])
      ->whereDoesntHave('riwayatKgb', function ($q) use ($today) {
          $q->whereYear('riwayat_kgb.tmt_baru', $today->year)
            ->whereMonth('riwayat_kgb.tmt_baru', $today->month);
      });
  ```

#### 2. Rule Validasi Kelayakan Syarat KGB (`validasiKgb`)
* **Metode**: `KgbController::validasiKgb(Pegawai $pegawai)`
* **Aturan Hukuman Disiplin**:
  - Pegawai tidak boleh sedang dalam status hukuman disiplin (`is_sedang_hukuman_disiplin == false`).
* **Aturan Evaluasi Kinerja (PKP / SKP)**:
  - Pegawai wajib memiliki data evaluasi PKP **minimal 2 tahun terakhir**.
  - Predikat nilai PKP pada 2 tahun terakhir tidak boleh tergolong tidak lulus (`'Cukup'`, `'Kurang'`, atau `'Sangat Kurang'`).

#### 3. Rule Kalkulasi Periode TMT & Masa Kerja Baru (`proses` & `getDataForModal`)
* **Masa Kerja Baru**: Masa Kerja Tahun ditambah 2 tahun (`$pegawai->masa_kerja_tahun + 2`).
* **TMT KGB Baru**: TMT gaji terakhir ditambah 2 tahun (`$tmtGajiTerakhir->addYears(2)`).
* **TMT YAD (Yang Akan Datang)**: TMT baru ditambah 2 tahun (`$tmtBaru->addYears(2)`).

---

## 2. Auto-Lookup Logic (Pencarian Data Otomatis)

Komponen *Auto-Lookup* menangani pengambilan besaran gaji pokok baru secara otomatis dari tabel master berdasarkan kombinasi golongan dan masa kerja, serta menyediakan data siap pakai untuk antarmuka pengguna.

### A. Backend Auto-Lookup (Lookup Gaji & Provider Data Modal)

#### 1. Lookup Nominal Gaji Pokok Baru (`lookupGaji`)
📄 [`app/Http/Controllers/Admin/KgbController.php`](file:///d:/Downloads/PROYEK%20MANDIRI/kgb-system/app/Http/Controllers/Admin/KgbController.php)
* **Metode**: `KgbController::lookupGaji(?string $golongan, int $masaKerjaTahun)`
* **Mekanisme**: Melakukan pencarian otomatis ke tabel master [`MasterGaji`](file:///d:/Downloads/PROYEK%20MANDIRI/kgb-system/app/Models/MasterGaji.php) berdasarkan `golongan` pegawai dan `masa_kerja` terkini.
* **Query Lookup**:
  ```php
  $row = MasterGaji::where('golongan', $golongan)
      ->where('masa_kerja', '<=', $masaKerjaTahun)
      ->orderBy('masa_kerja', 'desc')
      ->first();
  return $row ? (int) $row->nominal_gaji : 0;
  ```

#### 2. Endpoint Data Provider Modal AJAX (`getDataForModal`)
📄 [`app/Http/Controllers/Admin/KgbController.php`](file:///d:/Downloads/PROYEK%20MANDIRI/kgb-system/app/Http/Controllers/Admin/KgbController.php)
* **Route**: `GET /admin/kgb/{pegawai}/data-modal` (`admin.kgb.data-modal`)
* **Fungsi**: Dipanggil oleh frontend untuk menyajikan data secara otomatis tanpa *reload* halaman, meliputi:
  - Profil pegawai & TMT gaji terakhir.
  - TMT KGB Baru & Masa Kerja Baru hasil kalkulasi.
  - Nominal Gaji Pokok Baru hasil *auto-lookup*.
  - Daftar Pejabat Penetap.
  - Status hasil validasi *rule-based* (Lolos / Tidak Lolos beserta alasannya).

#### 3. Tabel Reference Master Gaji
📄 [`app/Models/MasterGaji.php`](file:///d:/Downloads/PROYEK%20MANDIRI/kgb-system/app/Models/MasterGaji.php)  
📄 [`app/Http/Controllers/Admin/MasterGajiController.php`](file:///d:/Downloads/PROYEK%20MANDIRI/kgb-system/app/Http/Controllers/Admin/MasterGajiController.php)
* Berisi data acuan skala gaji berdasarkan skema Golongan dan Masa Kerja (MKG).

---

### B. Frontend Auto-Lookup & Auto-Fill (UI Interaktif)

📄 [`resources/views/admin/kgb/nominatif.blade.php`](file:///d:/Downloads/PROYEK%20MANDIRI/kgb-system/resources/views/admin/kgb/nominatif.blade.php)

* **Live Search Pejabat Penetap**: Filter pencarian instan nama/jabatan pejabat pada dropdown modal proses KGB.
* **Smart Hint / Auto-Fill SK Terakhir**: Fitur tombol *"Gunakan Data Ini"* yang mengisikan Tanggal SK Terakhir dan Nomor SK Terakhir secara otomatis ke dalam form input modal tanpa perlu pengetikan manual.

---

## 3. Rangkuman Peta Berkas (File Map)

| Fungsi / Logika | Nama File | Tipe / Layer |
| :--- | :--- | :--- |
| **Rule-Based Validasi & Nominatif** | [`app/Http/Controllers/Admin/KgbController.php`](file:///d:/Downloads/PROYEK%20MANDIRI/kgb-system/app/Http/Controllers/Admin/KgbController.php) | Backend Controller |
| **Auto-Lookup Gaji Pokok** | [`app/Http/Controllers/Admin/KgbController.php`](file:///d:/Downloads/PROYEK%20MANDIRI/kgb-system/app/Http/Controllers/Admin/KgbController.php) | Backend Controller |
| **Skema Tabel Master Gaji** | [`app/Models/MasterGaji.php`](file:///d:/Downloads/PROYEK%20MANDIRI/kgb-system/app/Models/MasterGaji.php) | Eloquent Model |
| **Manajemen Master Gaji** | [`app/Http/Controllers/Admin/MasterGajiController.php`](file:///d:/Downloads/PROYEK%20MANDIRI/kgb-system/app/Http/Controllers/Admin/MasterGajiController.php) | Backend Controller |
| **Auto-Fill & Modal UI** | [`resources/views/admin/kgb/nominatif.blade.php`](file:///d:/Downloads/PROYEK%20MANDIRI/kgb-system/resources/views/admin/kgb/nominatif.blade.php) | Blade & Alpine.js View |
| **Route Endpoints** | [`routes/web.php`](file:///d:/Downloads/PROYEK%20MANDIRI/kgb-system/routes/web.php) | Route Mapping |
