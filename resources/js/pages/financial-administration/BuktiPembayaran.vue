<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { FileText, CheckCircle2, AlertCircle, Image as ImageIcon, Eye, Download, X } from '@lucide/vue';
import { ref, computed } from 'vue';

interface ReceiptItem {
    id: number;
    title: string;
    type: string;
    amount: number;
    date: string;
    payment_method: string;
    category: string | null;
    creator: string | null;
    receipt_path: string | null;
    has_receipt: boolean;
}

const props = defineProps<{
    items: ReceiptItem[];
    metrics: {
        total_count: number;
        has_receipt_count: number;
        missing_receipt_count: number;
    };
    canWrite: boolean;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Modul Keuangan', href: '#' },
            { title: 'Bukti Pembayaran', href: '/financial-administration/bukti-pembayaran' },
        ],
    },
});

const filterType = ref<'all' | 'has' | 'missing'>('all');
const previewImageUrl = ref<string | null>(null);

const filteredItems = computed(() => {
    if (filterType.value === 'has') {
        return props.items.filter(i => i.has_receipt);
    }
    if (filterType.value === 'missing') {
        return props.items.filter(i => !i.has_receipt);
    }
    return props.items;
});

const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
};

const openPreview = (url: string) => {
    previewImageUrl.value = url;
};

const closePreview = () => {
    previewImageUrl.value = null;
};
</script>

<template>
    <Head title="Bukti Pembayaran - Modul Keuangan" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-7xl mx-auto font-sans">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <FileText class="size-6 text-indigo-500" />
                    Bukti Pembayaran & Audit Transaksi
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Galeri dan audit kelengkapan berkas bukti transaksi (Struk, Transfer Slip, Nota Pembelian).
                </p>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Seluruh Transaksi</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ metrics.total_count }} Transaksi</p>
                </div>
                <div class="p-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl">
                    <FileText class="size-6" />
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-emerald-500 uppercase tracking-wider">Memiliki Bukti Resi</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ metrics.has_receipt_count }} Berkas</p>
                </div>
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-xl">
                    <CheckCircle2 class="size-6" />
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-amber-500 uppercase tracking-wider">Belum Ada Bukti</p>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ metrics.missing_receipt_count }} Transaksi</p>
                </div>
                <div class="p-3 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-xl">
                    <AlertCircle class="size-6" />
                </div>
            </div>
        </div>

        <!-- Filter Pills -->
        <div class="flex gap-2">
            <button 
                @click="filterType = 'all'"
                :class="[
                    'px-4 py-2 text-xs font-semibold rounded-xl border transition cursor-pointer',
                    filterType === 'all' 
                        ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' 
                        : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-800'
                ]"
            >
                Semua Transaksi ({{ metrics.total_count }})
            </button>
            <button 
                @click="filterType = 'has'"
                :class="[
                    'px-4 py-2 text-xs font-semibold rounded-xl border transition cursor-pointer',
                    filterType === 'has' 
                        ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' 
                        : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-800'
                ]"
            >
                Ada Bukti ({{ metrics.has_receipt_count }})
            </button>
            <button 
                @click="filterType = 'missing'"
                :class="[
                    'px-4 py-2 text-xs font-semibold rounded-xl border transition cursor-pointer',
                    filterType === 'missing' 
                        ? 'bg-amber-600 text-white border-amber-600 shadow-xs' 
                        : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-800'
                ]"
            >
                Belum Ada Bukti ({{ metrics.missing_receipt_count }})
            </button>
        </div>

        <!-- Gallery / Table Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div 
                v-for="item in filteredItems" 
                :key="item.id" 
                class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between"
            >
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-mono text-slate-400">{{ item.date }}</span>
                        <span 
                            :class="[
                                'px-2 py-0.5 rounded-full text-[10px] font-bold flex items-center gap-1',
                                item.has_receipt 
                                    ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-850'
                                    : 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-850'
                            ]"
                        >
                            <component :is="item.has_receipt ? CheckCircle2 : AlertCircle" class="size-3" />
                            {{ item.has_receipt ? 'Ada Bukti' : 'Belum Ada Bukti' }}
                        </span>
                    </div>

                    <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-1 leading-snug">{{ item.title }}</h4>
                    <p class="text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400 mb-3">{{ formatRupiah(item.amount) }}</p>
                    
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 space-y-0.5">
                        <p>Kategori: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ item.category || item.type }}</span></p>
                        <p>Metode: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ item.payment_method }}</span></p>
                    </div>
                </div>

                <!-- Receipt Image Card Preview -->
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-850">
                    <div v-if="item.has_receipt && item.receipt_path" class="relative group rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-950 h-32 border border-slate-200 dark:border-slate-800">
                        <img :src="item.receipt_path" :alt="item.title" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                        <div class="absolute inset-0 bg-slate-900/50 opacity-0 group-hover:opacity-100 transition duration-200 flex items-center justify-center gap-2">
                            <button @click="openPreview(item.receipt_path)" class="p-2 bg-white text-slate-900 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                                <Eye class="size-4" />
                            </button>
                            <a :href="item.receipt_path" target="_blank" download class="p-2 bg-white text-slate-900 rounded-lg hover:bg-slate-100 transition">
                                <Download class="size-4" />
                            </a>
                        </div>
                    </div>

                    <div v-else class="h-20 rounded-xl bg-slate-50 dark:bg-slate-950 border border-dashed border-slate-200 dark:border-slate-800 flex items-center justify-center text-slate-400 text-xs">
                        <ImageIcon class="size-4 mr-1.5 opacity-60" /> Tanpa Lampiran Resi
                    </div>
                </div>
            </div>
        </div>

        <!-- Fullscreen Preview Modal -->
        <div v-if="previewImageUrl" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-xs">
            <div class="relative max-w-3xl max-h-[90vh] bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-2xl flex flex-col">
                <div class="p-3 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-950">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Pratinjau Bukti Pembayaran</span>
                    <button @click="closePreview" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg cursor-pointer">
                        <X class="size-5" />
                    </button>
                </div>
                <div class="p-4 overflow-auto flex items-center justify-center">
                    <img :src="previewImageUrl" alt="Preview Bukti" class="max-h-[75vh] object-contain rounded-lg" />
                </div>
            </div>
        </div>
    </div>
</template>
