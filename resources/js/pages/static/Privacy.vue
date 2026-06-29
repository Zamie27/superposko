<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ShieldAlert, Key, Eye, CheckCircle2 } from '@lucide/vue';
import PublicFooter from '@/components/PublicFooter.vue';
import { login, register, dashboard } from '@/routes';

defineProps<{
    footerAbout: string;
    footerEmail: string;
    footerPhone: string;
    footerCopyright: string;
}>();
</script>

<template>
    <Head title="Kebijakan Privasi - SuperPosko" />

    <div class="min-h-screen bg-[#F4F7F7] text-slate-900 font-sans antialiased selection:bg-[#38BDF8] selection:text-slate-950 flex flex-col">
        <!-- Navigation Header -->
        <header class="sticky top-0 z-50 w-full border-b border-slate-200/50 bg-white shadow-md">
            <div class="mx-auto flex max-w-7xl h-16 items-center justify-between px-6 lg:px-8">
                <Link href="/" class="flex items-center group">
                    <img src="/logo_superposko.png" alt="SuperPosko" class="h-9 w-auto transition-transform duration-300 group-hover:scale-105" />
                </Link>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                    <Link href="/" class="hover:text-[#38BDF8] transition-colors">Home</Link>
                    <Link href="/event" class="hover:text-[#38BDF8] transition-colors">Event</Link>
                    <Link href="/panduan" class="hover:text-[#38BDF8] transition-colors">Panduan</Link>
                    <a href="/#fitur" class="hover:text-[#38BDF8] transition-colors">Fitur</a>
                    <a href="/#pricing" class="hover:text-[#38BDF8] transition-colors">Harga</a>
                </nav>

                <!-- Actions -->
                <div class="hidden md:flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="rounded-lg bg-[#38BDF8] hover:bg-[#38BDF8]/90 px-4.5 py-2 text-sm font-semibold text-white transition duration-200"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="text-sm font-semibold text-slate-700 hover:text-[#38BDF8] transition-colors"
                        >
                            Masuk
                        </Link>
                        <Link
                            :href="register()"
                            class="rounded-lg bg-[#38BDF8] hover:bg-[#38BDF8]/90 px-4.5 py-2 text-sm font-semibold text-white transition duration-200"
                        >
                            Daftar Posko
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-grow max-w-4xl mx-auto px-6 py-12 md:py-20 font-sans">
            <div class="text-center space-y-4 mb-16">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#38BDF8]/10 px-3.5 py-1.5 text-xs font-semibold text-sky-600">
                    🔒 Privacy Policy & Data Protection
                </span>
                <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 tracking-tight">
                    Kebijakan Privasi
                </h1>
                <p class="text-base text-slate-500 max-w-xl mx-auto">
                    Ketahui bagaimana kami mengumpulkan, menggunakan, dan melindungi data pribadi serta informasi tim posko KKN Anda secara aman.
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-8 md:p-12 shadow-sm space-y-8 text-sm text-slate-655 leading-relaxed">
                <!-- Point 1 -->
                <div class="space-y-3">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <Eye class="w-5 h-5 text-sky-500" /> 1. Informasi Yang Kami Kumpulkan
                    </h2>
                    <p>
                        Kami mengumpulkan informasi yang Anda berikan secara langsung saat mendaftar akun, seperti nama lengkap, alamat email, nomor WhatsApp, foto profil, dan data administrasi posko KKN yang Anda daftarkan. Kami juga menyimpan berkas program kerja (proposal/laporan) serta bukti transaksi pembayaran yang diunggah secara sukarela.
                    </p>
                </div>

                <!-- Point 2 -->
                <div class="space-y-3">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <CheckCircle2 class="w-5 h-5 text-sky-500" /> 2. Penggunaan Informasi
                    </h2>
                    <p>
                        Seluruh informasi dan data yang dikumpulkan digunakan semata-mata untuk mengoperasikan, memelihara, dan menyediakan fitur kolaborasi SuperPosko (misalnya: pengiriman kode verifikasi OTP, integrasi transaksi pembayaran Tripay, visualisasi diagram iuran kas, dan sinkronisasi logbook tim). Kami tidak akan memperjualbelikan data Anda kepada pihak ketiga manapun.
                    </p>
                </div>

                <!-- Point 3 -->
                <div class="space-y-3">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <Key class="w-5 h-5 text-sky-500" /> 3. Keamanan Data
                    </h2>
                    <p>
                        Kami menggunakan protokol transfer data terenkripsi SSL/HTTPS, hashing kata sandi tingkat lanjut, dan pembatasan akses data internal untuk memastikan kerahasiaan data posko KKN Anda. Penyimpanan media dokumentasi foto/video dioptimasi secara terenkripsi menggunakan server eksternal Immich.
                    </p>
                </div>

                <!-- Point 4 -->
                <div class="space-y-3">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <ShieldAlert class="w-5 h-5 text-sky-500" /> 4. Perubahan Kebijakan
                    </h2>
                    <p>
                        Kami berhak memperbarui Kebijakan Privasi ini sewaktu-waktu. Kami akan memberitahukan perubahan signifikan melalui email terdaftar atau banner pengumuman di dasbor pengguna sebelum perubahan tersebut berlaku secara efektif.
                    </p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <PublicFooter 
            :footerAbout="footerAbout"
            :footerEmail="footerEmail"
            :footerPhone="footerPhone"
            :footerCopyright="footerCopyright"
        />
    </div>
</template>
