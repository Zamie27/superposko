<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Sparkles, Check, QrCode, CreditCard, ShieldCheck, RefreshCw, Eye, AlertTriangle } from '@lucide/vue';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';

const props = defineProps<{
    preorderPromoActive: boolean;
    checkoutPaymentMethod: string;
    preorderPrice: number;
    preorderStrikePrice: number;
    packagePrice: number;
    packageStrikePrice: number;
    staticQrisUrl: string;
    tripayChannels: Array<{
        code: string;
        name: string;
        icon_url: string;
        group: string;
    }>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard Admin',
                href: '/admin/dashboard',
            },
            {
                title: 'Simulasi Halaman Pembayaran',
                href: '/admin/payment/test',
            },
        ],
    },
});

// Simulation state (defaults to matching the active database settings)
const previewMode = ref<'preorder' | 'qris' | 'tripay'>(
    props.preorderPromoActive
        ? 'preorder'
        : (props.checkoutPaymentMethod === 'qris_static' ? 'qris' : 'tripay')
);

const selectedMethod = ref<string>('QRIS');
const simulateUnpaid = ref(false);

const formattedPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(price);
};

const features = [
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
</script>

<template>
    <Head title="Simulasi Halaman Pembayaran - Admin" />

    <div class="min-h-screen bg-slate-50/50 py-8 px-4 sm:px-6 lg:px-8 font-sans">
        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Admin Control Board Panel -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                            <Eye class="size-5.5 text-sky-500" />
                            Simulasi Halaman Pembayaran & Preorder
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Gunakan panel ini untuk menguji dan melihat tampilan checkout yang akan dilihat oleh pengguna.
                        </p>
                    </div>
                    <Link href="/admin/prices" class="text-xs font-semibold text-sky-600 hover:text-sky-700 transition flex items-center gap-1">
                        Ubah Setelan Asli →
                    </Link>
                </div>

                <!-- Settings status check & toggle tabs -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center pt-2 border-t border-slate-100">
                    <div class="md:col-span-4 space-y-1.5">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Pengaturan Aktif Saat Ini</span>
                        <div class="flex items-center gap-2">
                            <span v-if="props.preorderPromoActive" class="inline-flex items-center rounded-full bg-purple-50 px-2 py-0.5 text-xs font-semibold text-purple-600 border border-purple-100">
                                Preorder Aktif
                            </span>
                            <span v-else class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-600 border border-emerald-100">
                                SaaS Utama Aktif
                            </span>
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">
                                {{ props.checkoutPaymentMethod === 'qris_static' ? 'QRIS Manual' : 'Tripay Online' }}
                            </span>
                        </div>
                    </div>

                    <div class="md:col-span-8 space-y-1.5">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Pilih Mode Simulasi Tampilan</span>
                        <div class="flex flex-wrap gap-2">
                            <button 
                                @click="previewMode = 'preorder'"
                                :class="previewMode === 'preorder' ? 'bg-purple-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition duration-200"
                            >
                                Promosi Preorder
                            </button>
                            <button 
                                @click="previewMode = 'qris'"
                                :class="previewMode === 'qris' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition duration-200"
                            >
                                Langganan - QRIS Statis
                            </button>
                            <button 
                                @click="previewMode = 'tripay'"
                                :class="previewMode === 'tripay' ? 'bg-sky-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition duration-200"
                            >
                                Langganan - Tripay Gateway
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Simulation Toggle for Unpaid Tripay Invoice -->
                <div v-if="previewMode === 'tripay'" class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-600">Simulasikan Status Tagihan Belum Dibayar (Tripay)</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" v-model="simulateUnpaid" class="sr-only peer">
                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                        <span class="ml-2 text-xs font-semibold text-slate-500">{{ simulateUnpaid ? 'Aktif (Tagihan Pending)' : 'Nonaktif (Pilih Metode Baru)' }}</span>
                    </label>
                </div>
            </div>

            <!-- Preview Sandbox Banner -->
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl flex gap-3 text-amber-900 text-xs items-center">
                <AlertTriangle class="size-4 shrink-0 text-amber-600" />
                <span>
                    <strong>Mode Simulasi:</strong> Formulir di bawah ini hanyalah pratinjau layout pengunggahan berkas dan tombol aksi pembayaran. Tidak ada transaksi riil yang dipicu.
                </span>
            </div>

            <!-- SIMULATED VIEWPORT CONTAINER -->
            <div class="border border-dashed border-slate-300 rounded-3xl p-2 bg-slate-100/50 shadow-inner">
                <!-- PREORDER PREVIEW -->
                <div v-if="previewMode === 'preorder'" class="bg-slate-50/50 rounded-2xl py-6 px-4 sm:px-6 lg:px-8">
                    <div class="max-w-4xl mx-auto space-y-8">
                        <div class="text-center space-y-3">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-3.5 py-1 text-xs font-semibold text-sky-600 border border-sky-100">
                                <Sparkles class="size-3.5 text-sky-500" /> Promosi Preorder SuperPosko
                            </span>
                            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                                Amankan Akses Posko KKN Lebih Awal
                            </h1>
                            <p class="text-xs text-slate-500 max-w-2xl mx-auto">
                                Dapatkan potongan harga khusus preorder sebelum website resmi diluncurkan. Layanan siap aktif untuk seluruh anggota kelompok Anda.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                            <div class="md:col-span-7 space-y-6">
                                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-6">
                                    <h2 class="text-sm font-bold text-slate-950 flex items-center gap-2">
                                        <ShieldCheck class="size-5 text-sky-500" />
                                        Fitur Premium Yang Didapatkan
                                    </h2>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div v-for="(feature, idx) in features" :key="idx" class="flex gap-2.5 items-center">
                                            <div class="flex-none rounded-full bg-sky-50 p-1 border border-sky-100">
                                                <Check class="size-3 text-sky-600 font-bold" />
                                            </div>
                                            <span class="text-xs font-medium text-slate-700 leading-relaxed">{{ feature }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-5 space-y-6">
                                <div class="bg-white rounded-2xl border border-slate-200/85 p-6 shadow-sm relative overflow-hidden flex flex-col space-y-5">
                                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-sky-400/20 to-transparent pointer-events-none rounded-bl-full"></div>
                                    <div class="space-y-1 pb-4 border-b border-slate-100">
                                        <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Preorder Promo</h3>
                                        <h4 class="text-base font-extrabold text-slate-900">Scan QRIS & Daftar</h4>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="text-[11px] text-slate-400 line-through font-medium">{{ formattedPrice(props.preorderStrikePrice) }}</span>
                                            <div class="flex items-baseline gap-1.5">
                                                <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ formattedPrice(props.preorderPrice) }}</span>
                                                <span class="text-xs text-slate-400 font-medium">/ 40 hari</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rounded-xl border p-2 bg-slate-50 flex flex-col items-center justify-center space-y-2">
                                        <img :src="props.staticQrisUrl" alt="QRIS SuperPosko" class="max-w-[150px] h-auto rounded-lg shadow-sm border bg-white" />
                                    </div>
                                    <form @submit.prevent class="space-y-3 pt-2">
                                        <div class="space-y-1">
                                            <label class="text-[11px] font-bold text-slate-700">Nama Lengkap</label>
                                            <input type="text" placeholder="Nama Lengkap" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs focus:outline-none" disabled />
                                        </div>
                                        <div class="space-y-1">
                                            <label class="text-[11px] font-bold text-slate-700">Nomor WhatsApp</label>
                                            <input type="text" placeholder="Contoh: 08123456789" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs focus:outline-none" disabled />
                                        </div>
                                        <Button type="button" class="w-full bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white font-bold py-2.5 rounded-lg transition text-xs shadow-sm cursor-not-allowed" disabled>
                                            Kirim Formulir Preorder (Simulasi)
                                        </Button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SUBSCRIPTION - QRIS PREVIEW -->
                <div v-else-if="previewMode === 'qris'" class="bg-slate-50/50 rounded-2xl py-6 px-4 sm:px-6 lg:px-8">
                    <div class="max-w-4xl mx-auto space-y-8">
                        <div class="text-center space-y-3">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-3.5 py-1 text-xs font-semibold text-sky-600 border border-sky-100">
                                <Sparkles class="size-3.5 text-sky-500" /> Layanan Premium SuperPosko
                            </span>
                            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                                Aktifkan Fitur Kolaborasi Posko KKN Anda
                            </h1>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                            <div class="md:col-span-7 space-y-6">
                                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-6">
                                    <h2 class="text-sm font-bold text-slate-950 flex items-center gap-2">
                                        <ShieldCheck class="size-5 text-sky-500" />
                                        Fitur Premium Yang Didapatkan (QRIS)
                                    </h2>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div v-for="(feature, idx) in features" :key="idx" class="flex gap-2.5 items-center">
                                            <div class="flex-none rounded-full bg-sky-50 p-1 border border-sky-100">
                                                <Check class="size-3 text-sky-600 font-bold" />
                                            </div>
                                            <span class="text-xs font-medium text-slate-700 leading-relaxed">{{ feature }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-5 space-y-6">
                                <div class="bg-white rounded-2xl border border-slate-200/85 p-6 shadow-sm relative overflow-hidden flex flex-col space-y-5">
                                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-sky-400/20 to-transparent pointer-events-none rounded-bl-full"></div>
                                    <div class="space-y-1 pb-4 border-b border-slate-100">
                                        <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Manual Activation</h3>
                                        <h4 class="text-base font-extrabold text-slate-900">Scan QRIS & Unggah Bukti</h4>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="text-[11px] text-slate-400 line-through font-medium">{{ formattedPrice(props.packageStrikePrice) }}</span>
                                            <div class="flex items-baseline gap-1.5">
                                                <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ formattedPrice(props.packagePrice) }}</span>
                                                <span class="text-xs text-slate-400 font-medium">/ 40 hari</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rounded-xl border p-2 bg-slate-50 flex flex-col items-center justify-center space-y-2">
                                        <img :src="props.staticQrisUrl" alt="QRIS SuperPosko" class="max-w-[150px] h-auto rounded-lg shadow-sm border bg-white" />
                                    </div>
                                    <form @submit.prevent class="space-y-3 pt-2">
                                        <div class="space-y-1">
                                            <label class="text-[11px] font-bold text-slate-700">Nama Lengkap</label>
                                            <input type="text" placeholder="Nama Lengkap" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs focus:outline-none" disabled />
                                        </div>
                                        <Button type="button" class="w-full bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white font-bold py-2.5 rounded-lg transition text-xs shadow-sm cursor-not-allowed" disabled>
                                            Kirim Pengajuan Langganan (Simulasi)
                                        </Button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SUBSCRIPTION - TRIPAY PREVIEW -->
                <div v-else-if="previewMode === 'tripay'" class="bg-slate-50/50 rounded-2xl py-6 px-4 sm:px-6 lg:px-8">
                    <div class="max-w-4xl mx-auto space-y-8">
                        <div class="text-center space-y-3">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-3.5 py-1 text-xs font-semibold text-sky-600 border border-sky-100">
                                <Sparkles class="size-3.5 text-sky-500" /> Layanan Premium SuperPosko
                            </span>
                            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                                Aktifkan Fitur Kolaborasi Posko KKN Anda
                            </h1>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                            <div class="md:col-span-7 space-y-6">
                                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-6">
                                    <h2 class="text-sm font-bold text-slate-950 flex items-center gap-2">
                                        <ShieldCheck class="size-5 text-sky-500" />
                                        Fitur Premium Yang Didapatkan (Tripay)
                                    </h2>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div v-for="(feature, idx) in features" :key="idx" class="flex gap-2.5 items-center">
                                            <div class="flex-none rounded-full bg-sky-50 p-1 border border-sky-100">
                                                <Check class="size-3 text-sky-600 font-bold" />
                                            </div>
                                            <span class="text-xs font-medium text-slate-700 leading-relaxed">{{ feature }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-5 space-y-6">
                                <div class="bg-white rounded-2xl border border-slate-200/85 p-6 shadow-sm relative overflow-hidden flex flex-col space-y-6">
                                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-sky-400/20 to-transparent pointer-events-none rounded-bl-full"></div>
                                    <div class="space-y-1 pb-4 border-b border-slate-100">
                                        <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Premium Access</h3>
                                        <h4 class="text-base font-extrabold text-slate-900">Paket Langganan Posko</h4>
                                    </div>

                                    <!-- Pending Payment Alert -->
                                    <div v-if="simulateUnpaid" class="bg-amber-50/70 border border-amber-200 rounded-xl p-4 text-xs text-amber-900 leading-relaxed flex gap-2.5">
                                        <AlertTriangle class="size-4 shrink-0 text-amber-600 mt-0.5" />
                                        <div>
                                            <span class="font-bold">Tagihan Belum Dibayar:</span> Anda memiliki transaksi Tripay yang sedang aktif (Ref: <code class="font-mono bg-amber-100 px-1 py-0.5 rounded text-[10px]">SUB-SIMULASI-REF</code>). Silakan selesaikan pembayaran Anda.
                                        </div>
                                    </div>

                                    <!-- Payment Method Selector -->
                                    <div class="space-y-3" :class="{ 'opacity-65 pointer-events-none': simulateUnpaid }">
                                        <label class="text-xs font-bold text-slate-700 block">Pilih Metode Pembayaran:</label>
                                        <div v-if="props.tripayChannels && props.tripayChannels.length > 0" class="space-y-2">
                                            <button
                                                v-for="channel in props.tripayChannels"
                                                :key="channel.code"
                                                type="button"
                                                @click="selectedMethod = channel.code"
                                                :class="[
                                                    'w-full flex items-center justify-between p-3 rounded-xl border text-left transition-all duration-200',
                                                    selectedMethod === channel.code
                                                        ? 'border-sky-500 bg-sky-50/40 ring-1 ring-sky-500 scale-[1.01]'
                                                        : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50/50'
                                                ]"
                                            >
                                                <div class="flex items-center gap-3">
                                                    <img :src="channel.icon_url" :alt="channel.name" class="h-4.5 w-auto object-contain bg-white rounded p-0.5 border" />
                                                    <div class="flex flex-col">
                                                        <span class="text-[11px] font-bold text-slate-900">{{ channel.name }}</span>
                                                        <span class="text-[9px] text-slate-400">{{ channel.group }}</span>
                                                    </div>
                                                </div>
                                                <div class="size-4 rounded-full border flex items-center justify-center" :class="selectedMethod === channel.code ? 'border-sky-500 bg-sky-500 text-white' : 'border-slate-300'">
                                                    <Check v-if="selectedMethod === channel.code" class="size-2.5 font-bold" />
                                                </div>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Pricing Display -->
                                    <div class="py-4 border-t border-b border-slate-100 space-y-2">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="text-[11px] text-slate-400 line-through font-medium">{{ formattedPrice(props.packageStrikePrice) }}</span>
                                            <div class="flex items-baseline gap-1.5">
                                                <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ formattedPrice(props.packagePrice) }}</span>
                                                <span class="text-xs text-slate-400 font-medium">/ 40 hari</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Button -->
                                    <div class="space-y-3">
                                        <Button
                                            type="button"
                                            :disabled="!selectedMethod && !simulateUnpaid"
                                            class="w-full bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white font-bold py-3.5 rounded-xl transition text-xs shadow-sm flex items-center justify-center gap-1.5"
                                        >
                                            <CreditCard class="size-4" />
                                            {{ simulateUnpaid ? 'Lanjutkan Pembayaran (Simulasi)' : 'Bayar Sekarang (Simulasi)' }}
                                        </Button>

                                        <button
                                            v-if="simulateUnpaid"
                                            @click="simulateUnpaid = false"
                                            type="button"
                                            class="w-full text-center text-xs font-semibold text-red-500 hover:text-red-600 transition py-1 hover:underline mt-2"
                                        >
                                            Batalkan & Ganti Metode Pembayaran (Simulasi)
                                        </button>

                                        <div class="flex items-center justify-center gap-1 text-[9px] text-slate-400">
                                            <ShieldCheck class="size-3 text-slate-400" />
                                            Simulasi transaksi aman oleh Tripay
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
