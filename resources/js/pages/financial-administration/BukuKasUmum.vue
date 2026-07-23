<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { 
    BookOpen, Calendar, Printer, Filter, ArrowDownRight, ArrowUpRight, Wallet, CheckCircle2, FileText, Download
} from '@lucide/vue';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';

interface BkuItem {
    no: number;
    id: number;
    date: string;
    title: string;
    description: string | null;
    category: string | null;
    type: string;
    payment_method: string;
    destination_payment_method: string | null;
    debit: number;
    kredit: number;
    running_balance: number;
    program_kerja: string | null;
    creator: string | null;
    receipt_path: string | null;
}

const props = defineProps<{
    bkuItems: BkuItem[];
    summary: {
        total_debit: number;
        total_kredit: number;
        saldo_akhir: number;
    };
    filters: {
        start_date: string;
        end_date: string;
    };
    poskoInfo: {
        name: string;
        ketua: string;
        bendahara: string;
    };
    canWrite: boolean;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Modul Keuangan', href: '#' },
            { title: 'Buku Kas Umum', href: '/financial-administration/buku-kas-umum' },
        ],
    },
});

const startDate = ref(props.filters.start_date);
const endDate = ref(props.filters.end_date);

const applyFilter = () => {
    router.get('/financial-administration/buku-kas-umum', {
        start_date: startDate.value,
        end_date: endDate.value,
    }, { preserveState: true, replace: true });
};

const resetFilter = () => {
    startDate.value = '';
    endDate.value = '';
    router.get('/financial-administration/buku-kas-umum', {}, { preserveState: true, replace: true });
};

const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
};

const printBku = () => {
    window.print();
};
</script>

<template>
    <Head title="Buku Kas Umum (BKU) - Modul Keuangan" />

    <div class="flex flex-col gap-6 p-4 md:p-6 w-full max-w-7xl mx-auto font-sans">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <BookOpen class="size-6 text-sky-500" />
                    Buku Kas Umum (BKU)
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Buku rekapitulasi kronologis seluruh aliran kas posko (Debet, Kredit, dan Saldo Kumulatif).
                </p>
            </div>

            <div class="flex items-center gap-2">
                <Button 
                    @click="printBku" 
                    class="bg-slate-800 hover:bg-slate-900 text-white font-semibold px-4 py-2 rounded-xl transition flex items-center gap-2 cursor-pointer"
                >
                    <Printer class="size-4" /> Cetak BKU
                </Button>
            </div>
        </div>

        <!-- Summary Cards (Screen view) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 print:hidden">
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Debet (Penerimaan)</p>
                    <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ formatRupiah(summary.total_debit) }}</p>
                </div>
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl text-emerald-600 dark:text-emerald-400">
                    <ArrowDownRight class="size-6" />
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Kredit (Pengeluaran)</p>
                    <p class="text-xl font-bold text-rose-600 dark:text-rose-400 mt-1">{{ formatRupiah(summary.total_kredit) }}</p>
                </div>
                <div class="p-3 bg-rose-50 dark:bg-rose-950/40 rounded-xl text-rose-600 dark:text-rose-400">
                    <ArrowUpRight class="size-6" />
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-sky-500 text-white shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-sky-100 uppercase tracking-wider">Saldo Kas Akhir</p>
                    <p class="text-xl font-bold text-white mt-1">{{ formatRupiah(summary.saldo_akhir) }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-xl text-white">
                    <Wallet class="size-6" />
                </div>
            </div>
        </div>

        <!-- Date Filter Toolbar (Screen View) -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 print:hidden">
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <Filter class="size-4 text-slate-400 shrink-0" />
                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Filter Periode:</span>
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <input 
                    v-model="startDate" 
                    type="date" 
                    class="rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-1.5 text-xs focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                />
                <span class="text-xs text-slate-400">s/d</span>
                <input 
                    v-model="endDate" 
                    type="date" 
                    class="rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-1.5 text-xs focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                />
                <Button @click="applyFilter" size="sm" class="bg-sky-500 hover:bg-sky-600 text-white text-xs rounded-xl cursor-pointer">
                    Terapkan
                </Button>
                <Button v-if="startDate || endDate" @click="resetFilter" variant="outline" size="sm" class="text-xs rounded-xl cursor-pointer">
                    Reset
                </Button>
            </div>
        </div>

        <!-- Printable Document Header (Only visible when printing) -->
        <div class="hidden print:block text-center border-b-2 border-slate-900 pb-4 mb-6">
            <h2 class="text-xl font-bold uppercase tracking-wide">BUKU KAS UMUM (BKU)</h2>
            <h3 class="text-base font-semibold text-slate-700 mt-1">{{ poskoInfo.name }}</h3>
            <p class="text-xs text-slate-500">Laporan Administrasi Keuangan Posko KKN</p>
        </div>

        <!-- BKU Table -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-xs">
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left text-xs min-w-max">
                    <thead class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-bold uppercase">
                        <tr>
                            <th class="py-3 px-4 text-center w-12">No</th>
                            <th class="py-3 px-4 w-28">Tanggal</th>
                            <th class="py-3 px-4">Uraian Transaksi</th>
                            <th class="py-3 px-4 w-28">Kategori</th>
                            <th class="py-3 px-4 text-right w-32">Debet (Rp)</th>
                            <th class="py-3 px-4 text-right w-32">Kredit (Rp)</th>
                            <th class="py-3 px-4 text-right w-36">Saldo (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-850">
                        <tr v-if="bkuItems.length === 0">
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada catatan transaksi pada Buku Kas Umum.</td>
                        </tr>
                        <tr 
                            v-for="item in bkuItems" 
                            :key="item.id" 
                            class="hover:bg-slate-50/50 dark:hover:bg-slate-850/50 transition duration-150"
                        >
                            <td class="py-3 px-4 text-center font-medium text-slate-400">{{ item.no }}</td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-300 font-mono">{{ item.date }}</td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ item.title }}</div>
                                <div v-if="item.description" class="text-[11px] text-slate-400 mt-0.5">{{ item.description }}</div>
                                <div v-if="item.program_kerja" class="text-[10px] text-sky-600 font-semibold mt-0.5">Proker: {{ item.program_kerja }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                    {{ item.category || item.type }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ item.debit > 0 ? formatRupiah(item.debit) : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-semibold text-rose-600 dark:text-rose-400">
                                {{ item.kredit > 0 ? formatRupiah(item.kredit) : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 dark:text-white">
                                {{ formatRupiah(item.running_balance) }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-slate-50 dark:bg-slate-950 font-bold border-t border-slate-200 dark:border-slate-800">
                        <tr>
                            <td colspan="4" class="py-3 px-4 text-right uppercase text-slate-500">Total BKU</td>
                            <td class="py-3 px-4 text-right font-mono text-emerald-600 dark:text-emerald-400">{{ formatRupiah(summary.total_debit) }}</td>
                            <td class="py-3 px-4 text-right font-mono text-rose-600 dark:text-rose-400">{{ formatRupiah(summary.total_kredit) }}</td>
                            <td class="py-3 px-4 text-right font-mono text-sky-600 dark:text-sky-400">{{ formatRupiah(summary.saldo_akhir) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Printable Signature Block -->
        <div class="hidden print:flex justify-between items-center mt-12 px-8 text-xs text-center">
            <div>
                <p>Mengetahui,</p>
                <p class="font-bold mt-1">Ketua Posko</p>
                <div class="h-16"></div>
                <p class="font-bold underline">{{ poskoInfo.ketua }}</p>
            </div>
            <div>
                <p>Dibuat Oleh,</p>
                <p class="font-bold mt-1">Bendahara Posko</p>
                <div class="h-16"></div>
                <p class="font-bold underline">{{ poskoInfo.bendahara }}</p>
            </div>
        </div>
    </div>
</template>
