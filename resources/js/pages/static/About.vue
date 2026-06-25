<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { BookOpen, Users, Compass, ShieldCheck } from '@lucide/vue';
import { login, register, dashboard } from '@/routes';

const props = defineProps<{
    footerAbout: string;
    footerEmail: string;
    footerPhone: string;
    footerCopyright: string;
}>();

const isMenuOpen = ref(false);

const formattedPhone = computed(() => {
    if (!props.footerPhone) return '';
    let clean = props.footerPhone.replace(/[^0-9]/g, '');
    if (clean.startsWith('62')) {
        clean = clean.substring(2);
    } else if (clean.startsWith('0')) {
        clean = clean.substring(1);
    }
    const part1 = clean.substring(0, 3);
    const part2 = clean.substring(3, 7);
    const part3 = clean.substring(7);
    let result = '+62';
    if (part1) result += ' ' + part1;
    if (part2) result += '-' + part2;
    if (part3) result += '-' + part3;
    return result;
});
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
                        Dengan SuperPosko, seluruh anggota tim memiliki akses visual instan terhadap arus dana kas, status progres program kerja kelompok, dan jadwal piket posko secara real-time. Kami percaya, koordinasi tim yang rapi dan transparan adalah kunci kesuksesan program KKN.
                    </p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-slate-900 py-16 text-slate-400 border-t border-slate-800/50 mt-auto">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-12 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <div class="flex items-center gap-2 text-white">
                            <img src="/icon_superposko.png" alt="SuperPosko Icon" class="h-10 w-auto" />
                        </div>
                        <p class="mt-4 max-w-md text-sm leading-relaxed">
                            {{ footerAbout }}
                        </p>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-white">Pintasan</h4>
                        <ul class="mt-4 space-y-2.5 text-sm">
                            <li><Link href="/" class="hover:text-[#38BDF8] transition-colors">Home Page</Link></li>
                            <li><Link href="/panduan" class="hover:text-[#38BDF8] transition-colors">Panduan</Link></li>
                            <li><Link href="/privacy" class="hover:text-[#38BDF8] transition-colors">Kebijakan Privasi</Link></li>
                            <li><Link href="/terms" class="hover:text-[#38BDF8] transition-colors">Syarat & Ketentuan</Link></li>
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
                </div>
            </div>
        </footer>
    </div>
</template>
