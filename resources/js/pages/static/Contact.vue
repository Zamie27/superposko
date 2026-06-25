<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Mail, Phone, MessageCircle, LifeBuoy } from '@lucide/vue';
import { login, register, dashboard } from '@/routes';
import PublicFooter from '@/components/PublicFooter.vue';

const props = defineProps<{
    footerAbout: string;
    footerEmail: string;
    footerPhone: string;
    footerCopyright: string;
    socialInstagram: string;
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

const waLink = computed(() => {
    if (!props.footerPhone) return '#';
    let clean = props.footerPhone.replace(/[^0-9]/g, '');
    if (clean.startsWith('0')) {
        clean = '62' + clean.substring(1);
    }
    return `https://wa.me/${clean}`;
});
</script>

<template>
    <Head title="Kontak Support - SuperPosko" />

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
        <main class="flex-grow max-w-5xl w-full mx-auto px-6 py-12 md:py-20 font-sans">
            <div class="text-center space-y-4 mb-16">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#38BDF8]/10 px-3.5 py-1.5 text-xs font-semibold text-sky-600">
                    📞 Customer Support & Hubungi Kami
                </span>
                <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 tracking-tight">
                    Kontak Support
                </h1>
                <p class="text-base text-slate-500 max-w-xl mx-auto">
                    Butuh bantuan teknis atau memiliki pertanyaan tentang layanan kami? Tim support SuperPosko siap melayani Anda.
                </p>
            </div>

            <!-- Contact Grid Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                <!-- Direct Support Channels -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-8 shadow-sm space-y-6">
                    <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2.5">
                        <MessageCircle class="w-6 h-6 text-sky-500" /> Saluran Komunikasi Utama
                    </h2>
                    <p class="text-sm text-slate-500">
                        Hubungi kami secara langsung melalui salah satu saluran di bawah ini untuk respon yang cepat dan terarah.
                    </p>

                    <div class="space-y-4">
                        <!-- WhatsApp Card -->
                        <a :href="waLink" target="_blank" rel="noopener noreferrer" class="flex items-center gap-4 p-4 rounded-xl border border-emerald-100 bg-emerald-50/50 hover:bg-emerald-50 transition duration-200 group">
                            <div class="p-3 bg-emerald-500 rounded-lg text-white">
                                <MessageCircle class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">WhatsApp Support</h3>
                                <p class="text-xs text-slate-500">{{ formattedPhone }}</p>
                            </div>
                        </a>

                        <!-- Email Card -->
                        <a :href="`mailto:${footerEmail}`" class="flex items-center gap-4 p-4 rounded-xl border border-sky-100 bg-sky-50/50 hover:bg-sky-50 transition duration-200 group">
                            <div class="p-3 bg-sky-500 rounded-lg text-white">
                                <Mail class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 group-hover:text-sky-600 transition-colors">Email Support</h3>
                                <p class="text-xs text-slate-500">{{ footerEmail }}</p>
                            </div>
                        </a>

                        <!-- Phone Card -->
                        <a :href="`tel:${footerPhone}`" class="flex items-center gap-4 p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-100 transition duration-200 group">
                            <div class="p-3 bg-slate-600 rounded-lg text-white">
                                <Phone class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 group-hover:text-slate-800 transition-colors">Hubungi Lewat Telepon</h3>
                                <p class="text-xs text-slate-500">{{ formattedPhone }}</p>
                            </div>
                        </a>

                        <!-- Instagram Card -->
                        <a :href="`https://instagram.com/${socialInstagram}`" target="_blank" rel="noopener noreferrer" class="flex items-center gap-4 p-4 rounded-xl border border-pink-100 bg-pink-50/50 hover:bg-pink-50 transition duration-200 group">
                            <div class="p-3 bg-gradient-to-tr from-yellow-500 via-pink-500 to-purple-600 rounded-lg text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 group-hover:text-pink-600 transition-colors">Instagram Resmi</h3>
                                <p class="text-xs text-slate-500">@{{ socialInstagram }}</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- App Feature Support Bubble Guide -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-8 shadow-sm flex flex-col justify-between">
                    <div class="space-y-6">
                        <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2.5">
                            <LifeBuoy class="w-6 h-6 text-[#38BDF8]" /> Bubble Layanan & Laporan Bug
                        </h2>
                        <p class="text-sm text-slate-655 leading-relaxed">
                            Jika Anda sudah masuk (login) ke dalam sistem dashboard SuperPosko dan mengalami kendala fungsionalitas, error halaman, atau kegagalan transaksi, Anda tidak perlu membuka halaman kontak ini kembali.
                        </p>
                        
                        <div class="p-5 rounded-xl bg-sky-50 border border-sky-100 text-sky-800 space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="p-1 bg-sky-500 rounded text-white shrink-0 mt-0.5">
                                    <LifeBuoy class="w-4 h-4" />
                                </div>
                                <div class="text-xs space-y-1">
                                    <span class="font-bold">Gunakan Bubble Layanan di Dasbor</span>
                                    <p class="leading-relaxed">
                                        Anda juga bisa menggunakan bubble layanan (tombol melayang berwarna biru dengan ikon headset / lapor bug di sudut kanan bawah layar Anda) jika memiliki kendala saat menggunakan platform.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-slate-400">
                            Fitur bubble layanan terhubung langsung ke dashboard pengelolaan pengaduan tim pengembang untuk penanganan prioritas yang lebih cepat.
                        </p>
                    </div>

                    <div class="pt-6 border-t border-slate-100 mt-6 flex justify-center">
                        <Link href="/panduan" class="text-xs font-bold text-sky-600 hover:text-sky-700 transition duration-150 flex items-center gap-1.5">
                            Buka Dokumentasi & Panduan Posko &rarr;
                        </Link>
                    </div>
                </div>
            </div>
        </main>

        <!-- Consistent Public Footer -->
        <PublicFooter 
            :footerAbout="footerAbout"
            :footerEmail="footerEmail"
            :footerPhone="footerPhone"
            :footerCopyright="footerCopyright"
        />
    </div>
</template>
