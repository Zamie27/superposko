<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ArrowDownRight, Wallet, Printer, Filter, Coins } from '@lucide/vue';
import { Button } from '@/components/ui/button';

interface IncomeItem {
    id: number;
    date: string;
    title: string;
    description: string | null;
    category: string;
    payment_method: string;
    amount: number;
    creator: string | null;
    receipt_path: string | null;
}

const props = defineProps<{
    incomes: IncomeItem[];
    totalIncome: number;
    categorySummary: Record<string, number>;
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
            { title: 'Buku Penerimaan Dana', href: '/financial-administration/buku-penerimaan-dana' },
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
    <Head title="Buku Penerimaan Dana - Modul Keuangan" />

    <div class="flex flex-col gap-6 p-4 md:p-6 w-full max-w-7xl mx-auto font-sans">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <ArrowDownRight class="size-6 text-emerald-500" />
                    Buku Penerimaan Dana
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Buku khusus rekapitulasi seluruh sumber dana masuk (Iuran, Donasi, Sponsor, Dana Kampus, dll).
                </p>
            </div>

            <Button @click="printPage" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold px-4 py-2 rounded-xl transition flex items-center gap-2 cursor-pointer">
                <Printer class="size-4" /> Cetak Buku Penerimaan
            </Button>
        </div>

        <!-- Summary Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 print:hidden">
            <div class="p-5 rounded-2xl bg-emerald-500 text-white shadow-xs col-span-1 md:col-span-2 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-emerald-100 uppercase tracking-wider">Total Dana Diterima</p>
                    <p class="text-2xl font-bold text-white mt-1">{{ formatRupiah(totalIncome) }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-xl text-white">
                    <Coins class="size-7" />
                </div>
            </div>

            <div v-for="(val, method) in methodSummary" :key="method" class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ method }}</p>
                <p class="text-lg font-bold text-slate-900 dark:text-white mt-1">{{ formatRupiah(val) }}</p>
            </div>
        </div>

        <!-- Category Distribution -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs print:hidden">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3">Ringkasan per Kategori Penerimaan</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div v-for="(amount, cat) in categorySummary" :key="cat" class="p-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850">
                    <p class="text-xs font-semibold text-slate-500 truncate">{{ cat }}</p>
                    <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ formatRupiah(amount) }}</p>
                </div>
            </div>
        </div>

        <!-- Printable Document Header -->
        <div class="hidden print:block text-center border-b-2 border-slate-900 pb-4 mb-6">
            <h2 class="text-xl font-bold uppercase tracking-wide">BUKU PENERIMAAN DANA</h2>
            <h3 class="text-base font-semibold text-slate-700 mt-1">{{ poskoInfo.name }}</h3>
        </div>

        <!-- Table -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-xs">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left text-xs min-w-max">
                    <thead class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-bold uppercase">
                        <tr>
                            <th class="py-3 px-4 w-28">Tanggal</th>
                            <th class="py-3 px-4">Uraian Penerimaan</th>
                            <th class="py-3 px-4 w-32">Kategori</th>
                            <th class="py-3 px-4 w-28">Metode</th>
                            <th class="py-3 px-4 text-right w-36">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-850">
                        <tr v-if="incomes.length === 0">
                            <td colspan="5" class="py-8 text-center text-slate-400">Belum ada data penerimaan dana yang dicatat.</td>
                        </tr>
                        <tr v-for="item in incomes" :key="item.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-850/50">
                            <td class="py-3 px-4 font-mono text-slate-600 dark:text-slate-300">{{ item.date }}</td>
                            <td class="py-3 px-4 max-w-[240px] sm:max-w-xs md:max-w-sm">
                                <div class="font-bold text-slate-900 dark:text-white truncate" :title="item.title">{{ item.title }}</div>
                                <div v-if="item.description" class="text-[11px] text-slate-400 mt-0.5 truncate" :title="item.description">{{ item.description }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400">
                                    {{ item.category }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-600 dark:text-slate-300">{{ item.payment_method }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                {{ formatRupiah(item.amount) }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-slate-50 dark:bg-slate-950 font-bold border-t border-slate-200 dark:border-slate-800">
                        <tr>
                            <td colspan="4" class="py-3 px-4 text-right uppercase text-slate-500">Total Seluruh Penerimaan</td>
                            <td class="py-3 px-4 text-right font-mono text-emerald-600 dark:text-emerald-400">{{ formatRupiah(totalIncome) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</template>
