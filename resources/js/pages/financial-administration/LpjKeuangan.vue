<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { FileText, Printer, CheckCircle2, TrendingUp, Award, Layers } from '@lucide/vue';
import { Button } from '@/components/ui/button';

interface ProkerLpj {
    id: number;
    name: string;
    planned_budget: number;
    total_allocated: number;
    total_spent: number;
    balance: number;
}

const props = defineProps<{
    summary: {
        total_income: number;
        total_general_expense: number;
        total_proker_expense: number;
        total_expense: number;
        final_balance: number;
    };
    prokerBreakdown: ProkerLpj[];
    poskoInfo: {
        name: string;
        ketua: string;
        bendahara: string;
        dpl: string;
    };
    canWrite: boolean;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Modul Keuangan', href: '#' },
            { title: 'LPJ Keuangan', href: '/financial-administration/lpj-keuangan' },
        ],
    },
});

const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
};

const printLpj = () => {
    window.print();
};
</script>

<template>
    <Head title="LPJ Keuangan Posko - Modul Keuangan" />

    <div class="flex flex-col gap-6 p-4 md:p-6 w-full max-w-5xl mx-auto font-sans">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <Award class="size-6 text-sky-500" />
                    Laporan Pertanggungjawaban (LPJ) Keuangan
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Dokumen pertanggungjawaban keuangan resmi posko KKN untuk diserahkan ke DPL dan Kampus.
                </p>
            </div>

            <Button @click="printLpj" class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-5 py-2.5 rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs">
                <Printer class="size-4" /> Cetak Berkas LPJ
            </Button>
        </div>

        <!-- Metric Cards (Screen View) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 print:hidden">
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Penerimaan Dana</p>
                <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ formatRupiah(summary.total_income) }}</p>
            </div>
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Realisasi Pengeluaran</p>
                <p class="text-xl font-bold text-rose-600 dark:text-rose-400 mt-1">{{ formatRupiah(summary.total_expense) }}</p>
            </div>
            <div class="p-5 rounded-2xl bg-sky-500 text-white shadow-xs">
                <p class="text-xs font-semibold text-sky-100 uppercase tracking-wider">Sisa Saldo Kas Final</p>
                <p class="text-xl font-bold text-white mt-1">{{ formatRupiah(summary.final_balance) }}</p>
            </div>
        </div>

        <!-- Formal LPJ Document Paper (Printable) -->
        <div class="p-8 rounded-2xl bg-white text-slate-900 border border-slate-200 shadow-lg space-y-6 print:shadow-none print:border-none print:p-0">
            <!-- Kop LPJ Header -->
            <div class="text-center border-b-2 border-slate-900 pb-4">
                <h2 class="text-xl font-extrabold uppercase tracking-wide text-slate-900">LAPORAN PERTANGGUNGJAWABAN (LPJ) KEUANGAN</h2>
                <h3 class="text-base font-bold text-slate-700 mt-1">{{ poskoInfo.name }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Kuliah Kerja Nyata (KKN) - Dokumen Resmi Administrasi Posko</p>
            </div>

            <!-- Section 1: Ringkasan Penerimaan & Pengeluaran -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 bg-slate-100 p-2 rounded-lg border-l-4 border-slate-900">
                    I. Ringkasan Kas Posko
                </h3>
                <table class="w-full text-xs text-left border border-slate-200">
                    <tbody>
                        <tr class="border-b border-slate-200">
                            <td class="p-2.5 font-semibold text-slate-700">Total Penerimaan (Masuk)</td>
                            <td class="p-2.5 text-right font-mono font-bold text-emerald-700">{{ formatRupiah(summary.total_income) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="p-2.5 font-semibold text-slate-700">Pengeluaran Operasional Umum Posko</td>
                            <td class="p-2.5 text-right font-mono font-semibold text-rose-600">{{ formatRupiah(summary.total_general_expense) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="p-2.5 font-semibold text-slate-700">Realisasi Belanja Program Kerja</td>
                            <td class="p-2.5 text-right font-mono font-semibold text-rose-600">{{ formatRupiah(summary.total_proker_expense) }}</td>
                        </tr>
                        <tr class="border-b border-slate-200 bg-rose-50/50">
                            <td class="p-2.5 font-bold text-slate-900 uppercase">Total Seluruh Realisasi Pengeluaran</td>
                            <td class="p-2.5 text-right font-mono font-bold text-rose-700">{{ formatRupiah(summary.total_expense) }}</td>
                        </tr>
                        <tr class="bg-sky-50 font-bold">
                            <td class="p-2.5 text-slate-900 uppercase">Sisa Saldo Kas Posko</td>
                            <td class="p-2.5 text-right font-mono text-sky-800 text-sm">{{ formatRupiah(summary.final_balance) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Section 2: Realisasi Anggaran per Program Kerja -->
            <div class="space-y-3 pt-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 bg-slate-100 p-2 rounded-lg border-l-4 border-slate-900">
                    II. Rincian Realisasi Keuangan per Program Kerja
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left border border-slate-200">
                        <thead class="bg-slate-100 font-bold uppercase text-slate-700 border-b border-slate-200">
                            <tr>
                                <th class="p-2.5">Nama Program Kerja</th>
                                <th class="p-2.5 text-right">Target Anggaran</th>
                                <th class="p-2.5 text-right">Dana Dialokasikan</th>
                                <th class="p-2.5 text-right">Realisasi Belanja</th>
                                <th class="p-2.5 text-right">Sisa / Selisih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr v-if="prokerBreakdown.length === 0">
                                <td colspan="5" class="p-4 text-center text-slate-400">Belum ada Program Kerja yang terdaftar.</td>
                            </tr>
                            <tr v-for="proker in prokerBreakdown" :key="proker.id">
                                <td class="p-2.5 font-bold text-slate-900">{{ proker.name }}</td>
                                <td class="p-2.5 text-right font-mono text-slate-600">{{ formatRupiah(proker.planned_budget) }}</td>
                                <td class="p-2.5 text-right font-mono text-slate-600">{{ formatRupiah(proker.total_allocated) }}</td>
                                <td class="p-2.5 text-right font-mono font-semibold text-rose-600">{{ formatRupiah(proker.total_spent) }}</td>
                                <td class="p-2.5 text-right font-mono font-bold text-slate-800">{{ formatRupiah(proker.balance) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Section 3: Lembar Pengesahan Signature Block -->
            <div class="pt-6 border-t border-slate-300">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-6 text-center">
                    LEMBAR PENGESAHAN KEUANGAN
                </h3>
                
                <div class="grid grid-cols-2 gap-8 text-xs text-center">
                    <div>
                        <p>Mengetahui,</p>
                        <p class="font-bold mt-1">Ketua Posko KKN</p>
                        <div class="h-20"></div>
                        <p class="font-bold underline text-slate-900">{{ poskoInfo.ketua }}</p>
                    </div>

                    <div>
                        <p>Yang Mempertanggungjawabkan,</p>
                        <p class="font-bold mt-1">Bendahara Posko KKN</p>
                        <div class="h-20"></div>
                        <p class="font-bold underline text-slate-900">{{ poskoInfo.bendahara }}</p>
                    </div>
                </div>

                <div class="mt-10 text-center text-xs">
                    <p>Disetujui Oleh,</p>
                    <p class="font-bold mt-1">Dosen Pembimbing Lapangan (DPL)</p>
                    <div class="h-20"></div>
                    <p class="font-bold underline text-slate-900">{{ poskoInfo.dpl }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
