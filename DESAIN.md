# Panduan Desain Antarmuka (DESAIN.md)

## Mission
Menciptakan panduan visual yang terstruktur, konsisten, dan elegan untuk proyek **SuperPosko** (superposko.web.id). Panduan ini dirancang untuk memastikan antarmuka pengguna (UI) tetap konsisten, sangat responsif di segala perangkat (terutama mobile untuk kebutuhan koordinasi KKN lapangan), dan mengusung estetika *simple but elegant* yang terinspirasi oleh UI SumoPod (bersih, minimalis, dan berfokus pada konten).

---

## Brand & Visual Identity
* **Produk:** SuperPosko (SaaS Platform Administrasi KKN)
* **Estetika Utama:** Minimalis, Elegan, Bersih, Berorientasi Konten (*Content-First*), dengan sudut lengkung yang halus dan bayangan mikro.
* **Tema Warna:** 
  * Basis Light Mode menggunakan warna **Cultured (`#F4F7F7`)** untuk memberikan kesan bersih namun lembut di mata (bukan putih murni yang silau).
  * Basis Dark Mode menggunakan **Invert dari Cultured (`#080A0A`)** sebagai dasar gelap yang elegan.
  * Warna Aksen Utama (Primary) menggunakan biru muda **Light Blue (`#38BDF8`)** yang cerah dan modern.

---

## Design Tokens & Foundations

### 1. Sistem Warna (Color System)

| Token Desain | Nama Semantik | Nilai Light Mode | Nilai Dark Mode (Invert) | Kegunaan |
| :--- | :--- | :--- | :--- | :--- |
| `color.bg.base` | Background Utama | `#F4F7F7` (Cultured) | `#080A0A` | Background dasar halaman aplikasi |
| `color.bg.surface` | Background Card/Kontainer | `#FFFFFF` | `#101414` | Card, sidebar, modal, dropdown |
| `color.bg.muted` | Background Muted | `#E5EBEB` | `#1A2020` | Input nonaktif, tag/badge, table header |
| `color.brand.primary` | Aksen Utama (Primary) | `#38BDF8` (Sky Blue) | `#38BDF8` | Button primary, border fokus, link aktif |
| `color.brand.hover` | Hover Primary | `#0EA5E9` (Sky Blue Darker) | `#7DD3FC` (Sky Blue Lighter) | State hover pada tombol/link |
| `color.text.primary` | Teks Utama | `#111827` (Gray 900) | `#F9FAF5` | Judul, tulisan konten utama |
| `color.text.secondary`| Teks Pendukung | `#4B5563` (Gray 600) | `#9CA3AF` | Subtitle, label, deskripsi tabel |
| `color.border.default`| Border Standar | `#E2E8F0` | `#1E293B` | Border tabel, pemisah baris, border input |
| `color.border.focus`  | Border Fokus | `#38BDF8` | `#38BDF8` | Ring indikator saat fokus |
| `color.success`       | Status Sukses | `#10B981` (Emerald) | `#34D399` | Badge status active/selesai |

### 2. Tipografi (Typography)
* **Font Family Utama:** `'Instrument Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif` (Sesuai dengan `vite.config.ts` untuk memancarkan kesan minimalis dan modern).
* **Ukuran Font (Typography Scale):**
  * `font.size.xs` = `12px` (Teks pendukung tabel, meta-info)
  * `font.size.sm` = `14px` (Teks dasar tombol, input, navigasi, paragraf)
  * `font.size.base` = `16px` (Konten utama, deskripsi card)
  * `font.size.lg` = `18px` (Judul card, header tabel)
  * `font.size.xl` = `20px` (Sub-judul section)
  * `font.size.2xl` = `24px` (Judul halaman pada mobile)
  * `font.size.3xl` = `32px` (Judul halaman utama / Dashboard KPI)
  * `font.size.4xl` = `48px` (Landing page Hero Title)

### 3. Spacing & Grid (Skala Jarak)
Menggunakan kelipatan 4px/8px untuk konsistensi ruang:
* `space.1` = `4px`
* `space.2` = `8px` (Padding dalam badge, spacing antar icon-text)
* `space.3` = `12px`
* `space.4` = `16px` (Padding standar card mobile, gap antar input)
* `space.5` = `20px`
* `space.6` = `24px` (Padding standar desktop, gap antar panel)
* `space.7` = `32px` (Gap section besar)
* `space.8` = `48px`

### 4. Radius & Border (Sudut Lengkung)
* `radius.xs` = `4px` (Badge, tag)
* `radius.sm` = `6px` (Tombol kecil, input)
* `radius.md` = `8px` (Tombol utama, panel kontrol)
* `radius.lg` = `12px` (Card konten utama, card tabel, modal)
* `radius.xl` = `16px` (Sidebar, panel besar)

### 5. Shadow & Elevation (Efek Kedalaman)
* `shadow.sm` = `0 1px 2px 0 rgba(0, 0, 0, 0.05)` (Desain flat tapi tegas)
* `shadow.md` = `0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -1px rgba(0, 0, 0, 0.02)` (Elevasi standar untuk card)

---

## Component-Level Rules

### 1. Struktur Layout Utama (Anatomi Dashboard)
Meniru visual SumoPod yang bersih dengan 2 area utama:
* **Sidebar Kiri (Navigation Panel):**
  * Lebar tetap pada desktop (`260px`).
  * Background: `color.bg.surface`, Border kanan tipis (`color.border.default`).
  * Aktif/Selected State: Latar belakang abu sangat tipis dengan indikator teks berwarna `color.brand.primary` dan font-weight medium.
  * Pada Mobile: Menjadi slide-over drawer dengan toggle hamburger menu di bagian topbar.
* **Main Content Area (Panel Kanan):**
  * Background dasar: `color.bg.base` (Cultured `#F4F7F7`).
  * Margin padding: `space.6` (`24px`) di desktop, dan `space.4` (`16px`) di mobile.
  * Seluruh card konten harus menggunakan background `color.bg.surface` (putih bersih) agar kontras dengan latar belakang dasar.

### 2. Form & Input Control (Simple & Clean)
* **Input Field & Select:**
  * Background: `color.bg.surface` (atau `color.bg.base` untuk status disabled).
  * Border: `1px solid color.border.default`.
  * Status Fokus: Border berubah menjadi `color.brand.primary` dengan soft shadow ring berwarna biru muda.
  * Placeholder: Menggunakan `color.text.secondary` dengan opacity `50%`.
* **Action Buttons (Tombol Utama & Sekunder):**
  * *Primary Button:* Background `color.brand.primary` (`#38BDF8`), teks putih, font-weight semi-bold. Transisi hover ke `#0EA5E9`.
  * *Secondary/Outline Button:* Teks `color.text.primary`, border `1px solid color.border.default`, background transparan/putih. Hover menjadi soft gray.

### 3. Data Tables (Content-First Table)
* Header kolom menggunakan huruf kapital, ukuran `font.size.xs`, teks berwarna `color.text.secondary`, tanpa border tebal (cukup garis pemisah tipis horizontal).
* Baris tabel menggunakan hover effect (background berubah menjadi soft gray/biru tipis).
* Status Badge (seperti status "active" atau "selesai"):
  * Menggunakan background pastel semi-transparan dengan teks berwarna solid kontras (misal: hijau pastel dengan tulisan hijau emerald gelap untuk status sukses/aktif).

---

## Aturan Responsif (Responsive & Mobile Rules)
* **Layout Grid:** Gunakan `grid-cols-1` pada mobile, `grid-cols-2` pada tablet, dan `grid-cols-3` atau lebih pada desktop untuk Dashboard KPI/Metrik.
* **Scrolling:** Tabel data besar pada mobile **harus** dibungkus dalam kontainer `overflow-x-auto` agar tidak memotong layar secara kasar, atau bertransformasi menjadi bentuk card list yang ditumpuk secara vertikal (vertical stack).
* **Ukuran Interaksi Sentuh (Touch Targets):** Semua elemen interaktif (tombol, link, checkbox) harus memiliki area klik minimal `44px x 44px` pada perangkat seluler.

---

## Aksesibilitas (Accessibility & WCAG 2.2 AA)
* **Focus States:** Jangan pernah menyembunyikan default browser focus ring tanpa menyediakan alternatif. Elemen yang difokuskan keyboard harus memiliki outline fokus yang sangat jelas (`color.brand.primary`).
* **Contrast Ratio:** Teks utama (`color.text.primary`) terhadap background wajib memenuhi rasio kontras minimal `4.5:1` (WCAG AA).
* **Alt Text & Aria-Label:** Icon-only buttons (tombol yang hanya berisi ikon tanpa tulisan) wajib menggunakan atribut `aria-label` yang deskriptif.

---

## Aturan Desain yang Dilarang (Prohibited Anti-Patterns)
* **Dilarang** menggunakan warna hitam pekat `#000000` atau abu-abu gelap kasar untuk shadow. Gunakan shadow yang sangat halus.
* **Dilarang** menggunakan font bawaan browser. Wajib memanggil font stack `'Instrument Sans'`.
* **Dilarang** menambahkan margin atau padding *ad-hoc* di luar skala kelipatan token (misal: `margin-top: 13px` dilarang, wajib memakai `margin-top: 12px` / `space.3` atau `16px` / `space.4`).

---

## QA Checklist untuk Developer (Sebelum Rilis UI)
- [ ] Apakah warna dasar background adalah Cultured `#F4F7F7` (Light Mode) dan memiliki kontras yang baik dengan card putih?
- [ ] Apakah font utama `'Instrument Sans'` ter-load dengan benar?
- [ ] Apakah sidebar sudah responsif dan tersembunyi ke dalam hamburger menu pada layar handphone?
- [ ] Apakah area klik tombol-tombol pada versi mobile memiliki tinggi minimal `44px`?
- [ ] Apakah transisi hover pada button primary `#38BDF8` sudah diimplementasikan dengan halus (`transition-duration: 200ms`)?
- [ ] Apakah halaman kosong (*empty state*) pada tabel/fitur sudah didesain secara elegan dengan ilustrasi minimalis dan tombol *Call to Action*?
- [ ] Apakah form input sudah memiliki focus ring indicator yang jelas?
- [ ] Apakah kontras warna teks dan background sudah nyaman dibaca di bawah terik matahari (simulasi penggunaan lapangan)?
