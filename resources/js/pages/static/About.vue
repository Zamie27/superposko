<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { BookOpen, Users, Compass, ShieldCheck } from '@lucide/vue';
import { login, register, dashboard } from '@/routes';
import PublicFooter from '@/components/PublicFooter.vue';

const props = defineProps<{
    footerAbout: string;
    footerEmail: string;
    footerPhone: string;
    footerCopyright: string;
}>();

const isMenuOpen = ref(false);
</script>

<template>
    <Head title="Tentang Kami - SuperPosko" />

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
                    ℹ️ Company & Platform Overview
                </span>
                <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 tracking-tight">
                    Tentang SuperPosko
                </h1>
                <p class="text-base text-slate-500 max-w-xl mx-auto">
                    Ketahui lebih dalam mengenai visi, misi, dan tim di balik terwujudnya platform kolaborasi KKN digital terbaik di Indonesia.
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 p-8 md:p-12 shadow-sm space-y-12">
                <!-- Visi & Misi -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                            <Compass class="w-5 h-5 text-sky-500" /> Visi Kami
                        </h2>
                        <p class="text-sm text-slate-655 leading-relaxed">
                            Menjadi platform digital nomor satu di Indonesia yang mendigitalisasi, mempermudah, dan menyatukan seluruh administrasi serta manajemen kerja mahasiswa KKN (Kuliah Kerja Nyata) demi pengabdian masyarakat yang terstruktur dan nihil konflik.
                        </p>
                    </div>
                    <div class="space-y-3">
                        <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                            <ShieldCheck class="w-5 h-5 text-sky-500" /> Misi Kami
                        </h2>
                        <p class="text-sm text-slate-655 leading-relaxed">
                            Membangun sistem kolaborasi kelompok yang transparan dalam pengelolaan kas keuangan kelompok, mempermudah pelaporan logbook harian terintegrasi, serta menyediakan sarana penyimpanan berkas program kerja yang aman dan efisien.
                        </p>
                    </div>
                </div>

                <hr class="border-slate-100" />

                <!-- Deskripsi Detail -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-slate-900">Solusi Modern Untuk Mahasiswa KKN</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        SuperPosko dirancang berdasarkan riset mendalam terhadap kendala-kendala umum yang dihadapi oleh kelompok KKN di lapangan, seperti konflik pembagian tugas harian, tidak transparannya kas keuangan kelompok, dan hilangnya proposal/berkas fisik penting. 
                    </p>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Dengan SuperPosko, seluruh anggota tim memiliki akses visual instan terhadap arus dana kas, status progres program kerja kelompok, dan jadwal piket posko secara real-time. Kami percaya, koordinasi tim yang rapi and transparan adalah kunci kesuksesan program KKN.
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
