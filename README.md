# Sistem Informasi Kenaikan Gaji Berkala (KGB)

Sistem Informasi Kenaikan Gaji Berkala (KGB) adalah aplikasi berbasis web yang dirancang khusus untuk mempermudah instansi pemerintahan dalam memantau, memproses, dan menerbitkan Surat Keputusan (SK) Kenaikan Gaji Berkala untuk Pegawai Negeri Sipil (PNS).

## 🚀 Fitur Utama

Aplikasi ini dibagi menjadi 2 hak akses utama: **Admin (Kepegawaian)** dan **Pegawai**.

### 👨‍💼 Fitur Admin
*   **Dashboard Utama**: Statistik jumlah pegawai aktif, jumlah SK yang telah terbit, dan pengingat (reminder) jadwal KGB yang akan segera jatuh tempo.
*   **Manajemen Pegawai**: Tambah, edit, hapus, serta impor data pegawai secara massal.
*   **Manajemen Master Pejabat**: Mengelola data pejabat struktural yang berwenang untuk menandatangani SK KGB.
*   **Proses Nominatif KGB**: 
    *   Sistem secara otomatis mendeteksi pegawai yang sudah mendekati jatuh tempo KGB (2 tahun dari TMT Gaji Terakhir).
    *   Filter kelayakan berdasarkan evaluasi SKP tahunan (minimal Berpredikat "Baik") dan status hukuman disiplin.
*   **Cetak SK KGB**: 
    *   Pembuatan otomatis SK KGB ke dalam format PDF yang siap diunduh atau dicetak.
    *   Menyimpan arsip SK KGB yang telah diterbitkan ke dalam riwayat pegawai.
*   **Evaluasi SKP**: Rekapitulasi dokumen Sasaran Kinerja Pegawai tahunan untuk syarat pengajuan KGB.

### 👤 Fitur Pegawai
*   **Dashboard Pegawai**: Tampilan profil ringkas beserta notifikasi/status kelayakan KGB saat ini.
*   **Estimasi Jadwal KGB**: Menampilkan *countdown* sisa hari menuju jatuh tempo KGB berikutnya.
*   **Riwayat SK KGB**: Pegawai dapat melihat daftar riwayat KGB terdahulu dan mengunduh file PDF SK mereka masing-masing secara mandiri.
*   **Evaluasi SKP**: Melacak status predikat SKP yang telah dinilai oleh atasan sebagai syarat kelayakan KGB.
*   **Pengaturan Profil**: Mengelola data akun, alamat email, dan pembaruan kata sandi.

## 🛠️ Stack Teknologi

Aplikasi ini dibangun menggunakan arsitektur modern untuk memastikan kecepatan, keamanan, dan kemudahan dalam pengembangan lebih lanjut:
*   **Framework PHP**: Laravel (v10 / v11)
*   **Database**: MySQL
*   **Frontend**: Laravel Blade, Tailwind CSS, Alpine.js
*   **PDF Generator**: DomPDF (atau sejenisnya) untuk rendering cetak SK.

## ⚙️ Panduan Instalasi (Development)

Bagi pengembang yang ingin menjalankan proyek ini secara lokal, ikuti langkah-langkah berikut:

1. **Kloning Repositori**
   ```bash
   git clone <url-repositori-anda>
   cd kgb-system
   ```

2. **Instalasi Dependensi PHP (Composer)**
   ```bash
   composer install
   ```

3. **Instalasi Dependensi Frontend (NPM)**
   ```bash
   npm install
   npm run build
   ```

4. **Konfigurasi Environment**
   * Salin file `.env.example` dan ubah namanya menjadi `.env`.
   * Buka file `.env` dan atur konfigurasi *database* lokal Anda.
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=kgb_system
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Migrasi dan Seeding Database**
   Menjalankan migrasi *database* beserta akun *default* untuk demo.
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Storage Link**
   Menautkan direktori `storage` agar file (seperti PDF atau Foto) dapat diakses publik.
   ```bash
   php artisan storage:link
   ```

8. **Menjalankan Server**
   Jalankan *server* Artisan dan Vite secara paralel di 2 terminal berbeda:
   ```bash
   php artisan serve
   ```
   ```bash
   npm run dev
   ```
   
Aplikasi sekarang dapat diakses melalui browser di alamat: `http://localhost:8000`.

---
*Dibuat untuk mempermudah digitalisasi layanan kepegawaian pemerintahan.*
