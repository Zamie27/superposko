<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ArrowUpRight, Wallet, Printer, Filter, ShoppingCart } from '@lucide/vue';
import { Button } from '@/components/ui/button';

interface ExpenseItem {
    id: number;
    date: string;
    title: string;
    description: string | null;
    category: string;
    payment_method: string;
    amount: number;
    program_kerja: string | null;
    creator: string | null;
    receipt_path: string | null;
}

const props = defineProps<{
    expenses: ExpenseItem[];
    totalExpense: number;
    categorySummary: Record<string, number>;
    prokerSummary: Record<string, number>;
    methodSummary: Record<string, number>;
    poskoInfo: {
        name: string;
    };
    canWrite: boolean;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Modul Keuangan', href: '#' },
            { title: 'Buku Pengeluaran Dana', href: '/financial-administration/buku-pengeluaran-dana' },
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
    <Head title="Buku Pengeluaran Dana - Modul Keuangan" />

    <div class="flex flex-col gap-6 p-4 md:p-6 w-full max-w-7xl mx-auto font-sans">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <ArrowUpRight class="size-6 text-rose-500" />
                    Buku Pengeluaran Dana
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Buku khusus rincian pengeluaran dana operasional posko dan belanja program kerja.
                </p>
            </div>

            <Button @click="printPage" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold px-4 py-2 rounded-xl transition flex items-center gap-2 cursor-pointer">
                <Printer class="size-4" /> Cetak Buku Pengeluaran
            </Button>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 print:hidden">
            <div class="p-5 rounded-2xl bg-rose-500 text-white shadow-xs col-span-1 md:col-span-2 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-rose-100 uppercase tracking-wider">Total Realisasi Pengeluaran</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ formatRupiah(totalExpense) }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-xl text-white">
                    <ShoppingCart class="size-7" />
                </div>
            </div>

            <div v-for="(val, method) in methodSummary" :key="method" class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pengeluaran {{ method }}</p>
                <p class="text-lg font-bold text-slate-900 dark:text-white mt-1">{{ formatRupiah(val) }}</p>
            </div>
        </div>

        <!-- Proker & Category Summary Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 print:hidden">
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3">Ringkasan per Program Kerja</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                    <div v-for="(amount, proker) in prokerSummary" :key="proker" class="flex justify-between items-center p-2 rounded-lg bg-slate-50 dark:bg-slate-950 text-xs">
                        <span class="font-medium text-slate-700 dark:text-slate-300 truncate max-w-[200px]">{{ proker }}</span>
                        <span class="font-bold text-rose-600 dark:text-rose-400">{{ formatRupiah(amount) }}</span>
                    </div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3">Ringkasan per Kategori</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                    <div v-for="(amount, cat) in categorySummary" :key="cat" class="flex justify-between items-center p-2 rounded-lg bg-slate-50 dark:bg-slate-950 text-xs">
                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ cat }}</span>
                        <span class="font-bold text-rose-600 dark:text-rose-400">{{ formatRupiah(amount) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Printable Header -->
        <div class="hidden print:block text-center border-b-2 border-slate-900 pb-4 mb-6">
            <h2 class="text-xl font-bold uppercase tracking-wide">BUKU PENGELUARAN DANA</h2>
            <h3 class="text-base font-semibold text-slate-700 mt-1">{{ poskoInfo.name }}</h3>
        </div>

        <!-- Table -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-xs">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left text-xs min-w-max">
                    <thead class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-bold uppercase">
                        <tr>
                            <th class="py-3 px-4 w-28">Tanggal</th>
                            <th class="py-3 px-4">Uraian Pengeluaran</th>
                            <th class="py-3 px-4 w-32">Kategori</th>
                            <th class="py-3 px-4 w-28">Metode</th>
                            <th class="py-3 px-4 text-right w-36">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-850">
                        <tr v-if="expenses.length === 0">
                            <td colspan="5" class="py-8 text-center text-slate-400">Belum ada data pengeluaran dana yang dicatat.</td>
                        </tr>
                        <tr v-for="item in expenses" :key="item.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-850/50">
                            <td class="py-3 px-4 font-mono text-slate-600 dark:text-slate-300">{{ item.date }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ item.title }}</div>
                                <div v-if="item.description" class="text-[11px] text-slate-400 mt-0.5">{{ item.description }}</div>
                                <div v-if="item.program_kerja" class="text-[10px] text-sky-600 font-semibold mt-0.5">Proker: {{ item.program_kerja }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400">
                                    {{ item.category }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-600 dark:text-slate-300">{{ item.payment_method }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-rose-600 dark:text-rose-400">
                                {{ formatRupiah(item.amount) }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-slate-50 dark:bg-slate-950 font-bold border-t border-slate-200 dark:border-slate-800">
                        <tr>
                            <td colspan="4" class="py-3 px-4 text-right uppercase text-slate-500">Total Seluruh Pengeluaran</td>
                            <td class="py-3 px-4 text-right font-mono text-rose-600 dark:text-rose-400">{{ formatRupiah(totalExpense) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</template>
