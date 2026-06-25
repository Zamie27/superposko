<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Gift, Calendar, ShieldCheck, Trophy, Info, Play } from '@lucide/vue';
import { ref, computed } from 'vue';
import { login, register } from '@/routes';

const isMenuOpen = ref(false);

const props = defineProps<{
    title: string;
    description: string;
    youtubeEmbedUrl: string;
    prize: string;
    startDate: string;
    endDate: string;
    rules: string;
    footerAbout: string;
    footerEmail: string;
    footerPhone: string;
    footerCopyright: string;
}>();

const formattedPhone = computed(() => {
    if (!props.footerPhone) {
return '';
}

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

    if (part1) {
result += ' ' + part1;
}

    if (part2) {
result += '-' + part2;
}

    if (part3) {
result += '-' + part3;
}
    
    return result;
});

const rulesList = computed(() => {
    if (!props.rules) {
return [];
}

    return props.rules.split('\n').filter(line => line.trim() !== '');
});

const formatDate = (dateStr: string) => {
    if (!dateStr) {
return '';
}

    const date = new Date(dateStr);

    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};
</script>

<template>
    <Head :title="`${title} - SuperPosko`" />

    <div class="min-h-screen bg-[#F4F7F7] text-slate-900 font-sans antialiased selection:bg-[#38BDF8] selection:text-slate-950">
        
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
                    <a href="/#faq" class="hover:text-[#38BDF8] transition-colors">FAQ</a>
                </nav>

                <!-- Actions -->
                <div class="hidden md:flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        href="/dashboard"
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
                    <Link href="/" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">Home</Link>
                    <Link href="/event" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">Event</Link>
                    <Link href="/panduan" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">Panduan</Link>
                    <a href="/#fitur" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">Fitur</a>
                    <a href="/#pricing" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">Harga</a>
                    <a href="/#faq" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">FAQ</a>
                    <div class="h-px bg-slate-200/50 my-2"></div>
                    <Link
                        v-if="$page.props.auth.user"
                        href="/dashboard"
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

        <!-- Main Content Hero -->
        <main class="mx-auto max-w-7xl px-6 lg:px-8 py-12 md:py-16">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/10 px-4 py-2 text-xs font-black text-amber-600 uppercase tracking-widest">
                    🏆 Special Event
                </span>
                <h1 class="mt-6 text-4xl font-extrabold tracking-tight sm:text-5xl text-slate-900 leading-[1.15]">
                    {{ title }}
                </h1>
                <p class="mt-4 text-base text-slate-650 leading-relaxed">
                    {{ description }}
                </p>
            </div>

            <!-- Event Highlight Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
                <!-- Prize card -->
                <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white p-6 rounded-2xl shadow-md flex flex-col justify-between items-center text-center">
                    <div class="h-12 w-12 rounded-full bg-white/10 flex items-center justify-center mb-4">
                        <Trophy class="size-6 text-white" />
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-100 block">Total Hadiah</span>
                        <h3 class="text-3xl font-black mt-2 select-all">{{ prize }}</h3>
                        <span class="text-xs font-bold text-white bg-white/20 px-2.5 py-0.5 rounded-md inline-block mt-1">
                            & Sertifikat Penghargaan
                        </span>
                    </div>
                    <span class="text-[10px] text-amber-100/80 mt-4">Hadiah saldo digital untuk pelapor bug valid dan Sertifikat Penghargaan</span>
                </div>

                <!-- Date Duration card -->
                <div class="bg-white border p-6 rounded-2xl shadow-xs flex flex-col justify-between items-center text-center">
                    <div class="h-12 w-12 rounded-full bg-sky-50 dark:bg-sky-950/30 flex items-center justify-center mb-4 text-[#38BDF8]">
                        <Calendar class="size-6" />
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Periode Event</span>
                        <h3 class="text-lg font-extrabold text-slate-800 mt-2">
                            {{ formatDate(startDate) }} - {{ formatDate(endDate) }}
                        </h3>
                    </div>
                    <span class="text-[10px] text-slate-400 mt-4">Durasi event berlangsung selama 5 hari</span>
                </div>

                <!-- Goal / Purpose card -->
                <div class="bg-white border p-6 rounded-2xl shadow-xs flex flex-col justify-between items-center text-center">
                    <div class="h-12 w-12 rounded-full bg-emerald-50 dark:bg-emerald-950/30 flex items-center justify-center mb-4 text-emerald-500">
                        <ShieldCheck class="size-6" />
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Tujuan Pengujian</span>
                        <h3 class="text-sm font-bold text-slate-700 mt-2 leading-relaxed">
                            Mematangkan kesiapan, kesesuaian, kestabilan, dan keamanan aplikasi sebelum live deployment.
                        </h3>
                    </div>
                    <span class="text-[10px] text-slate-450 mt-4">Fokus stabilitas & integritas platform</span>
                </div>
            </div>

            <!-- YouTube Video Embed -->
            <div v-if="youtubeEmbedUrl" class="mb-16">
                <h2 class="text-xl font-extrabold text-slate-900 mb-6 flex items-center gap-2">
                    <Play class="size-5 text-red-500 fill-red-500 shrink-0" />
                    Video Panduan & Tutorial Event
                </h2>
                <div class="aspect-video w-full max-w-4xl mx-auto rounded-2xl border-4 border-white shadow-lg overflow-hidden bg-slate-950">
                    <iframe
                        :src="youtubeEmbedUrl"
                        title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                        class="w-full h-full"
                    ></iframe>
                </div>
            </div>

            <!-- Rules & Guidelines -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start mb-16">
                <!-- Requirements/Rules -->
                <div class="bg-white border rounded-2xl p-6 shadow-xs lg:col-span-2">
                    <h2 class="text-xl font-extrabold text-slate-900 mb-6 flex items-center gap-2">
                        <Gift class="size-5 text-[#38BDF8] shrink-0" />
                        Syarat & Ketentuan Event
                    </h2>
                    <ul class="space-y-4">
                        <li v-for="(rule, index) in rulesList" :key="index" class="flex gap-4 items-start">
                            <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-[#38BDF8]/10 text-xs font-bold text-sky-600">
                                {{ index + 1 }}
                            </span>
                            <p class="text-sm text-slate-650 leading-relaxed pt-0.5">
                                {{ rule.replace(/^\d+\.\s*/, '') }}
                            </p>
                        </li>
                    </ul>
                </div>

                <!-- Callout action box -->
                <div class="bg-[#F8FAFC] border-2 border-dashed border-slate-200 rounded-2xl p-6 flex flex-col justify-between gap-6">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-lg flex items-center gap-2">
                            <Info class="size-5 text-slate-500 shrink-0" />
                            Ingin Ikut Berpartisipasi?
                        </h3>
                        <p class="text-xs leading-relaxed text-slate-500 mt-2.5">
                            Silakan buat akun posko Anda secara gratis melalui tombol di bawah ini. Anda dapat menguji semua fitur secara penuh tanpa batasan fitur premium selama masa pengujian.
                        </p>
                    </div>

                    <div class="flex flex-col gap-2.5">
                        <Link
                            v-if="$page.props.auth.user"
                            href="/dashboard"
                            class="rounded-xl bg-[#38BDF8] hover:bg-[#38BDF8]/90 py-3 text-sm font-bold text-white transition duration-200 text-center"
                        >
                            Buka Dashboard Pengujian
                        </Link>
                        <template v-else>
                            <Link
                                :href="register()"
                                class="rounded-xl bg-[#38BDF8] hover:bg-[#38BDF8]/90 py-3 text-sm font-bold text-white transition duration-200 text-center"
                            >
                                Daftar Akun Posko Baru
                            </Link>
                            <Link
                                :href="login()"
                                class="rounded-xl border border-slate-350 hover:bg-slate-50 py-3 text-sm font-bold text-slate-700 transition duration-200 text-center bg-white"
                            >
                                Masuk ke Akun
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-slate-900 py-16 text-slate-400 border-t border-slate-800/50">
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
                            <li><a href="/#fitur" class="hover:text-[#38BDF8] transition-colors">Fitur Utama</a></li>
                            <li><a href="/#pricing" class="hover:text-[#38BDF8] transition-colors">Harga Paket</a></li>
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
