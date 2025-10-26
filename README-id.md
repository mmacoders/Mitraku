# SI-Huyula (Elektronik Perjanjian Kerja Sama)

Aplikasi Laravel + Vue.js untuk mengelola perjanjian kerja sama (PKS) dengan kontrol akses berbasis peran untuk pengguna Mitra dan Admin.

## Fitur

- **Autentikasi Pengguna**: Pendaftaran dan login untuk pengguna Mitra, pengguna Admin dibuat melalui seeder
- **Dasbor Berbasis Peran**: Dasbor terpisah untuk pengguna Mitra dan Admin
- **Manajemen PKS**: 
  - Mitra dapat mengajukan permintaan PKS baru dengan dokumen
  - Admin dapat meninjau, menyetujui, menolak, atau meminta revisi
  - Pelacakan status (Proses, Revisi, Ditolak, Disetujui)
  - Timeline/riwayat perubahan status
- **Manajemen Dokumen**: Unggah dan penyimpanan dokumen PKS
- **Manajemen Rapat**: Jadwalkan rapat terkait pengajuan PKS
- **Sistem Notifikasi**: Notifikasi real-time untuk semua peristiwa alur kerja
- **Tanda Tangan Digital**: Tangkap dan simpan tanda tangan digital untuk dokumen yang disetujui
- **Pelacakan Dokumen**: Visualisasi timeline menyeluruh dari riwayat dokumen
- **UI Responsif**: Desain mobile-first dengan TailwindCSS
- **Mode Gelap**: Beralih antara tema terang dan gelap

## Teknologi

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue.js 3 + Vite + TailwindCSS
- **Database**: MySQL
- **Autentikasi**: Laravel Breeze

## Instalasi

1. Klon repositori:
   ```bash
   git clone <url-repositori>
   cd e-pks
   ```

2. Instal dependensi PHP:
   ```bash
   composer install
   ```

3. Instal dependensi JavaScript:
   ```bash
   npm install
   ```

4. Buat database MySQL dengan nama `e_pks`

5. Konfigurasi file `.env` Anda dengan kredensial database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=e_pks
   DB_USERNAME=username_anda
   DB_PASSWORD=password_anda
   ```

6. Hasilkan kunci aplikasi:
   ```bash
   php artisan key:generate
   ```

7. Jalankan migrasi database:
   ```bash
   php artisan migrate
   ```

8. Isi database (membuat pengguna admin dan data sampel):
   ```bash
   php artisan db:seed
   ```

9. Bangun aset frontend:
   ```bash
   npm run build
   ```

10. Mulai server pengembangan:
    ```bash
    php artisan serve
    ```

11. Di terminal lain, mulai server pengembangan Vite:
    ```bash
    npm run dev
    ```

## Pengguna Default

- **Admin**: 
  - Email: admin@epks.local
  - Password: password

- **Mitra**: 
  - Email: test@example.com
  - Password: password

## Pengembangan

Untuk menjalankan tes:
```bash
php artisan test
```

Untuk menjalankan PHP CS Fixer:
```bash
./vendor/bin/pint
```

## Peningkatan Terbaru

### Implementasi Alur Kerja PKS
- Peningkatan pendaftaran pengguna dengan pemilihan peran (Mitra/Admin)
- Sistem notifikasi komprehensif untuk semua peristiwa alur kerja
- Kemampuan penangkapan dan penyimpanan tanda tangan digital
- Visualisasi timeline dokumen untuk pelacakan riwayat lengkap
- Penjadwalan rapat terintegrasi dengan pengajuan PKS

### Penyelarasan Dasbor
- Dasbor admin didesain ulang agar sesuai dengan struktur dasbor Mitra
- Ditambahkan spanduk selamat datang dengan pesan yang konsisten
- Diimplementasikan kartu KPI untuk metrik kunci
- Ditambahkan bagian grafik dengan grafik donat dan garis
- Dimasukkan bagian filter dengan pencarian, status, dan penyaringan tanggal
- Bagian aktivitas terkini untuk ikhtisar cepat

## Lisensi

Proyek ini merupakan perangkat lunak open-source yang dilisensikan di bawah [lisensi MIT](https://opensource.org/licenses/MIT).