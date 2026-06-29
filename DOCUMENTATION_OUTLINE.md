# Struktur Menu & Outline Dokumentasi SuperPosko

File ini merinci pemetaan rute, fitur, peran, dan modul yang ada di platform **SuperPosko** dalam format outline menu dokumentasi yang ramah pengguna.

---

## 🧭 NAVIGASI UTAMA (SIDEBAR DOKUMENTASI)

### GETTING STARTED
*   **Pengenalan SuperPosko** (Apa itu SuperPosko, visi keterbukaan informasi KKN)
*   **Panduan Memulai (Quick Start)** (Pendaftaran akun, alur pembuatan posko baru)
*   **Paket Langganan Posko** (Informasi biaya lisensi posko standar)
*   **Peran & Hak Akses (User Roles)** (Memahami perbedaan Guest/User Biasa, Host/Pemilik Posko, dan Member/Anggota)
*   **Ketentuan Layanan & Pelanggaran** (Aturan penggunaan, moderasi konten, dan sanksi blokir)

### FITUR UTAMA POSKO
*   **Dashboard** (Rangkuman statistik aktivitas posko secara real-time)
*   **Kas & Keuangan** (Pencatatan kas masuk/keluar secara transparan)
*   **Logbook & Proker** (Manajemen Program Kerja kelompok & Log harian anggota)
*   **Inventaris** (Manajemen barang sewaan atau barang inventaris tetap posko)
*   **Logistik** (Stok bahan habis pakai & dapur serta alur keluar barang)
*   **Barang Pribadi** (Checklist bawaan pribadi dari rumah agar tidak tertinggal)
*   **Piket & Agenda** (Jadwal piket harian dan kalender kegiatan posko)
*   **Anggota** (Undang anggota kelompok & penugasan jabatan posko)
*   **Log Aktivitas** (Riwayat aktivitas tindakan anggota posko)
*   **Buku Kontak** (Daftar kontak penting tingkat desa, kecamatan, dll.)
*   **Repository Proker** (Pusat berkas proker, batas ukuran 20MB, disimpan di server aplikasi)
*   **Voting & Aspirasi** (Poling suara & kotak saran/keluhan anonim posko)
*   **Dokumentasi** (Galeri foto & video, batas total 20GB, disimpan di server Immich)

### PENGATURAN & UTILITAS PENGGUNA
*   **Profil & Keamanan** (Ubah nama, ganti email dengan verifikasi OTP, & password)

### LAYANAN BANTUAN
*   **Mengajukan Keluhan** (Cara menggunakan bubble Layanan Bantuan untuk kendala sistem)

---

## 📖 DETAIL KONTEN DOKUMEN (PENJELASAN SISTEM & FITUR)

### 1. GETTING STARTED

#### 📄 Pengenalan SuperPosko
*   **Deskripsi**: SuperPosko adalah SaaS Platform kolaborasi digital khusus kelompok KKN (Kuliah Kerja Nyata) berbasis web untuk menunjang keterbukaan informasi keuangan, pembagian tugas harian, serta kerapian administrasi posko selama masa pengabdian.
*   **Tujuan Utama**: 
    *   Mencegah konflik internal posko (transparansi kas, pembagian piket adil).
    *   Mempermudah pembuatan laporan akhir (logbook harian terintegrasi, repositori dokumen).
    *   Mempermudah koordinasi jarak jauh antar anggota kelompok.

#### 📄 Panduan Memulai (Quick Start)
*   **Registrasi & Login**: Pengguna dapat masuk menggunakan Google Sign-In atau email standar dengan verifikasi OTP.
*   **Membeli/Mengaktifkan Posko**:
    *   Setelah login, pengguna awal (Host) dapat membeli lisensi posko melalui **Sistem Pembayaran Tripay** (Payment Gateway).
    *   Setelah pembayaran sukses, modul posko akan aktif sepenuhnya dan Host bisa mengundang anggota.

#### 📄 Paket Langganan Posko
*   **Deskripsi Paket**: Dapatkan akses penuh ke seluruh modul platform untuk satu posko KKN tanpa batasan kuota user/anggota kelompok.
*   **Harga Lisensi**: 
    *   Harga lisensi mengikuti konfigurasi aktif yang diatur oleh Admin pada sistem (ditampilkan di halaman Pembayaran).
    *   Pembayaran bersifat sekali bayar (*one-time payment*) untuk masa aktif selama durasi pelaksanaan KKN.

#### 📄 Peran & Hak Akses (User Roles)
Sistem memiliki 3 tingkat peran utama dari sisi pengguna:
1.  **Guest / User Biasa (Unsubscribed)**: Pengguna baru yang belum membeli/bergabung dengan posko mana pun. Hanya bisa melihat landing page atau melakukan checkout posko.
2.  **Host (Pemilik Posko)**: Pengguna yang membeli lisensi posko. Memiliki kontrol penuh atas posko tersebut: mengundang/mengeluarkan anggota, mengatur sirkulasi keuangan, mengelola inventaris/logistik, memoderasi aspirasi, dan mengunggah dokumen/media.
3.  **Member / Anggota**: Pengguna (mahasiswa KKN) yang bergabung ke dalam posko lewat undangan Host. Bisa menggunakan modul harian (piket harian, mengisi logbook pribadi, checklist barang pribadi, voting, aspirasi, upload dokumentasi) tetapi tidak bisa mengubah setelan utama posko.

#### 📄 Ketentuan Layanan & Pelanggaran
*   **Penggunaan Sumber Daya**: Setiap posko diberikan alokasi penyimpanan yang cukup besar namun memiliki batasan demi keadilan penggunaan bersama (Fair Usage Policy). Dokumentasi media dibatasi 20GB per posko, sedangkan dokumen berkas dibatasi 20MB per berkas.
*   **Ketentuan Pelanggaran (Sanksi Blokir)**:
    *   Dilarang mengunggah konten yang mengandung unsur SARA, pornografi, ujaran kebencian, kekerasan, atau pelanggaran hak cipta.
    *   Dilarang melakukan tindakan penyalahgunaan sistem (*abuse*), seperti memborbardir server dengan berkas sampah atau mencoba meretas sistem.
    *   **Sanksi**: Admin berhak menonaktifkan (*banned*) akun pengguna atau membekukan akses posko yang terbukti melakukan pelanggaran tanpa pengembalian dana (*refund*).

---

### 2. FITUR UTAMA POSKO

#### 📄 Dashboard
*   **Fungsi**: Rangkuman visual kondisi posko.
*   **Fitur Utama**: Menampilkan grafik saldo kas tersisa, jumlah program kerja yang selesai, jadwal piket hari ini, agenda terdekat, serta notifikasi aktivitas terbaru.
*   **Rute Teknis**: `host.dashboard`.

#### 📄 Kas & Keuangan (E-Bendahara)
*   **Fungsi**: Modul pelaporan sirkulasi dana posko secara transparan.
*   **Fitur Utama**:
    *   Pencatatan Kas Masuk (iuran anggota, bantuan dana, sponsor).
    *   Pencatatan Kas Keluar (belanja logistik dapur, print proker, sewa peralatan).
    *   Seluruh anggota posko dapat memantau riwayat transaksi untuk menjamin transparansi keuangan.
*   **Rute Teknis**: `finance.index`, `finance.store`, `finance.update`, `finance.destroy`.

#### 📄 Logbook & Proker
*   **Fungsi**: Laporan kerja kelompok dan jurnal aktivitas harian individu.
*   **Fitur Utama**:
    *   **Program Kerja (Proker)**: Menyusun daftar rencana kegiatan besar (target, penanggung jawab, estimasi tanggal, dan status progres).
    *   **Log harian**: Mengisi logbook aktivitas harian per tanggal sebagai draf laporan akhir KKN.
*   **Rute Teknis**:
    *   Proker: `logbook.proker.store`, `logbook.proker.update`, `logbook.proker.destroy`
    *   Logbook: `logbook.daily.store`, `logbook.daily.update`, `logbook.daily.destroy`

#### 📄 Inventaris
*   **Fungsi**: Manajemen barang pinjaman, sewaan, atau aset milik bersama posko.
*   **Fitur Utama**: Pencatatan jumlah, kondisi barang (baik/rusak), serta status kepemilikan agar tidak hilang saat masa KKN berakhir (misal: proyektor, dispenser, tikar, kompor).
*   **Rute Teknis**: `management.inventory.index`, `management.inventory.store`, `management.inventory.update`, `management.inventory.destroy`.

#### 📄 Logistik
*   **Fungsi**: Mengelola bahan konsumsi dan kebutuhan habis pakai posko harian.
*   **Fitur Utama**:
    *   Pencatatan stok bahan (beras, minyak, sabun, ATK).
    *   **Alur Barang Keluar**: Pengurangan stok logistik ketika diambil oleh anggota posko (contoh: "mengambil 2 kg beras untuk masak sore").
*   **Rute Teknis**: `management.logistic.index`, `management.logistic.store`, `management.logistic.barang-keluar` dll.

#### 📄 Barang Pribadi
*   **Fungsi**: Membantu mahasiswa menyusun daftar checklist barang bawaan pribadi dari rumah.
*   **Fitur Utama**: 
    *   Daftar checklist barang pribadi (laptop, selimut, pakaian, obat pribadi).
    *   Fitur interaktif *Toggle Packed* (sudah masuk tas) untuk meminimalkan resiko barang tertinggal di rumah atau tertukar di posko.
*   **Rute Teknis**: `personal-belongings.index`, `personal-belongings.store`, `personal-belongings.toggle-packed` dll.

#### 📄 Piket & Agenda
*   **Fungsi**: Pembagian jadwal tugas harian dan kalender kegiatan posko.
*   **Fitur Utama**:
    *   **Jadwal Piket**: Penentuan hari piket (Senin - Minggu) untuk setiap anggota posko (bersih-bersih posko, memasak).
    *   **Agenda Kegiatan**: Kalender jadwal spesifik kelompok (contoh: "Rapat Desa", "Kunjungan DPL").
*   **Rute Teknis**: `management.schedule.index`, `management.schedule.roster.store`, `management.schedule.event.store` dll.

#### 📄 Anggota
*   **Fungsi**: Pengelolaan data identitas kelompok KKN.
*   **Fitur Utama**: 
    *   Host dapat mengundang anggota kelompok dengan mengisi email.
    *   Menetapkan peran jabatan di posko (contoh: "Ketua Posko", "Sekretaris", "Bendahara").
*   **Rute Teknis**: `management.members.index`, `management.members.store`, `management.members.update`, `management.members.destroy`.

#### 📄 Log Aktivitas
*   **Fungsi**: Pelacakan riwayat aktivitas posko.
*   **Fitur Utama**: Menampilkan rekaman tindakan (*audit trail*) yang dilakukan oleh masing-masing anggota di posko (misal: siapa yang mengubah proker, siapa yang menambahkan pengeluaran kas).
*   **Rute Teknis**: `management.activity-logs.index`.

#### 📄 Buku Kontak
*   **Fungsi**: Direktori kontak darurat di daerah KKN.
*   **Fitur Utama**: Menyimpan nomor penting Kepala Desa, Ketua RT, Puskesmas terdekat, kepolisian, atau pemilik posko agar bisa dihubungi cepat saat darurat.
*   **Rute Teknis**: `contacts.index`, `contacts.store`, `contacts.update`, `contacts.destroy`.

#### 📄 Repository Proker
*   **Fungsi**: Pengarsipan berkas administrasi KKN secara aman.
*   **Fitur Penyimpanan**:
    *   Berkas disimpan secara aman di **Private Directory Server Aplikasi** (tidak dapat diakses publik secara bebas tanpa login/hak akses posko).
    *   **Batas Ukuran**: Maksimal **20 MB per berkas**.
    *   **Format yang Didukung**: `pdf, doc, docx, xls, xlsx, ppt, pptx, zip, rar, png, jpg, jpeg`.
    *   Fitur *Preview* dokumen instan di browser dan tombol unduh berkas.
*   **Rute Teknis**: `repository.index`, `repository.store`, `repository.download`, `repository.view`.

#### 📄 Voting & Aspirasi
*   **Fungsi**: Wadah musyawarah dan komunikasi tim posko secara transparan.
*   **Fitur Utama**:
    *   **Voting**: Membuat poling suara untuk pengambilan keputusan bersama (misal: menu makan harian).
    *   **Kotak Aspirasi**: Mengirim kritik, saran, atau masukan posko secara anonim agar anggota bebas menyampaikan unek-unek tanpa rasa canggung.
*   **Rute Teknis**: `voting.poll.store`, `voting.poll.vote`, `voting.aspiration.store`, `voting.aspiration.respond` dll.

#### 📄 Dokumentasi
*   **Fungsi**: Galeri foto dan video bukti fisik kegiatan KKN.
*   **Fitur Penyimpanan**:
    *   Berkas media disimpan pada **Server Penyimpanan Eksternal Immich** yang dioptimasi khusus untuk rendering foto/video berkecepatan tinggi dengan auto-thumbnail generator.
    *   **Kapasitas Penyimpanan**: Maksimal **20 GB per posko** (dikelola melalui koordinasi API terenkripsi).
    *   **Batas Unggahan**: Maksimal **500 MB per satu kali upload** langsung.
    *   **Teknologi Chunk Upload**: Untuk file besar (khususnya video HD), unggahan otomatis dibagi menjadi potongan kecil-kecil, memastikan unggahan berhasil meskipun koneksi internet di pedesaan lokasi KKN tidak stabil.
*   **Rute Teknis**: `host.documentation.index`, `host.documentation.upload`, `host.documentation.upload_chunk` dll.

---

### 3. PENGATURAN & UTILITAS PENGGUNA

#### 📄 Profil & Keamanan
*   **Fungsi**: Mengelola akun pribadi pengguna.
*   **Fitur Keamanan**:
    *   Ubah nama lengkap dan foto profil.
    *   **Ganti Email dengan OTP**: Keamanan ekstra, sistem mengirim kode OTP ke email baru terlebih dahulu sebelum email diperbarui secara permanen di database.
    *   Ubah sandi login secara berkala.
    *   **Batas Upaya Login**: Akses login dilindungi dengan sistem pembatasan percobaan (*throttle: 6 upaya per menit*) untuk mencegah pembajakan akun (brute force).
*   **Rute Teknis**: `profile.edit`, `profile.update`, `profile.email.otp`, `profile.email.change`, `user-password.update`.

---

### 4. LAYANAN BANTUAN

#### 📄 Mengajukan Keluhan
*   **Fungsi**: Saluran komunikasi cepat jika pengguna mengalami masalah saat menggunakan sistem SuperPosko.
*   **Fitur Utama**:
    *   **Bubble Layanan Bantuan**: Tersedia tombol bantuan terapung (*floating button*) di sudut halaman web.
    *   Pengguna dapat mengisi keluhan, kendala transaksi pembayaran posko, atau pertanyaan umum seputar penggunaan aplikasi langsung kepada Customer Service.
*   **Rute Teknis**: `reports.create`, `reports.store`.
