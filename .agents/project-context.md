# Project Context

## Project Name

**SuperPosko** (superposko.web.id)

## Project Overview

SuperPosko adalah platform **SaaS (Software as a Service)** berbasis web yang dirancang sebagai **Modular Monolith** untuk mempermudah, merapikan, dan mendigitalisasi seluruh administrasi serta operasional kelompok KKN (Kuliah Kerja Nyata). 

Platform ini bertujuan untuk menghilangkan "drama" operasional internal kelompok KKN melalui sentralisasi data, dokumentasi terintegrasi, dan transparansi keuangan dalam satu pintu akses dengan manajemen hak akses berbasis peran (Role-Based Access Control / RBAC). Sebagai aplikasi SaaS, platform ini ditujukan untuk digunakan oleh kelompok KKN dari berbagai universitas di Indonesia.

Aplikasi ini dibangun menggunakan arsitektur **Single Page Application (SPA) Monolith** modern yang didukung oleh **Laravel 13**, **Inertia.js v2**, dan **Vue 3**.

---

# Main Goals

Tujuan utama dari sistem SuperPosko adalah:

* **SaaS Multitenancy / Multi-Kelompok:** Memungkinkan pendaftaran kelompok KKN dari berbagai universitas secara mandiri, dengan isolasi data yang aman per kelompok/posko.
* **Transparansi Keuangan:** Memastikan pencatatan kas masuk dan keluar terdokumentasi dengan baik beserta bukti nota digital untuk menghindari perselisihan internal.
* **Akuntabilitas Program Kerja:** Memfasilitasi pelaporan logbook harian kepada Dosen Pembimbing Lapangan (DPL) serta manajemen visual pengerjaan program kerja.
* **Sentralisasi Aset & Logistik:** Melacak stok logistik dan status barang pinjaman dari warga desa agar meminimalisir risiko kehilangan.
* **Pencegahan Drama Internal:** Mengatur jadwal piket harian, voting internal, dan direktori kontak penting desa secara demokratis dan terstruktur.

---

# Target Users & Roles

Sistem ini menggunakan Role-Based Access Control (RBAC) dengan tingkatan akses berikut:

### 1. Administrator Utama / SaaS Owner
* Mengelola paket langganan SaaS, database posko terdaftar, dan statistik pengguna platform secara keseluruhan.

### 2. Ketua Posko (Admin Posko)
* Mendaftarkan posko, mengundang anggota posko.
* Mengelola konfigurasi posko dan alokasi anggaran awal.
* Mengatur hak akses khusus untuk modul keuangan.

### 3. Bendahara Posko
* Memiliki hak khusus untuk mencatat, mengedit, dan memvalidasi arus kas masuk/keluar serta nota digital.
* Membuat laporan keuangan mingguan/bulanan.

### 4. Anggota Posko
* Melakukan input logbook harian pribadi.
* Mengelola papan Kanban program kerja (Proker).
* Meminjam/mengembalikan inventaris posko.
* Melakukan voting dan mengakses buku kontak desa.
* Melihat jadwal piket harian.

### 5. Dosen Pembimbing Lapangan (DPL) / Pihak Eksternal (Akses Khusus)
* Membaca logbook harian mahasiswa bimbingannya secara langsung.
* Memberikan evaluasi, feedback, dan persetujuan proker.

---

# Core Features (Modul Aplikasi)

Aplikasi dibangun secara modular untuk mempermudah ekspansi fitur:

### 1. E-Bendahara (Kas & Keuangan)
* Pencatatan kas masuk (iuran anggota, dana universitas, sponsorship) dan kas keluar.
* Upload bukti transaksi / nota digital langsung dari kamera HP atau file explorer.
* Grafik ringkasan sisa saldo kas posko secara real-time.

### 2. Digital Logbook & Proker
* **Logbook Harian:** Input aktivitas harian individu untuk kebutuhan pelaporan universitas.
* **Kanban Board Proker:** Papan status program kerja (`To Do`, `In Progress`, `Done`) beserta penanggung jawab, tenggat waktu, dan perkiraan biaya.

### 3. Manajemen Inventaris & Logistik
* Katalog logistik posko (sembako, konsumsi, perlengkapan medis).
* Katalog barang pinjaman dari warga (misal: tikar, sound system, cangkul) beserta nama pemilik dan status pengembalian.
* Peringatan otomatis jika stok barang habis/menipis.

### 4. Buku Kontak Desa (Direktori Digital)
* Daftar kontak warga penting (Kepala Desa, Ketua RT/RW, Tokoh Masyarakat, Puskesmas, Kepolisian Terdekat).
* Fitur klik untuk langsung menghubungi via WhatsApp atau Telepon.

### 5. Schedule Management (Piket & Agenda)
* Kalender agenda bersama (jadwal penyuluhan, rapat koordinasi).
* Penjadwalan piket harian posko (piket masak, kebersihan) secara dinamis.

### 6. Repository Proker (Lost & Found Dokumen)
* Penyimpanan file digital penting seperti proposal proker, surat izin, notulensi rapat, dan form feedback warga per proker agar tidak hilang saat laptop anggota rusak.

### 7. Voting & Aspirasi Internal
* Polling jajak pendapat internal (misal: penentuan menu makan malam, perubahan jadwal rapat).
* Hasil voting transparan dan real-time untuk menghindari konflik pengambilan keputusan.

---

# Technology Stack

Platform dibangun menggunakan stack teknologi modern berkinerja tinggi sesuai dengan `GEMINI.md`:

* **Framework PHP:** Laravel 13 (PHP 8.4)
* **Frontend Bridge:** Inertia.js v2 (Inertia Vue v2)
* **Library UI:** Vue 3, Shadcn Vue / Reka UI
* **Engine CSS:** Tailwind CSS v4
* **Database:** MySQL
* **Tools Pembantu:** Wayfinder (TypeScript routes generator), Pint (PHP code formatter), Vite (Bundler & HMR)

---

# Application & Database Architecture

Aplikasi ini menggunakan konsep **Modular Monolith** dengan pendekatan multitenancy:

```
                  ┌──────────────────────────────┐
                  │       Client / Browser       │
                  │   (Vue 3 SPA via Inertia.js) │
                  └──────────────┬───────────────┘
                                 │ HTTP / JSON
                                 ▼
                  ┌──────────────────────────────┐
                  │    Laravel 13 Application    │
                  │   ├─ Controllers             │
                  │   ├─ Inertia Pages           │
                  │   └─ Modular Logic Services  │
                  └──────────────┬───────────────┘
                                 │ Eloquent ORM
                                 ▼
                  ┌──────────────────────────────┐
                  │        MySQL Database        │
                  │ (Tenant-scoped by posko_id)  │
                  └──────────────────────────────┘
```

### Deployment & Docker Environment

Aplikasi ini dideploy dan dijalankan menggunakan **Docker Compose** dengan kontainer berikut:
* **`superposko-app`** (PHP 8.4 + Node.js 24 + Composer): Tempat aplikasi Laravel dan build tools berjalan (di-mount ke `/var/www/html`).
* **`superposko-nginx`**: Web server Alpine Nginx yang bertindak sebagai entrypoint HTTP port `9091`.
* **`superposko-db`** (MySQL 8.4): Server database utama port `3306`.
* **`superposko-pma`** (phpMyAdmin): Tool manajemen database berbasis web port `9093`.

> [!NOTE]
> Perintah Artisan, Composer, npm, dan Git di server harus dieksekusi di dalam kontainer `superposko-app` menggunakan `docker exec -it superposko-app <perintah>` sebagai user `www` (atau user `root` jika memerlukan hak akses khusus seperti `chown`/`chmod`).

### Strategi Multitenancy
* Menggunakan pendekatan **Single Database** dengan pemisahan data berbasis `posko_id` (atau tenant ID) di setiap tabel transaksional untuk memastikan performa efisien, migrasi database yang mudah, dan hosting yang hemat biaya.

---

# Design & UX Principles

* **Rich & Modern Aesthetics:** Tampilan antarmuka yang bersih, premium, menggunakan font modern (seperti *Instrument Sans*), dan palet warna yang harmonis untuk memberikan kesan profesional.
* **Mobile First Experience:** Karena anggota KKN mayoritas mengakses sistem dari lapangan menggunakan *smartphone*, semua modul dirancang sangat responsif dan mudah digunakan di perangkat mobile.
* **Micro-Animations & Clean States:** Transisi yang mulus saat perpindahan halaman dengan Inertia, visualisasi loader yang informatif, serta loading skeleton pada deferred props.
* **No Drama UX:** Tampilan informasi kas, piket, dan proker harus sejelas mungkin untuk menghindari salah paham antar anggota posko.
