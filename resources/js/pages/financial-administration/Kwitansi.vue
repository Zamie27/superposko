<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { FileCheck, Printer, RefreshCw, Sparkles, CheckCircle2 } from '@lucide/vue';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';

interface FinanceOption {
    id: number;
    title: string;
    type: string;
    amount: number;
    date: string;
    payment_method: string;
    category: string | null;
    description: string | null;
}

const props = defineProps<{
    finances: FinanceOption[];
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
            { title: 'Kwitansi Digital', href: '/financial-administration/kwitansi' },
        ],
    },
});

// Form state for Kwitansi Generator
const receiptNumber = ref(`KW-${new Date().getFullYear()}${String(new Date().getMonth() + 1).padStart(2, '0')}-001`);
const receivedFrom = ref('Masyarakat / Kas Posko KKN');
const amount = ref<number>(100000);
const paymentFor = ref('Pembayaran Iuran / Operasional Posko KKN');
const location = ref('Posko KKN');
const date = ref(new Date().toISOString().split('T')[0]);

const selectedTransactionId = ref<number | null>(null);

const onSelectTransaction = (e: Event) => {
    const val = (e.target as HTMLSelectElement).value;
    if (val) {
        const fin = props.finances.find(f => f.id === Number(val));
        if (fin) {
            selectedTransactionId.value = fin.id;
            amount.value = fin.amount;
            paymentFor.value = fin.title + (fin.description ? ` (${fin.description})` : '');
            date.value = fin.date;
        }
    } else {
        selectedTransactionId.value = null;
    }
};

// Indonesian Number to Words Generator (Terbilang Rupiah)
const terbilang = (n: number): string => {
    if (n <= 0) return 'Nol Rupiah';

    const satuan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];

    const convert = (val: number): string => {
        let temp = '';
        if (val < 12) {
            temp = ' ' + satuan[val];
        } else if (val < 20) {
            temp = convert(val - 10) + ' Belas';
        } else if (val < 100) {
            temp = convert(Math.floor(val / 10)) + ' Puluh' + convert(val % 10);
        } else if (val < 200) {
            temp = ' Seratus' + convert(val - 100);
        } else if (val < 1000) {
            temp = convert(Math.floor(val / 100)) + ' Ratus' + convert(val % 100);
        } else if (val < 2000) {
            temp = ' Seribu' + convert(val - 1000);
        } else if (val < 1000000) {
            temp = convert(Math.floor(val / 1000)) + ' Ribu' + convert(val % 1000);
        } else if (val < 1000000000) {
            temp = convert(Math.floor(val / 1000000)) + ' Juta' + convert(val % 1000000);
        }
        return temp;
    };

    return (convert(Math.floor(n)).trim() + ' Rupiah').replace(/\s+/g, ' ');
};

const terbilangText = computed(() => terbilang(amount.value));

const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
};

const printKwitansi = () => {
    window.print();
};
</script>

<template>
    <Head title="Kwitansi Digital - Modul Keuangan" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-5xl mx-auto font-sans">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <FileCheck class="size-6 text-sky-500" />
                    Generator Kwitansi Resmi Digital
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Buat dan cetak kwitansi resmi bukti pembayaran posko lengkap dengan kalimat Terbilang Rupiah otomatis.
                </p>
            </div>

            <Button @click="printKwitansi" class="bg-sky-600 hover:bg-sky-700 text-white font-bold px-5 py-2.5 rounded-xl transition flex items-center gap-2 cursor-pointer shadow-xs">
                <Printer class="size-4" /> Cetak Kwitansi
            </Button>
        </div>

        <!-- Controls / Input Form (Screen View) -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4 print:hidden">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pengaturan Data Kwitansi</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700 dark:text-slate-300">Pilih dari Transaksi Keuangan (Opsional)</label>
                    <select @change="onSelectTransaction" class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-xs focus:border-sky-500 bg-white dark:bg-slate-950 dark:text-white">
                        <option value="">-- Ketik Manual --</option>
                        <option v-for="fin in finances" :key="fin.id" :value="fin.id">
                            {{ fin.date }} - {{ fin.title }} (Rp {{ fin.amount.toLocaleString('id-ID') }})
                        </option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700 dark:text-slate-300">Nomor Kwitansi</label>
                    <input v-model="receiptNumber" type="text" class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-xs focus:border-sky-500 dark:bg-slate-950 dark:text-white font-mono" />
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700 dark:text-slate-300">Sudah Terima Dari</label>
                    <input v-model="receivedFrom" type="text" placeholder="Nama Pembayar / Pihak Donor / Posko" class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-xs focus:border-sky-500 dark:bg-slate-950 dark:text-white" />
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700 dark:text-slate-300">Jumlah Uang (Rp)</label>
                    <input v-model.number="amount" type="number" min="0" class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-xs focus:border-sky-500 dark:bg-slate-950 dark:text-white font-mono font-bold" />
                </div>

                <div class="col-span-1 md:col-span-2 space-y-1">
                    <label class="font-semibold text-slate-700 dark:text-slate-300">Untuk Pembayaran</label>
                    <input v-model="paymentFor" type="text" placeholder="Rincian peruntukan pembayaran" class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-xs focus:border-sky-500 dark:bg-slate-950 dark:text-white" />
                </div>
            </div>
        </div>

        <!-- Official Kwitansi Card Document (Printable) -->
        <div class="p-8 rounded-2xl bg-white text-slate-900 border-2 border-slate-900 shadow-lg relative print:shadow-none print:border-slate-900 font-serif">
            <!-- Kwitansi Header -->
            <div class="flex justify-between items-start border-b-2 border-slate-900 pb-4 mb-6">
                <div>
                    <h2 class="text-2xl font-extrabold uppercase tracking-widest text-slate-900">KWITANSI</h2>
                    <p class="text-xs font-bold text-slate-600 font-sans tracking-wide mt-0.5">{{ poskoInfo.name }}</p>
                </div>
                <div class="text-right font-mono text-xs">
                    <p class="font-bold text-slate-800">No. {{ receiptNumber }}</p>
                    <p class="text-slate-500 mt-1 font-sans">Tanggal: {{ date }}</p>
                </div>
            </div>

            <!-- Kwitansi Content Fields -->
            <div class="space-y-4 text-xs font-sans">
                <div class="grid grid-cols-4 items-center">
                    <span class="font-bold text-slate-600 uppercase">Telah Terima Dari</span>
                    <span class="col-span-3 font-semibold text-slate-900 border-b border-dotted border-slate-400 pb-1">: {{ receivedFrom }}</span>
                </div>

                <div class="grid grid-cols-4 items-start">
                    <span class="font-bold text-slate-600 uppercase">Uang Sejumlah</span>
                    <span class="col-span-3 font-bold italic text-slate-900 bg-slate-100 p-2.5 rounded-lg border border-slate-200">: # {{ terbilangText }} #</span>
                </div>

                <div class="grid grid-cols-4 items-center">
                    <span class="font-bold text-slate-600 uppercase">Untuk Pembayaran</span>
                    <span class="col-span-3 font-semibold text-slate-900 border-b border-dotted border-slate-400 pb-1">: {{ paymentFor }}</span>
                </div>
            </div>

            <!-- Bottom Section: Amount Badge & Signature -->
            <div class="mt-8 pt-6 border-t border-slate-200 flex justify-between items-end font-sans">
                <!-- Amount Badge -->
                <div class="px-5 py-3 rounded-xl bg-slate-900 text-white font-mono text-xl font-extrabold shadow-xs">
                    {{ formatRupiah(amount) }}
                </div>

                <!-- Signatures -->
                <div class="text-center text-xs space-y-1">
                    <p class="text-slate-500">{{ location }}, {{ date }}</p>
                    <p class="font-bold text-slate-800">Bendahara Posko</p>
                    <div class="h-16"></div>
                    <p class="font-bold underline text-slate-900">{{ poskoInfo.bendahara }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
