# Antigravity AI Development Workflow untuk SuperPosko

AI harus mengikuti workflow ini secara ketat saat membantu pengembangan proyek **SuperPosko** (SaaS Administrasi KKN).

---

## Step 1: Pahami Konteks & Tenant Scoping (Multitenancy)
Sebelum membuat atau mengubah kode backend/frontend, pastikan:
* Pahami fitur apa yang dikerjakan berdasarkan [project-context.md](file:///d:/Coding/Herd/superposko/.agents/project-context.md).
* Karena ini adalah aplikasi SaaS multitenancy, setiap query database, proses penyimpanan, dan pengambilan data **wajib dibatasi / di-scope secara aman berbasis `posko_id`** (tenant posko). Jangan biarkan data antar posko bocor.

---

## Step 2: Rencanakan Desain Arsitektur & Antarmuka
Sebelum menulis kode, buat perencanaan singkat mengenai:
* **Database & Migrasi:** Tentukan kolom tabel, relasi, dan foreign key `posko_id`.
* **Routing:** Definisikan route menggunakan controller Inertia.
* **UI/UX & Desain:** Pastikan komponen antarmuka yang akan dibuat menerapkan standar warna **Cultured (`#F4F7F7`)**, warna aksen **Light Blue (`#38BDF8`)**, serta tata letak responsif mobile-first sesuai dengan panduan di [DESAIN.md](file:///d:/Coding/Herd/superposko/DESAIN.md).

---

## Step 3: Pengerjaan Backend (Laravel 13 Way)
Setiap penambahan fungsionalitas data baru wajib mengikuti siklus standard Laravel:
1. **Migrasi Database:** Buat migrasi tabel baru lengkap dengan indeks penunjang performa (terutama pada `posko_id`).
2. **Model Eloquent:** Definisikan Model, relasi, mass assignment `fillable`, cast tipe data, dan Factory untuk testing.
3. **Form Request:** Buat kelas Form Request terpisah untuk validasi input request sebelum masuk controller.
4. **Controller:** Gunakan Controller untuk memproses logika bisnis (atau delegasikan ke Service Layer jika kompleks) dan return data menggunakan `Inertia::render()`.

---

## Step 4: Routing & Integrasi Wayfinder
* Setelah route didaftarkan di file PHP (`routes/web.php` atau `routes/settings.php`), pastikan route tersebut ter-generate menjadi TypeScript action/route menggunakan Wayfinder.
* Aktifkan skill `wayfinder-development`.
* Panggil route/action backend di dalam komponen Vue menggunakan impor `@/actions/...` atau `@/routes/...` agar terintegrasi secara type-safe.

---

## Step 5: Pengerjaan Frontend (Inertia v2 & Vue 3)
* Aktifkan skill `inertia-vue-development` dan `tailwindcss-development`.
* Buat halaman Vue di direktori `resources/js/pages/`.
* Gunakan fitur-fitur baru Inertia v2 seperti:
  * **Deferred Props:** Untuk memuat data berat secara asynchronous.
  * **Merging Props & Infinite Scroll:** Jika berurusan dengan list data yang panjang (seperti Logbook/Kas).
* Saat memuat deferred props, tampilkan komponen skeleton loader yang berdenyut lambat (pulsing skeleton) untuk menjaga transisi visual yang premium.

---

## Step 6: UI Styling dengan Tailwind CSS v4 & Shadcn
* Terapkan utility classes Tailwind CSS v4.
* Gunakan basis warna background `bg-[#F4F7F7]` (Cultured) untuk layout luar dan `bg-white` untuk panel card konten di atasnya.
* Pastikan elemen interaktif (tombol, input, checkbox) memiliki status focus ring dengan warna `ring-[#38BDF8]`.
* Gunakan reusable UI components (seperti tombol, dialog, dropdown, input) yang sudah tersedia di `resources/js/components/ui/` sebelum membuat komponen baru dari nol.

---

## Step 7: Penanganan Validasi Form
* Validasi form dilakukan secara dua arah:
  * **Frontend:** Gunakan form helper dari Inertia (`useForm`) untuk mengelola status submit, validasi error lokal, dan loading state secara elegan.
  * **Backend:** Wajib divalidasi ulang menggunakan Form Request Laravel untuk keamanan.

---

## Step 8: Pengujian & Kode Formatter (Pint)
* **Testing:** Tulis feature test menggunakan PHPUnit di folder `tests/Feature/`. Setiap fitur baru wajib memiliki pengujian otomatis (happy path & edge cases).
* **Pint Formatter:** Sebelum menyerahkan perubahan kode PHP kepada pengguna, jalankan perintah Pint untuk merapikan style PHP:
  ```powershell
  vendor/bin/pint --dirty --format agent
  ```

---

## Step 9: Verifikasi Tampilan & Build
* Lakukan verifikasi visual (responsivitas mobile, contrast rasio, kelengkapan input form, empty state).
* Lakukan build lokal atau di server menggunakan:
  ```bash
  npm run build
  ```
  untuk memastikan tidak ada error kompilasi TypeScript atau bundler (seperti warning anotasi Rolldown yang telah dikonfigurasi).
