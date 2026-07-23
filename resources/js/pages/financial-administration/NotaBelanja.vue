<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ShoppingBag, Printer, Eye, Download, Image as ImageIcon, Box, ClipboardList } from '@lucide/vue';
import { ref } from 'vue';

interface NotaItem {
    id: number;
    type: 'logistic' | 'inventory' | 'finance_expense';
    name: string;
    quantity: number;
    unit: string;
    unit_price: number;
    total_price: number;
    date: string;
    notes: string | null;
    receipt_path: string | null;
}

const props = defineProps<{
    notaItems: NotaItem[];
    totalShopping: number;
    canWrite: boolean;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Modul Keuangan', href: '#' },
            { title: 'Nota Belanja', href: '/financial-administration/nota-belanja' },
        ],
    },
});

const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
};

const printPage = () => {
    window.print();
};
</script>

<template>
    <Head title="Nota Belanja - Modul Keuangan" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-7xl mx-auto font-sans">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <ShoppingBag class="size-6 text-amber-500" />
                    Arsip Nota Belanja & Pembelian Barang
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Arsip berkas rincian nota belanja yang terhubung langsung ke Logistik, Inventaris, dan Pengeluaran Posko.
                </p>
            </div>

            <button @click="printPage" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold px-4 py-2 rounded-xl transition flex items-center gap-2 cursor-pointer text-xs">
                <Printer class="size-4" /> Cetak Nota Belanja
            </button>
        </div>

        <!-- Metric Card -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between print:hidden">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Pembelian Logistik & Inventaris</p>
                <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ formatRupiah(totalShopping) }}</p>
            </div>
            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 text-amber-600 rounded-xl">
                <ShoppingBag class="size-7" />
            </div>
        </div>

        <!-- Table -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-bold uppercase">
                        <tr>
                            <th class="py-3 px-4 w-28">Tanggal</th>
                            <th class="py-3 px-4">Nama Barang / Belanja</th>
                            <th class="py-3 px-4 w-28">Sumber Modul</th>
                            <th class="py-3 px-4 text-center w-24">Jumlah</th>
                            <th class="py-3 px-4 text-right w-32">Harga Satuan</th>
                            <th class="py-3 px-4 text-right w-36">Total Belanja</th>
                            <th class="py-3 px-4 text-center w-28 print:hidden">Berkas Nota</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-850">
                        <tr v-if="notaItems.length === 0">
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada nota belanja pembelian yang dicatat.</td>
                        </tr>
                        <tr v-for="item in notaItems" :key="item.id + item.type" class="hover:bg-slate-50/50 dark:hover:bg-slate-850/50">
                            <td class="py-3 px-4 font-mono text-slate-600 dark:text-slate-300">{{ item.date }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ item.name }}</div>
                                <div v-if="item.notes" class="text-[11px] text-slate-400 mt-0.5">{{ item.notes }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span 
                                    :class="[
                                        'px-2 py-0.5 rounded-full text-[10px] font-semibold flex items-center gap-1 w-fit',
                                        item.type === 'logistic' ? 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-400' :
                                        item.type === 'inventory' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400' :
                                        'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
                                    ]"
                                >
                                    <component :is="item.type === 'logistic' ? ClipboardList : Box" class="size-3" />
                                    {{ item.type === 'logistic' ? 'Logistik' : item.type === 'inventory' ? 'Inventaris' : 'Kas Pengeluaran' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-700 dark:text-slate-300">
                                {{ item.quantity }} {{ item.unit }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-slate-600 dark:text-slate-400">
                                {{ item.unit_price > 0 ? formatRupiah(item.unit_price) : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-amber-600 dark:text-amber-400">
                                {{ formatRupiah(item.total_price) }}
                            </td>
                            <td class="py-3 px-4 text-center print:hidden">
                                <a v-if="item.receipt_path" :href="item.receipt_path" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-[10px] inline-flex items-center gap-1 transition">
                                    <Eye class="size-3" /> Lihat Nota
                                </a>
                                <span v-else class="text-slate-400 text-[10px]italic">-</span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-slate-50 dark:bg-slate-950 font-bold border-t border-slate-200 dark:border-slate-800">
                        <tr>
                            <td colspan="5" class="py-3 px-4 text-right uppercase text-slate-500">Total Belanja</td>
                            <td class="py-3 px-4 text-right font-mono text-amber-600 dark:text-amber-400">{{ formatRupiah(totalShopping) }}</td>
                            <td class="print:hidden"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</template>
