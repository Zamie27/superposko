<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Book, Gavel, Scale, AlertTriangle } from '@lucide/vue';
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
    <Head title="Syarat & Ketentuan - SuperPosko" />

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
                    ⚖️ Terms of Service & User Agreement
                </span>
                <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 tracking-tight">
                    Syarat & Ketentuan
                </h1>
                <p class="text-base text-slate-500 max-w-xl mx-auto">
                    Harap baca dengan saksama seluruh aturan dan ketentuan penggunaan platform sebelum menggunakan layanan premium SuperPosko.
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-8 md:p-12 shadow-sm space-y-8 text-sm text-slate-655 leading-relaxed">
                <!-- Point 1 -->
                <div class="space-y-3">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <Book class="w-5 h-5 text-sky-500" /> 1. Penerimaan Ketentuan
                    </h2>
                    <p>
                        Dengan mendaftar akun atau menggunakan fitur di platform SuperPosko, Anda setuju untuk terikat secara hukum oleh Syarat & Ketentuan ini. Jika Anda tidak menyetujui bagian mana pun dari ketentuan ini, Anda tidak diperkenankan menggunakan platform kami.
                    </p>
                </div>

                <!-- Point 2 -->
                <div class="space-y-3">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <Gavel class="w-5 h-5 text-sky-500" /> 2. Batasan Penggunaan & Hak Akses
                    </h2>
                    <p>
                        Setiap akun posko KKN dibatasi sesuai dengan paket yang dipilih. Pengguna dilarang menyalahgunakan platform untuk mengunggah konten ilegal, pornografi, ujaran kebencian, pelanggaran hak cipta, atau tindakan pembajakan server. Batasan ukuran file berkas adalah 20MB per file dan kuota penyimpanan media dokumentasi dibatasi sebesar 20GB per posko.
                    </p>
                </div>

                <!-- Point 3 -->
                <div class="space-y-3">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <Scale class="w-5 h-5 text-sky-500" /> 3. Kebijakan Pembayaran & Pengembalian Dana
                    </h2>
                    <p>
                        Layanan aktivasi posko adalah transaksi sekali bayar (one-time payment) untuk masa aktif selama 40 hari penuh. Seluruh pembayaran diproses secara aman melalui integrasi Tripay / Midtrans. Pembayaran yang telah sukses dikonfirmasi tidak dapat dibatalkan atau dikembalikan (non-refundable).
                    </p>
                </div>

                <!-- Point 4 -->
                <div class="space-y-3">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <AlertTriangle class="w-5 h-5 text-sky-500" /> 4. Pembatasan Tanggung Jawab
                    </h2>
                    <p>
                        SuperPosko tidak bertanggung jawab atas kerugian tidak langsung, konflik internal kelompok, kesalahan pencatatan laporan keuangan manual oleh bendahara posko, atau kehilangan data yang diakibatkan oleh kelalaian login pengguna.
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
