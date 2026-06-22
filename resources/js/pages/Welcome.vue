<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { dashboard, login, register } from '@/routes';

const isMenuOpen = ref(false);

const operasionalFeatures = [
    {
        title: 'E-Bendahara (Kas & Keuangan)',
        description: 'Manajemen transparan arus kas masuk/keluar dengan lampiran bukti nota digital. Menghindari risiko nombok dan selisih dana.',
        icon: `<svg class="h-6 w-6 text-[#38BDF8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>`
    },
    {
        title: 'Digital Logbook & Proker',
        description: 'Sistem pelaporan kegiatan harian untuk DPL dan Kanban Board untuk melacak status progres program kerja (To Do, In Progress, Done).',
        icon: `<svg class="h-6 w-6 text-[#38BDF8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
        </svg>`
    },
    {
        title: 'Manajemen Inventaris & Logistik',
        description: 'Katalogisasi barang milik posko (stok) maupun barang pinjaman warga, dilengkapi sistem peringatan stok menipis dan checklist pengembalian.',
        icon: `<svg class="h-6 w-6 text-[#38BDF8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>`
    }
];

const pendukungFeatures = [
    {
        title: 'Buku Kontak Desa',
        description: 'Directory digital kontak warga penting (RT/RW/Tokoh) untuk mempermudah koordinasi lapangan yang mendadak.',
        icon: `<svg class="h-6 w-6 text-[#38BDF8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>`
    },
    {
        title: 'Schedule Management (Piket & Agenda)',
        description: 'Kalender terpadu untuk jadwal piket harian, rapat, dan agenda kerja bersama untuk menjaga disiplin anggota.',
        icon: `<svg class="h-6 w-6 text-[#38BDF8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 3V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>`
    },
    {
        title: 'Repository Proker (Lost & Found)',
        description: 'Wadah sentral untuk menyimpan proposal, surat izin, notulensi, dan feedback warga per program kerja agar tidak tercecer di laptop anggota.',
        icon: `<svg class="h-6 w-6 text-[#38BDF8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20"></path>
        </svg>`
    },
    {
        title: 'Voting & Aspirasi',
        description: 'Fitur polling cepat untuk pengambilan keputusan internal kelompok secara demokratis dan transparan.',
        icon: `<svg class="h-6 w-6 text-[#38BDF8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>`
    },
    {
        title: 'Galeri Dokumentasi',
        description: 'Penyimpanan terpusat khusus untuk foto dan video dokumentasi program kerja posko dengan kapasitas penyimpanan lega hingga 20 GB.',
        icon: `<svg class="h-6 w-6 text-[#38BDF8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>`
    }
];

const checklistFeatures = [
    'E-Bendahara (Kas & Keuangan)',
    'Digital Logbook & Proker',
    'Manajemen Inventaris & Logistik',
    'Buku Kontak Desa',
    'Schedule Management (Piket & Agenda)',
    'Repository Proker (Lost & Found)',
    'Voting & Aspirasi',
    'Manajemen Barang Bawaan Pribadi',
    'Galeri Dokumentasi (Penyimpanan 20 GB)',
    'Menambahkan Anggota Sampai 20 Akun',
    'Support 24/7'
];

const props = defineProps<{
    packageName: string;
    packagePrice: number;
    packageStrikePrice: number;
    packageDescription: string;
    footerAbout: string;
    footerEmail: string;
    footerPhone: string;
    footerCopyright: string;
}>();

const formattedPhone = computed(() => {
    if (!props.footerPhone) return '';
    let clean = props.footerPhone.replace(/[^0-9]/g, '');
    if (clean.startsWith('62')) {
        clean = clean.substring(2);
    } else if (clean.startsWith('0')) {
        clean = clean.substring(1);
    }
    
    let part1 = clean.substring(0, 3);
    let part2 = clean.substring(3, 7);
    let part3 = clean.substring(7);
    
    let result = '+62';
    if (part1) result += ' ' + part1;
    if (part2) result += '-' + part2;
    if (part3) result += '-' + part3;
    
    return result;
});

const faqs = [
    {
        question: 'Apakah biaya langganan flat?',
        answer: 'Ya, biaya flat per kelompok posko KKN, berapapun jumlah anggota di dalamnya.'
    },
    {
        question: 'Bagaimana cara mendaftarkan kelompok kami?',
        answer: 'Klik "Daftar Sekarang", lengkapi formulir info posko KKN Anda, lakukan aktivasi pembayaran flat, dan tim Anda langsung aktif.'
    },
    {
        question: 'Apakah data kami aman setelah masa KKN selesai?',
        answer: 'Ya, seluruh data tersimpan dengan aman dan dapat diunduh kapan saja oleh pihak posko untuk keperluan pelaporan.'
    }
];
</script>

<template>
    <Head title="SuperPosko - Platform Manajemen KKN Modern" />

    <div class="min-h-screen bg-[#F4F7F7] text-slate-900 font-sans antialiased selection:bg-[#38BDF8] selection:text-slate-950">
        
        <!-- Navigation Header -->
        <header class="sticky top-0 z-50 w-full border-b border-slate-200/50 bg-white shadow-md">
            <div class="mx-auto flex max-w-7xl h-16 items-center justify-between px-6 lg:px-8">
                <!-- Logo Only (No text span as logo contains branding text) -->
                <Link href="/" class="flex items-center group">
                    <img src="/logo_superposko.png" alt="SuperPosko" class="h-9 w-auto transition-transform duration-300 group-hover:scale-105" />
                </Link>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                    <Link href="/" class="text-[#38BDF8] transition-colors">Home</Link>
                    <a href="#fitur" class="hover:text-[#38BDF8] transition-colors">Fitur</a>
                    <a href="#pricing" class="hover:text-[#38BDF8] transition-colors">Harga</a>
                    <a href="#faq" class="hover:text-[#38BDF8] transition-colors">FAQ</a>
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

                <!-- Mobile Menu Button -->
                <div class="flex items-center gap-2 md:hidden">
                    <button 
                        @click="isMenuOpen = !isMenuOpen" 
                        type="button" 
                        class="rounded-lg p-2 text-slate-600 hover:bg-slate-200/50"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path v-if="!isMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Dropdown Nav -->
            <div v-if="isMenuOpen" class="border-b border-slate-200/50 bg-[#F4F7F7] px-6 py-4 md:hidden">
                <nav class="flex flex-col gap-4 text-sm font-semibold text-slate-600">
                    <a href="#fitur" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">Fitur</a>
                    <a href="#pricing" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">Harga</a>
                    <a href="#faq" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">FAQ</a>
                    <div class="h-px bg-slate-200/50 my-2"></div>
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        @click="isMenuOpen = false"
                        class="text-center rounded-lg bg-[#38BDF8] py-2.5 text-sm font-bold text-white"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            @click="isMenuOpen = false"
                            class="text-center py-2 text-sm font-semibold text-slate-700"
                        >
                            Masuk
                        </Link>
                        <Link
                            :href="register()"
                            @click="isMenuOpen = false"
                            class="text-center rounded-lg bg-[#38BDF8] py-2.5 text-sm font-bold text-white"
                        >
                            Daftar Posko
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative overflow-hidden bg-white min-h-[calc(100vh-4rem)] flex flex-col justify-center py-0">
            <div class="absolute top-0 left-1/2 -z-10 h-[400px] w-[800px] -translate-x-1/2 rounded-full bg-[#38BDF8]/5 blur-3xl"></div>
            
            <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center py-16">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#38BDF8]/10 px-3.5 py-1.5 text-xs font-semibold text-sky-600">
                    🚀 Kolaborasi Tim KKN Modern & Terstruktur
                </span>
                
                <h1 class="mt-6 text-4xl font-extrabold tracking-tight sm:text-6xl text-slate-900 leading-[1.15]">
                    Atur Posko KKN Kamu <br class="hidden sm:inline" />
                    <span class="text-[#38BDF8]">
                        Dalam Satu Platform!
                    </span>
                </h1>

                <p class="mx-auto mt-6 max-w-2xl text-base sm:text-lg leading-relaxed text-slate-600">
                    Platform posko KKN digital modern untuk koordinasi program kerja, pencatatan keuangan transparan, pembagian piket, dan manajemen peminjaman logistik yang praktis.
                </p>

                <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="rounded-xl bg-[#38BDF8] hover:bg-[#38BDF8]/90 px-6 py-3.5 text-base font-bold text-white shadow-md shadow-[#38BDF8]/10 transition duration-200"
                    >
                        Buka Dashboard Tim
                    </Link>
                    <Link
                        v-else
                        :href="register()"
                        class="rounded-xl bg-[#38BDF8] hover:bg-[#38BDF8]/90 px-6 py-3.5 text-base font-bold text-white shadow-md shadow-[#38BDF8]/10 transition duration-200"
                    >
                        Daftar Sekarang
                    </Link>
                    <a
                        href="#fitur"
                        class="rounded-xl border border-slate-300 hover:border-slate-400 bg-white hover:bg-slate-50 px-6 py-3.5 text-base font-semibold text-slate-700 transition duration-200"
                    >
                        Pelajari Fitur
                    </a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="fitur" class="py-20 bg-white border-y border-slate-200/50">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                
                <!-- Category 1: Modul Operasional Utama -->
                <div class="mb-16">
                    <div class="max-w-3xl mb-10">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-[#38BDF8]">Pondasi Operasional</h2>
                        <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                            Modul Operasional Utama (Pondasi)
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                        <div 
                            v-for="feat in operasionalFeatures" 
                            :key="feat.title"
                            class="flex flex-col items-start p-8 rounded-2xl border border-slate-200/60 bg-[#F4F7F7]/30 hover:bg-[#F4F7F7]/50 transition duration-300"
                        >
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#38BDF8]/10 mb-6" v-html="feat.icon"></div>
                            <h4 class="text-lg font-bold text-slate-900">{{ feat.title }}</h4>
                            <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ feat.description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Category 2: Modul Pendukung -->
                <div>
                    <div class="max-w-3xl mb-10">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-[#38BDF8]">Anti-Drama Internal</h2>
                        <h3 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                            Modul Pendukung (Anti-Drama Internal)
                        </h3>
                    </div>
                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:flex lg:flex-wrap lg:justify-center">
                        <div 
                            v-for="feat in pendukungFeatures" 
                            :key="feat.title"
                            class="flex flex-col items-start p-8 rounded-2xl border border-slate-200/60 bg-[#F4F7F7]/30 hover:bg-[#F4F7F7]/50 transition duration-300 lg:w-[calc(25%-1.5rem)]"
                        >
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#38BDF8]/10 mb-6" v-html="feat.icon"></div>
                            <h4 class="text-lg font-bold text-slate-900">{{ feat.title }}</h4>
                            <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ feat.description }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- Pricing Card Section -->
        <section id="pricing" class="py-20 lg:py-24">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                        Harga Langganan Terjangkau
                    </h2>
                    <p class="mt-4 text-base text-slate-600">
                        Investasi hemat demi transparansi keuangan dan keteraturan administrasi seluruh anggota kelompok posko KKN Anda.
                    </p>
                </div>

                <!-- Pricing Card -->
                <div class="mx-auto max-w-md rounded-2xl border border-[#38BDF8]/40 bg-white p-8 text-center shadow-md">
                    <span class="text-xs font-bold uppercase tracking-wider text-sky-600">Akses Penuh SaaS</span>
                    <h3 class="mt-2 text-2xl font-bold text-slate-900">{{ packageName }}</h3>
                    <div class="my-6 flex items-baseline justify-center gap-1.5 flex-col items-center">
                        <div class="flex items-center gap-2">
                            <span class="text-sm line-through text-slate-400">Rp {{ packageStrikePrice.toLocaleString('id-ID') }}</span>
                            <span class="text-4xl font-extrabold text-slate-900">Rp {{ packagePrice.toLocaleString('id-ID') }}</span>
                        </div>
                        <span class="text-xs text-slate-500 mt-1">Sekali bayar untuk 1 Siklus Posko (40 Hari)</span>
                    </div>
                    <p class="text-sm leading-relaxed text-slate-600">
                        {{ packageDescription }}
                    </p>
                    
                    <div class="mt-8">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="dashboard()"
                            class="block w-full rounded-xl bg-[#38BDF8] hover:bg-[#38BDF8]/90 py-3 text-sm font-bold text-white transition duration-200"
                        >
                            Masuk ke Dashboard
                        </Link>
                        <Link
                            v-else
                            :href="register()"
                            class="block w-full rounded-xl bg-[#38BDF8] hover:bg-[#38BDF8]/90 py-3 text-sm font-bold text-white transition duration-200"
                        >
                            Daftar Sekarang
                        </Link>
                    </div>

                    <!-- Divider -->
                    <div class="my-6 border-t border-slate-100"></div>

                    <!-- Features Checklist -->
                    <ul class="space-y-3.5 text-left text-sm text-slate-600">
                        <li v-for="item in checklistFeatures" :key="item" class="flex items-center gap-3">
                            <svg class="h-5 w-5 shrink-0 text-[#38BDF8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>{{ item }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="py-20 bg-white/50 border-t border-slate-200/50">
            <div class="mx-auto max-w-4xl px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Pertanyaan Umum</h2>
                    <p class="mt-3 text-slate-600">Punya pertanyaan lain mengenai SuperPosko?</p>
                </div>
                
                <div class="space-y-6">
                    <div 
                        v-for="faq in faqs" 
                        :key="faq.question"
                        class="rounded-xl border border-slate-200 bg-white p-6"
                    >
                        <h3 class="text-base font-bold text-slate-900">{{ faq.question }}</h3>
                        <p class="mt-2.5 text-sm leading-relaxed text-slate-600">{{ faq.answer }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Banner Section -->
        <section class="py-12 bg-[#F4F7F7]">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-2xl bg-[#38BDF8] px-8 py-12 text-center shadow-xl md:px-16 md:py-16">
                    <div class="relative z-10 max-w-2xl mx-auto">
                        <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                            Siap mengaktifkan SuperPosko?
                        </h2>
                        <p class="mt-4 text-base text-white/90 font-medium">
                            Ajak kelompok posko Anda berkolaborasi secara digital dan transparan demi pengabdian masyarakat yang sukses.
                        </p>
                        <div class="mt-8 flex justify-center gap-4.5">
                            <Link
                                id="dashboard-cta-link"
                                v-if="$page.props.auth.user"
                                :href="dashboard()"
                                class="rounded-xl bg-white hover:bg-slate-100 px-6 py-3.5 text-sm font-bold text-slate-950 shadow-sm transition duration-200"
                            >
                                Masuk ke Dasbor
                            </Link>
                            <Link
                                id="register-cta-link"
                                v-else
                                :href="register()"
                                class="rounded-xl bg-white hover:bg-slate-100 px-6 py-3.5 text-sm font-bold text-slate-950 shadow-sm transition duration-200"
                            >
                                Daftar Sekarang
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-slate-900 py-16 text-slate-400 border-t border-slate-800/50">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-12 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <div class="flex items-center gap-2 text-white">
                            <!-- Footer logo as icon only without text -->
                            <img src="/icon_superposko.png" alt="SuperPosko Icon" class="h-10 w-auto" />
                        </div>
                        <p class="mt-4 max-w-md text-sm leading-relaxed">
                            {{ footerAbout }}
                        </p>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-white">Pintasan</h4>
                        <ul class="mt-4 space-y-2.5 text-sm">
                            <li><a href="#fitur" class="hover:text-[#38BDF8] transition-colors">Fitur Utama</a></li>
                            <li><a href="#pricing" class="hover:text-[#38BDF8] transition-colors">Harga Paket</a></li>
                            <li><a href="#faq" class="hover:text-[#38BDF8] transition-colors">FAQ</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-white">Hubungi Kami</h4>
                        <ul class="mt-4 space-y-2.5 text-sm">
                            <li>Email: {{ footerEmail }}</li>
                            <li>Telepon: {{ formattedPhone }}</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-12 border-t border-slate-800 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs">
                    <p>
                        &copy; 2026 
                        <a href="https://kuukok.my.id" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors underline font-medium">{{ footerCopyright }}</a> 
                        - Solusi digital profesional. Hak Cipta Dilindungi.
                    </p>
                    <p>Dibuat dengan dedikasi untuk memajukan pengabdian masyarakat mahasiswa Indonesia.</p>
                </div>
            </div>
        </footer>

    </div>
</template>

<style>
html {
    scroll-behavior: smooth;
}
</style>
