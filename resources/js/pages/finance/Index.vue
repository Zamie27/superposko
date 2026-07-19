<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import {
    Plus, Edit, Trash2, FileText, ArrowUpRight, ArrowDownLeft, Wallet, ArrowRightLeft,
    X, ImageIcon, Search, Filter, Printer, ExternalLink, Info, RefreshCw, Tag, CirclePlus, CircleMinus
} from '@lucide/vue';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { useConfirm } from '@/composables/useConfirm';
import { useToast } from '@/composables/useToast';

interface ProgramKerja {
    id: number;
    name: string;
    budget: number;
}

interface Creator {
    id: number;
    name: string;
}

interface FinanceRecord {
    id: number;
    type: 'income' | 'expense' | 'allocation' | 'transfer';
    payment_method: 'Cash' | 'SeaBank' | 'DANA';
    destination_payment_method: 'Cash' | 'SeaBank' | 'DANA' | null;
    amount: number;
    title: string;
    description: string | null;
    category: string | null;
    date: string;
    receipt_path: string | null;
    program_kerja_id: number | null;
    program_kerja: { id: number; name: string } | null;
    creator: Creator;
}

interface CustomTag {
    id: number;
    name: string;
    type: 'income' | 'expense';
}

const props = defineProps<{
    finances: FinanceRecord[];
    programKerjas: ProgramKerja[];
    customTags: CustomTag[];
    metrics: {
        total_income: number;
        total_expense: number;
        balance: number;
        balances_by_method: Record<string, number>;
    };
    canWrite: boolean;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Kas & Keuangan',
                href: '/finance',
            },
        ],
    },
});

const toast = useToast();
const { confirm } = useConfirm();

// State
const isModalOpen = ref(false);
const searchQuery = ref('');
const filterType = ref<'all' | 'income' | 'expense' | 'allocation' | 'transfer'>('all');
const filterProker = ref<string>('all');
const filterCategory = ref<string>('all');
const activeTab = ref<'ledger' | 'summary'>('ledger');
const editingRecord = ref<FinanceRecord | null>(null);
const previewImage = ref<string | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);
const filePreview = ref<string | null>(null);
const isTagModalOpen = ref(false);

// Category and Link Type States
const linkType = ref<'umum' | 'proker'>('umum');
const selectedCategory = ref('');
const customCategory = ref('');
const selectedProkerCategory = ref('Kas ke Proker');

const defaultIncomeCategories = [
    { value: 'Iuran Anggota', label: 'Iuran Anggota', isDefault: true },
    { value: 'Sponsor', label: 'Sponsor', isDefault: true },
    { value: 'Donasi / Sumbangan', label: 'Donasi / Sumbangan', isDefault: true },
    { value: 'Dana Kampus', label: 'Dana Kampus', isDefault: true },
];

const defaultExpenseCategories = [
    { value: 'Konsumsi', label: 'Konsumsi', isDefault: true },
    { value: 'Transportasi', label: 'Transportasi', isDefault: true },
    { value: 'Perlengkapan & Bahan', label: 'Perlengkapan & Bahan', isDefault: true },
    { value: 'Humas & Publikasi', label: 'Humas & Publikasi', isDefault: true },
];

const incomeCategories = computed(() => {
    const custom = props.customTags
        .filter(t => t.type === 'income')
        .map(t => ({ value: t.name, label: t.name, isDefault: false, tagId: t.id }));
    return [...defaultIncomeCategories, ...custom, { value: 'Lainnya', label: 'Lainnya', isDefault: true }];
});

const expenseCategories = computed(() => {
    const custom = props.customTags
        .filter(t => t.type === 'expense')
        .map(t => ({ value: t.name, label: t.name, isDefault: false, tagId: t.id }));
    return [...defaultExpenseCategories, ...custom, { value: 'Lainnya', label: 'Lainnya', isDefault: true }];
});

// All unique categories from transactions (for filter dropdown)
const allCategories = computed(() => {
    const cats = new Set<string>();
    props.finances.forEach(f => {
        if (f.category && f.category !== 'Kas ke Proker' && f.category !== 'Proker ke Kas' && f.category !== 'Belanja Proker') {
            cats.add(f.category);
        }
    });
    return Array.from(cats).sort();
});

// Tag management form
const tagForm = useForm({
    name: '',
    type: 'expense' as 'income' | 'expense',
});

const submitTagForm = () => {
    tagForm.post('/finance/tags', {
        onSuccess: () => {
            tagForm.reset();
            isTagModalOpen.value = false;
            toast.success('Tag berhasil ditambahkan');
        },
        onError: () => {
            toast.error('Gagal menambahkan tag');
        }
    });
};

const deleteTag = async (tag: CustomTag) => {
    const proceed = await confirm({
        title: 'Hapus Tag',
        message: `Apakah Anda yakin ingin menghapus tag "${tag.name}"? Transaksi yang menggunakan tag ini tidak akan terpengaruh.`,
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        variant: 'destructive'
    });
    if (proceed) {
        router.delete(`/finance/tags/${tag.id}`, {
            onSuccess: () => toast.success('Tag berhasil dihapus'),
            onError: () => toast.error('Gagal menghapus tag'),
        });
    }
};

// Form
const form = useForm({
    type: 'expense' as 'income' | 'expense' | 'allocation' | 'transfer',
    payment_method: 'Cash' as 'Cash' | 'SeaBank' | 'DANA',
    destination_payment_method: '' as 'Cash' | 'SeaBank' | 'DANA' | '',
    amount: '' as number | '',
    title: '',
    description: '',
    category: '',
    date: new Date().toISOString().split('T')[0],
    program_kerja_id: '' as number | '' | 'null',
    receipt_file: null as File | null,
});

// Format currency helper
const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(val);
};

const formattedAmount = computed(() => {
    if (!form.amount) return '';
    return Number(form.amount).toLocaleString('id-ID');
});

const onAmountInput = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const cleanValue = target.value.replace(/\D/g, '');
    form.amount = cleanValue ? parseInt(cleanValue, 10) : '';
};

// Filtered Records
const filteredFinances = computed(() => {
    return props.finances.filter(record => {
        const matchesSearch = record.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (record.description && record.description.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
            (record.category && record.category.toLowerCase().includes(searchQuery.value.toLowerCase()));
        
        const matchesType = filterType.value === 'all' || record.type === filterType.value;
        
        const matchesProker = filterProker.value === 'all' || record.program_kerja_id === Number(filterProker.value);

        const matchesCategory = filterCategory.value === 'all' || record.category === filterCategory.value;

        return matchesSearch && matchesType && matchesProker && matchesCategory;
    });
});

// Summary of filtered records
const filteredSummary = computed(() => {
    const records = filteredFinances.value;
    const totalIncome = records
        .filter(r => r.type === 'income')
        .reduce((sum, r) => sum + r.amount, 0);
    const totalExpense = records
        .filter(r => r.type === 'expense')
        .reduce((sum, r) => sum + r.amount, 0);
    const totalTransfer = records
        .filter(r => r.type === 'transfer' || r.type === 'allocation')
        .reduce((sum, r) => sum + r.amount, 0);
    return {
        count: records.length,
        totalIncome,
        totalExpense,
        totalTransfer,
        net: totalIncome - totalExpense,
    };
});

const hasActiveFilter = computed(() => {
    return filterType.value !== 'all' || filterProker.value !== 'all' || filterCategory.value !== 'all' || searchQuery.value !== '';
});

// Proker spending breakdowns
const prokerSpentBreakdown = computed(() => {
    return props.programKerjas.map(proker => {
        const allocated = props.finances
            .filter(f => f.program_kerja_id === proker.id && f.type === 'allocation' && f.category === 'Kas ke Proker')
            .reduce((sum, f) => sum + Number(f.amount), 0);

        const returned = props.finances
            .filter(f => f.program_kerja_id === proker.id && f.type === 'allocation' && f.category === 'Proker ke Kas')
            .reduce((sum, f) => sum + Number(f.amount), 0);

        const netAllocated = allocated - returned;

        const spent = props.finances
            .filter(f => f.program_kerja_id === proker.id && f.type === 'expense')
            .reduce((sum, f) => sum + Number(f.amount), 0);

        const available = netAllocated - spent;

        return {
            ...proker,
            spent,
            available,
            percent: proker.budget > 0 ? Math.round((spent / proker.budget) * 100) : 0
        };
    }).sort((a, b) => b.spent - a.spent);
});

// Actions
const openAddModal = () => {
    editingRecord.value = null;
    form.reset();
    form.type = 'expense';
    form.payment_method = 'Cash';
    form.destination_payment_method = '';
    form.date = new Date().toISOString().split('T')[0];
    form.program_kerja_id = '';
    form.category = '';
    linkType.value = 'umum';
    selectedCategory.value = '';
    customCategory.value = '';
    filePreview.value = null;
    isModalOpen.value = true;
};

const openEditModal = (record: FinanceRecord) => {
    editingRecord.value = record;
    form.type = record.type;
    form.payment_method = record.payment_method;
    form.destination_payment_method = record.destination_payment_method || '';
    form.amount = record.amount;
    form.title = record.title;
    form.description = record.description || '';
    form.date = record.date;
    form.program_kerja_id = record.program_kerja_id || '';
    form.category = record.category || '';
    filePreview.value = record.receipt_path;

    if (record.type === 'allocation' || record.program_kerja_id) {
        linkType.value = 'proker';
        selectedProkerCategory.value = record.category || 'Kas ke Proker';
        selectedCategory.value = '';
        customCategory.value = '';
    } else {
        linkType.value = 'umum';
        const isPredefined = (record.type === 'income' ? incomeCategories.value : expenseCategories.value)
            .some(c => c.value === record.category);
        if (record.category) {
            if (isPredefined) {
                selectedCategory.value = record.category;
                customCategory.value = '';
            } else {
                selectedCategory.value = 'Lainnya';
                customCategory.value = record.category;
            }
        } else {
            selectedCategory.value = '';
            customCategory.value = '';
        }
    }
    isModalOpen.value = true;
};

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;

    if (target.files && target.files[0]) {
        const file = target.files[0];
        form.receipt_file = file;
        filePreview.value = URL.createObjectURL(file);
    }
};

const submitForm = () => {
    if (linkType.value === 'umum') {
        // General transactions (income or expense, no proker)
        form.program_kerja_id = '';
        if (selectedCategory.value === 'Lainnya') {
            form.category = customCategory.value;
        } else {
            form.category = selectedCategory.value;
        }
        // type stays as income or expense or transfer
    } else {
        // Proker-linked transactions
        if (form.program_kerja_id === 'null' || form.program_kerja_id === '') {
            form.program_kerja_id = '';
        }

        if (form.type === 'allocation') {
            // Alokasi Dana: category = 'Kas ke Proker' or 'Proker ke Kas'
            form.category = selectedProkerCategory.value || 'Kas ke Proker';
        } else if (form.type === 'expense') {
            // Belanja Proker: category = 'Belanja Proker'
            form.category = 'Belanja Proker';
        }
    }

    if (editingRecord.value) {
        // Form post due to file upload constraints
        form.post(`/finance/${editingRecord.value.id}`, {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
                filePreview.value = null;
                toast.success('Transaksi berhasil diperbarui');
            },
            onError: () => {
                toast.error('Gagal memperbarui transaksi. Cek isian Anda.');
            }
        });
    } else {
        form.post('/finance', {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
                filePreview.value = null;
                toast.success('Transaksi berhasil dicatat');
            },
            onError: () => {
                toast.error('Gagal menyimpan transaksi. Cek isian Anda.');
            }
        });
    }
};

const deleteRecord = async (record: FinanceRecord) => {
    const proceed = await confirm({
        title: 'Hapus Transaksi',
        message: `Apakah Anda yakin ingin menghapus transaksi "${record.title}"? Saldo kas akan terhitung ulang otomatis.`,
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        variant: 'destructive'
    });

    if (proceed) {
        router.delete(`/finance/${record.id}`, {
            onSuccess: () => {
                toast.success('Transaksi berhasil dihapus');
            },
            onError: () => {
                toast.error('Gagal menghapus transaksi');
            }
        });
    }
};

const triggerPrint = () => {
    window.print();
};
</script>

<template>
    <Head title="E-Bendahara - Kas & Keuangan" />

    <div class="flex flex-col gap-6 p-4 md:p-6 w-full max-w-7xl mx-auto">
        <!-- Print Header (Hidden on screen) -->
        <div class="hidden print:block border-b-2 border-slate-900 pb-4 mb-6">
            <h1 class="text-2xl font-extrabold text-center uppercase tracking-wider text-slate-900">
                Laporan Kas & Keuangan Posko KKN
            </h1>
            <p class="text-xs text-center text-slate-500 mt-1">
                Dicetak pada: {{ new Date().toLocaleString('id-ID', { dateStyle: 'full', timeStyle: 'short' }) }}
            </p>
        </div>

        <!-- Upper Banner Dashboard -->
        <div class="no-print grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Saldo Kas Card -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 p-6 text-white shadow-lg transition-transform duration-300 hover:scale-[1.01]">
                <div class="absolute right-0 top-0 translate-x-4 -translate-y-4 opacity-15">
                    <Wallet class="size-36" />
                </div>
                <span class="text-xs font-bold uppercase tracking-widest text-indigo-100">Total Saldo Kas</span>
                <h3 class="text-3xl font-black mt-2 tracking-tight">
                    {{ formatRupiah(metrics.balance) }}
                </h3>
                <div class="mt-4 grid grid-cols-3 gap-2 text-xs border-t border-indigo-300/30 pt-3">
                    <div v-for="(bal, method) in metrics.balances_by_method" :key="method">
                        <span class="block opacity-75 font-semibold">{{ method }}</span>
                        <span class="font-bold">{{ formatRupiah(bal) }}</span>
                    </div>
                </div>
            </div>

            <!-- Pemasukan Card -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pemasukan</span>
                        <div class="size-8 rounded-full bg-emerald-50 dark:bg-emerald-950/30 flex items-center justify-center text-emerald-600">
                            <ArrowUpRight class="size-4" />
                        </div>
                    </div>
                    <h3 class="text-2xl font-extrabold mt-3 text-slate-900 dark:text-white">
                        {{ formatRupiah(metrics.total_income) }}
                    </h3>
                </div>
                <p class="text-[10px] text-slate-400 mt-4">Dana iuran anggota, donasi, atau sponsor.</p>
            </div>

            <!-- Pengeluaran Card -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pengeluaran</span>
                        <div class="size-8 rounded-full bg-red-50 dark:bg-red-950/30 flex items-center justify-center text-red-600">
                            <ArrowDownLeft class="size-4" />
                        </div>
                    </div>
                    <h3 class="text-2xl font-extrabold mt-3 text-slate-900 dark:text-white">
                        {{ formatRupiah(metrics.total_expense) }}
                    </h3>
                </div>
                <p class="text-[10px] text-slate-400 mt-4">Biaya operasional posko & belanja program kerja.</p>
            </div>
        </div>

        <!-- Quick Tabs for Navigation (no-print) -->
        <div class="no-print flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-px">
            <div class="flex gap-4">
                <button 
                    @click="activeTab = 'ledger'" 
                    class="pb-3 text-sm font-bold border-b-2 transition-colors relative"
                    :class="[activeTab === 'ledger' ? 'border-indigo-500 text-slate-900 dark:text-white' : 'border-transparent text-slate-400 hover:text-slate-600 dark:hover:text-slate-300']"
                >
                    Buku Ledger Keuangan
                </button>
                <button 
                    @click="activeTab = 'summary'" 
                    class="pb-3 text-sm font-bold border-b-2 transition-colors relative"
                    :class="[activeTab === 'summary' ? 'border-indigo-500 text-slate-900 dark:text-white' : 'border-transparent text-slate-400 hover:text-slate-600 dark:hover:text-slate-300']"
                >
                    Analisa Anggaran Proker
                </button>
            </div>

            <div class="flex items-center gap-2">
                <Button 
                    v-if="canWrite"
                    variant="outline"
                    size="sm" 
                    class="h-9 rounded-xl border-slate-200 dark:border-slate-800"
                    @click="isTagModalOpen = true"
                >
                    <Tag class="size-4 mr-1.5" />
                    <span>Kelola Tag</span>
                </Button>

                <Button 
                    variant="outline" 
                    size="sm" 
                    class="h-9 rounded-xl border-slate-200 dark:border-slate-800"
                    @click="triggerPrint"
                >
                    <Printer class="size-4 mr-1.5" />
                    <span>Cetak Laporan</span>
                </Button>

                <Button 
                    v-if="canWrite"
                    @click="openAddModal"
                    size="sm"
                    class="h-9 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold hover:from-indigo-700 hover:to-purple-700 shadow-md"
                >
                    <Plus class="size-4 mr-1.5" />
                    <span>Catat Keuangan</span>
                </Button>
            </div>
        </div>

        <!-- Tab 1: Ledger Keuangan -->
        <div v-if="activeTab === 'ledger'" class="flex flex-col gap-4">
            <!-- Search & Filters (no-print) -->
            <div class="no-print grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
                <!-- Search input -->
                <div class="relative">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" />
                    <input 
                        v-model="searchQuery"
                        type="text" 
                        placeholder="Cari transaksi..."
                        class="w-full pl-9 pr-4 py-2 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:border-indigo-500 bg-transparent dark:text-white"
                    />
                </div>

                <!-- Type filter -->
                <div class="relative">
                    <Filter class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" />
                    <select 
                        v-model="filterType"
                        class="w-full pl-9 pr-4 py-2 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:border-indigo-500 bg-transparent dark:text-white appearance-none"
                    >
                        <option value="all" class="dark:bg-slate-900">Semua Tipe Transaksi</option>
                        <option value="income" class="dark:bg-slate-900">Pemasukan (+)</option>
                        <option value="expense" class="dark:bg-slate-900">Pengeluaran (-)</option>
                        <option value="allocation" class="dark:bg-slate-900">Alokasi Dana</option>
                        <option value="transfer" class="dark:bg-slate-900">Transfer Antar Kas</option>
                    </select>
                </div>

                <!-- Proker filter -->
                <div class="relative">
                    <FileText class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" />
                    <select 
                        v-model="filterProker"
                        class="w-full pl-9 pr-4 py-2 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:border-indigo-500 bg-transparent dark:text-white appearance-none"
                    >
                        <option value="all" class="dark:bg-slate-900">Semua Program Kerja</option>
                        <option 
                            v-for="proker in programKerjas" 
                            :key="proker.id" 
                            :value="proker.id"
                            class="dark:bg-slate-900"
                        >
                            {{ proker.name }}
                        </option>
                    </select>
                </div>

                <!-- Category/Tag filter -->
                <div class="relative">
                    <Tag class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-slate-400" />
                    <select 
                        v-model="filterCategory"
                        class="w-full pl-9 pr-4 py-2 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:outline-none focus:border-indigo-500 bg-transparent dark:text-white appearance-none"
                    >
                        <option value="all" class="dark:bg-slate-900">Semua Kategori / Tag</option>
                        <option 
                            v-for="cat in allCategories" 
                            :key="cat" 
                            :value="cat"
                            class="dark:bg-slate-900"
                        >
                            {{ cat }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Ledger Table -->
            <div class="border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900 overflow-hidden shadow-xs">
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-[10px] md:text-xs font-bold uppercase tracking-wider text-slate-400">
                                <th class="py-3.5 px-4">Tanggal</th>
                                <th class="py-3.5 px-4">Keterangan Transaksi</th>
                                <th class="py-3.5 px-4 text-center">Tipe</th>
                                <th class="py-3.5 px-4 text-right">Nominal</th>
                                <th class="py-3.5 px-4">Link Proker</th>
                                <th class="py-3.5 px-4 no-print">Bukti</th>
                                <th class="py-3.5 px-4 text-slate-400 font-semibold no-print">Petugas</th>
                                <th class="py-3.5 px-4 text-right no-print" v-if="canWrite">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-xs md:text-sm">
                            <tr 
                                v-for="record in filteredFinances" 
                                :key="record.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors"
                            >
                                <!-- Date -->
                                <td class="py-3.5 px-4 font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                    {{ new Date(record.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                                </td>

                                <!-- Title / Description -->
                                <td class="py-3.5 px-4">
                                    <div>
                                        <p class="font-bold text-slate-800 dark:text-slate-200">{{ record.title }}</p>
                                        <p v-if="record.description" class="text-[10px] md:text-xs text-slate-400 mt-0.5 line-clamp-1">
                                            {{ record.description }}
                                        </p>
                                    </div>
                                </td>

                                <!-- Type -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span 
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold"
                                        :class="[
                                            record.type === 'allocation'
                                                ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/20 dark:text-amber-400'
                                                : record.type === 'income'
                                                    ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-450'
                                                    : record.type === 'transfer'
                                                        ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/20 dark:text-indigo-400'
                                                        : 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-400'
                                        ]"
                                    >
                                    {{ record.type === 'allocation' ? (record.category === 'Kas ke Proker' ? 'Alokasi → Proker' : 'Alokasi → Kas') : record.type === 'income' ? 'Pemasukan' : record.type === 'transfer' ? 'Transfer Kas' : 'Pengeluaran' }}
                                </span>
                                </td>

                                <!-- Amount -->
                                <td 
                                    class="py-3.5 px-4 text-right font-extrabold whitespace-nowrap"
                                    :class="[
                                        record.type === 'transfer' || record.type === 'allocation'
                                            ? 'text-indigo-600 dark:text-indigo-400'
                                            : record.type === 'income'
                                                ? 'text-emerald-600 dark:text-emerald-450'
                                                : 'text-slate-800 dark:text-slate-200'
                                    ]"
                                >
                                    {{ record.type === 'transfer' || record.type === 'allocation' ? '' : (record.type === 'income' ? '+' : '-') }} {{ formatRupiah(record.amount) }}
                                </td>

                                <!-- Link Proker / Kategori -->
                                <td class="py-3.5 px-4">
                                    <div v-if="record.type === 'transfer'" class="flex items-center gap-1.5">
                                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ record.payment_method }}</span>
                                        <ArrowRightLeft class="size-3 text-slate-400" />
                                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ record.destination_payment_method }}</span>
                                    </div>
                                    <template v-else>
                                        <span 
                                            v-if="record.program_kerja"
                                            class="inline-flex items-center gap-1 text-[10px] font-semibold bg-sky-50 dark:bg-sky-950/20 text-sky-700 dark:text-sky-400 px-2 py-0.5 rounded-md max-w-[150px] truncate"
                                            :title="record.program_kerja.name"
                                        >
                                            <FileText class="size-3 shrink-0" />
                                            <span class="truncate">{{ record.program_kerja.name }}</span>
                                        </span>
                                        <span 
                                            v-else-if="record.category" 
                                            class="inline-flex items-center gap-1 text-[10px] font-semibold bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 px-2 py-0.5 rounded-md max-w-[150px] truncate"
                                            :title="record.category"
                                        >
                                            <span class="truncate">{{ record.category }}</span>
                                        </span>
                                        <span v-else class="text-slate-400 text-[10px] italic">Umum</span>
                                    </template>
                                </td>

                                <!-- Bukti Nota (no-print) -->
                                <td class="py-3.5 px-4 no-print">
                                    <button 
                                        v-if="record.receipt_path" 
                                        @click="previewImage = record.receipt_path"
                                        class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-500 hover:text-indigo-600"
                                    >
                                        <ImageIcon class="size-3.5" />
                                        <span>Lihat Nota</span>
                                    </button>
                                    <span v-else class="text-slate-300 dark:text-slate-700">-</span>
                                </td>

                                <!-- Petugas (no-print) -->
                                <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 no-print max-w-[100px] truncate">
                                    {{ record.creator.name }}
                                </td>

                                <!-- Actions (no-print) -->
                                <td class="py-3.5 px-4 text-right no-print" v-if="canWrite">
                                    <div class="flex items-center justify-end gap-2">
                                        <button 
                                            @click="openEditModal(record)"
                                            class="p-1 rounded-md text-slate-400 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 transition-colors"
                                            title="Edit transaksi"
                                        >
                                            <Edit class="size-3.5" />
                                        </button>
                                        <button 
                                            @click="deleteRecord(record)"
                                            class="p-1 rounded-md text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors"
                                            title="Hapus transaksi"
                                        >
                                            <Trash2 class="size-3.5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="filteredFinances.length === 0">
                                <td colspan="8" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                    Tidak ada catatan transaksi keuangan yang sesuai filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary Bar (no-print) -->
            <div class="no-print grid grid-cols-2 md:grid-cols-5 gap-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Transaksi</span>
                    <span class="text-sm font-extrabold text-slate-800 dark:text-white mt-0.5">{{ filteredSummary.count }} data</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Masuk</span>
                    <span class="text-sm font-extrabold text-emerald-600 dark:text-emerald-450 mt-0.5">{{ formatRupiah(filteredSummary.totalIncome) }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Keluar</span>
                    <span class="text-sm font-extrabold text-red-600 dark:text-red-400 mt-0.5">{{ formatRupiah(filteredSummary.totalExpense) }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Transfer & Alokasi</span>
                    <span class="text-sm font-extrabold text-indigo-600 dark:text-indigo-400 mt-0.5">{{ formatRupiah(filteredSummary.totalTransfer) }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Selisih (Net)</span>
                    <span 
                        class="text-sm font-extrabold mt-0.5"
                        :class="[filteredSummary.net >= 0 ? 'text-emerald-600 dark:text-emerald-450' : 'text-red-600 dark:text-red-400']"
                    >
                        {{ formatRupiah(filteredSummary.net) }}
                    </span>
                </div>
                <div v-if="hasActiveFilter" class="col-span-2 md:col-span-5 text-[10px] text-slate-400 border-t border-slate-100 dark:border-slate-800 pt-2 mt-1">
                    <span class="font-semibold">Filter aktif:</span>
                    <span v-if="filterType !== 'all'" class="ml-1 inline-flex items-center gap-0.5 bg-indigo-50 dark:bg-indigo-950/20 text-indigo-600 dark:text-indigo-400 px-1.5 py-0.5 rounded-md font-bold">
                        Tipe: {{ filterType === 'income' ? 'Pemasukan' : filterType === 'expense' ? 'Pengeluaran' : filterType === 'allocation' ? 'Alokasi' : 'Transfer' }}
                    </span>
                    <span v-if="filterCategory !== 'all'" class="ml-1 inline-flex items-center gap-0.5 bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 px-1.5 py-0.5 rounded-md font-bold">
                        Tag: {{ filterCategory }}
                    </span>
                    <span v-if="filterProker !== 'all'" class="ml-1 inline-flex items-center gap-0.5 bg-sky-50 dark:bg-sky-950/20 text-sky-600 dark:text-sky-400 px-1.5 py-0.5 rounded-md font-bold">
                        Proker: {{ programKerjas.find(p => p.id === Number(filterProker))?.name || filterProker }}
                    </span>
                    <span v-if="searchQuery" class="ml-1 inline-flex items-center gap-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 px-1.5 py-0.5 rounded-md font-bold">
                        Cari: "{{ searchQuery }}"
                    </span>
                </div>
            </div>
        </div>

        <!-- Tab 2: Proker Budget Breakdown -->
        <div v-else-if="activeTab === 'summary'" class="flex flex-col gap-4">
            <div class="bg-indigo-50/50 dark:bg-indigo-950/10 border border-indigo-100 dark:border-indigo-950/30 p-4 rounded-2xl flex items-start gap-3">
                <Info class="size-5 text-indigo-600 dark:text-indigo-400 shrink-0 mt-0.5" />
                <div>
                    <h4 class="text-sm font-bold text-indigo-900 dark:text-indigo-300">Hubungan Keuangan & Program Kerja</h4>
                    <p class="text-xs text-indigo-700/80 dark:text-indigo-400/80 mt-0.5">
                        Setiap transaksi pengeluaran yang ditautkan ke Program Kerja otomatis akan diakumulasikan sebagai realisasi belanja proker tersebut. Hal ini membantu posko mengontrol pengeluaran agar tidak over-budget.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div 
                    v-for="breakdown in prokerSpentBreakdown" 
                    :key="breakdown.id"
                    class="border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between"
                >
                    <div>
                        <div class="flex items-start justify-between gap-4">
                            <h4 class="font-bold text-sm md:text-base text-slate-900 dark:text-white line-clamp-1">{{ breakdown.name }}</h4>
                            <span 
                                class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0"
                                :class="[breakdown.spent > breakdown.budget ? 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-400' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-450']"
                            >
                                {{ breakdown.percent }}% Terpakai
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden mt-3">
                            <div 
                                class="h-2 rounded-full transition-all" 
                                :class="[breakdown.spent > breakdown.budget ? 'bg-red-500' : 'bg-emerald-500']"
                                :style="`width: ${Math.min(breakdown.percent, 100)}%`"
                            ></div>
                        </div>

                        <!-- Ledger breakdown list -->
                        <div class="mt-4 grid grid-cols-3 gap-2 text-xs border-b border-slate-100 dark:border-slate-800 pb-3">
                            <div>
                                <span class="text-[10px] text-slate-400 font-semibold block uppercase">Estimasi Anggaran</span>
                                <span class="font-bold text-slate-700 dark:text-slate-300">{{ formatRupiah(breakdown.budget) }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-semibold block uppercase">Realisasi Belanja</span>
                                <span class="font-extrabold text-slate-800 dark:text-slate-200">{{ formatRupiah(breakdown.spent) }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-semibold block uppercase">Dana Tersedia</span>
                                <span 
                                    class="font-bold"
                                    :class="[breakdown.available > 0 ? 'text-emerald-600 dark:text-emerald-450 font-extrabold' : 'text-slate-500 dark:text-slate-400']"
                                >
                                    {{ formatRupiah(breakdown.available) }}
                                </span>
                            </div>
                        </div>

                        <!-- Proker specific balance breakdown -->
                        <div v-if="metrics.proker_balances && metrics.proker_balances[breakdown.id]" class="mt-3 bg-slate-50 dark:bg-slate-800/30 rounded-xl p-3 border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] text-slate-400 font-semibold block uppercase mb-1.5">Rincian Saldo Tersedia</span>
                            <div class="grid grid-cols-3 gap-2 text-xs">
                                <div v-for="(bal, method) in metrics.proker_balances[breakdown.id]" :key="method" class="flex flex-col">
                                    <span class="text-[9px] md:text-[10px] text-slate-500 dark:text-slate-400 font-medium">{{ method }}</span>
                                    <span 
                                        class="font-bold"
                                        :class="[bal > 0 ? 'text-slate-700 dark:text-slate-300' : 'text-slate-400']"
                                    >
                                        {{ formatRupiah(bal) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter quickly actions -->
                    <div class="mt-3.5 flex items-center justify-between">
                        <span class="text-[10px] text-slate-400">Total rencana biaya proker posko</span>
                        <button 
                            @click="filterProker = breakdown.id; activeTab = 'ledger'"
                            class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-500 hover:text-indigo-600 transition-colors"
                        >
                            <span>Lihat Rincian Ledger</span>
                            <ExternalLink class="size-3" />
                        </button>
                    </div>
                </div>

                <div v-if="prokerSpentBreakdown.length === 0" class="col-span-2 py-12 text-center text-slate-400 dark:text-slate-500">
                    Belum ada data program kerja untuk dianalisa.
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal (no-print) -->
        <div 
            v-if="isModalOpen" 
            class="no-print fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4"
        >
            <div class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 shadow-xl relative animate-in fade-in zoom-in-95 duration-200">
                <button 
                    @click="isModalOpen = false" 
                    class="absolute top-4 right-4 p-1.5 rounded-full text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
                >
                    <X class="size-4" />
                </button>

                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
                    {{ editingRecord ? 'Edit Catatan Keuangan' : 'Catat Transaksi Keuangan Baru' }}
                </h3>

                <form @submit.prevent="submitForm" class="flex flex-col gap-4">
                    <!-- Type selector -->
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Tipe Transaksi</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button
                                type="button"
                                @click="form.type = 'expense'; linkType = 'umum'"
                                class="py-2.5 rounded-xl border text-sm font-bold transition-all flex items-center justify-center gap-1.5"
                                :class="[form.type === 'expense' ? 'bg-red-500 border-red-500 text-white shadow-xs' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 dark:text-white']"
                            >
                                <ArrowDownLeft class="size-4" />
                                <span>Pengeluaran</span>
                            </button>
                            <button
                                type="button"
                                @click="form.type = 'income'; linkType = 'umum'"
                                class="py-2.5 rounded-xl border text-sm font-bold transition-all flex items-center justify-center gap-1.5"
                                :class="[form.type === 'income' ? 'bg-emerald-500 border-emerald-500 text-white shadow-xs' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 dark:text-white']"
                            >
                                <ArrowUpRight class="size-4" />
                                <span>Pemasukan</span>
                            </button>
                            <button
                                type="button"
                                @click="form.type = 'allocation'; linkType = 'proker'"
                                class="py-2.5 rounded-xl border text-sm font-bold transition-all flex items-center justify-center gap-1.5"
                                :class="[form.type === 'allocation' ? 'bg-amber-500 border-amber-500 text-white shadow-xs' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 dark:text-white']"
                            >
                                <RefreshCw class="size-4" />
                                <span>Alokasi Dana</span>
                            </button>
                            <button
                                type="button"
                                @click="form.type = 'transfer'; linkType = 'umum'"
                                class="py-2.5 rounded-xl border text-sm font-bold transition-all flex items-center justify-center gap-1.5"
                                :class="[form.type === 'transfer' ? 'bg-indigo-500 border-indigo-500 text-white shadow-xs' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 dark:text-white']"
                            >
                                <ArrowRightLeft class="size-4" />
                                <span>Transfer</span>
                            </button>
                        </div>
                    </div>

                    <!-- Title / Nama Transaksi -->
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Nama Transaksi</label>
                        <input 
                            v-model="form.title"
                            type="text" 
                            placeholder="Contoh: Beli Semen, Iuran Kas Minggu 2"
                            required
                            class="w-full px-4 py-2 border border-slate-200 dark:border-slate-800 bg-transparent rounded-xl text-sm focus:outline-none focus:border-indigo-500 dark:text-white"
                        />
                        <p v-if="form.errors.title" class="text-xs text-red-500 mt-1">{{ form.errors.title }}</p>
                    </div>

                    <!-- === INCOME: Jenis Pemasukan Umum === -->
                    <div v-if="form.type === 'income'" class="mt-3">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Jenis Pemasukan</label>
                        <select 
                            v-model="selectedCategory"
                            class="w-full px-4 py-2 border border-slate-200 dark:border-slate-800 bg-transparent rounded-xl text-sm focus:outline-none focus:border-indigo-500 dark:text-white appearance-none"
                            required
                        >
                            <option value="" class="dark:bg-slate-900">-- Pilih Jenis Pemasukan --</option>
                            <option 
                                v-for="cat in incomeCategories" 
                                :key="cat.value" 
                                :value="cat.value"
                                class="dark:bg-slate-900"
                            >
                                {{ cat.label }}
                            </option>
                        </select>
                        <div v-if="selectedCategory === 'Lainnya'" class="mt-2">
                            <input 
                                v-model="customCategory"
                                type="text"
                                placeholder="Tulis jenis pemasukan manual..."
                                required
                                class="w-full px-4 py-2 border border-slate-200 dark:border-slate-800 bg-transparent rounded-xl text-sm focus:outline-none focus:border-indigo-500 dark:text-white"
                            />
                        </div>
                        <p v-if="form.errors.category" class="text-xs text-red-500 mt-1">{{ form.errors.category }}</p>
                    </div>

                    <!-- === EXPENSE: Umum / Proker Toggle === -->
                    <div v-else-if="form.type === 'expense'" class="space-y-3 mt-3">
                        <!-- Kategori Transaksi (Umum vs Proker) -->
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Kategori Pengeluaran</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    type="button"
                                    @click="linkType = 'umum'"
                                    :class="[
                                        'py-2 rounded-xl border text-xs font-bold transition-all flex items-center justify-center gap-1.5',
                                        linkType === 'umum'
                                            ? 'bg-sky-500 border-sky-500 text-white shadow-xs'
                                            : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 dark:text-white'
                                    ]"
                                >
                                    Pengeluaran Umum
                                </button>
                                <button
                                    type="button"
                                    @click="linkType = 'proker'"
                                    :class="[
                                        'py-2 rounded-xl border text-xs font-bold transition-all flex items-center justify-center gap-1.5',
                                        linkType === 'proker'
                                            ? 'bg-sky-500 border-sky-500 text-white shadow-xs'
                                            : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 dark:text-white'
                                    ]"
                                >
                                    Belanja Program Kerja
                                </button>
                            </div>
                        </div>

                        <!-- Jika Pengeluaran Umum -->
                        <div v-if="linkType === 'umum'">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Jenis Pengeluaran</label>
                            <select 
                                v-model="selectedCategory"
                                class="w-full px-4 py-2 border border-slate-200 dark:border-slate-800 bg-transparent rounded-xl text-sm focus:outline-none focus:border-indigo-500 dark:text-white appearance-none"
                                required
                            >
                                <option value="" class="dark:bg-slate-900">-- Pilih Jenis Pengeluaran --</option>
                                <option 
                                    v-for="cat in expenseCategories" 
                                    :key="cat.value" 
                                    :value="cat.value"
                                    class="dark:bg-slate-900"
                                >
                                    {{ cat.label }}
                                </option>
                            </select>
                            <div v-if="selectedCategory === 'Lainnya'" class="mt-2">
                                <input 
                                    v-model="customCategory"
                                    type="text"
                                    placeholder="Tulis jenis pengeluaran manual..."
                                    required
                                    class="w-full px-4 py-2 border border-slate-200 dark:border-slate-800 bg-transparent rounded-xl text-sm focus:outline-none focus:border-indigo-500 dark:text-white"
                                />
                            </div>
                            <p v-if="form.errors.category" class="text-xs text-red-500 mt-1">{{ form.errors.category }}</p>
                        </div>

                        <!-- Jika Belanja Program Kerja -->
                        <div v-else>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Program Kerja</label>
                            <select 
                                v-model="form.program_kerja_id"
                                class="w-full px-4 py-2 border border-slate-200 dark:border-slate-800 bg-transparent rounded-xl text-sm focus:outline-none focus:border-indigo-500 dark:text-white appearance-none"
                                required
                            >
                                <option value="" class="dark:bg-slate-900">-- Pilih Program Kerja --</option>
                                <option 
                                    v-for="proker in programKerjas" 
                                    :key="proker.id" 
                                    :value="proker.id"
                                    class="dark:bg-slate-900"
                                >
                                    {{ proker.name }} (Estimasi: {{ formatRupiah(proker.budget) }})
                                </option>
                            </select>
                            <p v-if="form.errors.program_kerja_id" class="text-xs text-red-500 mt-1">{{ form.errors.program_kerja_id }}</p>
                        </div>
                    </div>

                    <!-- === ALLOCATION: Alokasi Dana Proker === -->
                    <div v-else-if="form.type === 'allocation'" class="space-y-3 mt-3">
                        <!-- Arah Alokasi Dana -->
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Arah Alokasi Dana</label>
                            <select 
                                v-model="selectedProkerCategory"
                                class="w-full px-4 py-2 border border-slate-200 dark:border-slate-800 bg-transparent rounded-xl text-sm focus:outline-none focus:border-indigo-500 dark:text-white appearance-none"
                                required
                            >
                                <option value="Kas ke Proker" class="dark:bg-slate-900">Kas Posko → Program Kerja</option>
                                <option value="Proker ke Kas" class="dark:bg-slate-900">Program Kerja → Kas Posko</option>
                            </select>
                            <p v-if="form.errors.category" class="text-xs text-red-500 mt-1">{{ form.errors.category }}</p>
                        </div>

                        <!-- Pilih Program Kerja -->
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Program Kerja</label>
                            <select 
                                v-model="form.program_kerja_id"
                                class="w-full px-4 py-2 border border-slate-200 dark:border-slate-800 bg-transparent rounded-xl text-sm focus:outline-none focus:border-indigo-500 dark:text-white appearance-none"
                                required
                            >
                                <option value="" class="dark:bg-slate-900">-- Pilih Program Kerja --</option>
                                <option 
                                    v-for="proker in programKerjas" 
                                    :key="proker.id" 
                                    :value="proker.id"
                                    class="dark:bg-slate-900"
                                >
                                    {{ proker.name }} (Estimasi: {{ formatRupiah(proker.budget) }})
                                </option>
                            </select>
                            <p v-if="form.errors.program_kerja_id" class="text-xs text-red-500 mt-1">{{ form.errors.program_kerja_id }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                        <!-- Payment Method -->
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                {{ form.type === 'transfer' ? 'Sumber Uang' : 
                                   (form.type === 'allocation' ? (selectedProkerCategory === 'Kas ke Proker' ? 'Sumber Uang (Dari Kas)' : 'Sumber Uang (Dari Proker)') : 
                                   'Penyimpanan / Rekening') }}
                            </label>
                            <select 
                                v-model="form.payment_method"
                                class="w-full px-4 py-2 border border-slate-200 dark:border-slate-800 bg-transparent rounded-xl text-sm focus:outline-none focus:border-indigo-500 dark:text-white appearance-none"
                                required
                            >
                                <option value="Cash" class="dark:bg-slate-900">Uang Tunai (Cash)</option>
                                <option value="SeaBank" class="dark:bg-slate-900">SeaBank</option>
                                <option value="DANA" class="dark:bg-slate-900">DANA</option>
                            </select>
                            <p v-if="form.errors.payment_method" class="text-xs text-red-500 mt-1">{{ form.errors.payment_method }}</p>
                        </div>
                        
                        <!-- Destination Payment Method (For Transfer & Allocation) -->
                        <div v-if="form.type === 'transfer' || form.type === 'allocation'">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                {{ form.type === 'transfer' ? 'Tujuan Uang' : 
                                   (selectedProkerCategory === 'Kas ke Proker' ? 'Tujuan Uang (Masuk Ke Proker)' : 'Tujuan Uang (Masuk Ke Kas)') }}
                            </label>
                            <select 
                                v-model="form.destination_payment_method"
                                class="w-full px-4 py-2 border border-slate-200 dark:border-slate-800 bg-transparent rounded-xl text-sm focus:outline-none focus:border-indigo-500 dark:text-white appearance-none"
                                :required="form.type === 'transfer' || form.type === 'allocation'"
                            >
                                <option value="" class="dark:bg-slate-900">-- Pilih Tujuan Uang --</option>
                                <option value="Cash" class="dark:bg-slate-900">Uang Tunai (Cash)</option>
                                <option value="SeaBank" class="dark:bg-slate-900">SeaBank</option>
                                <option value="DANA" class="dark:bg-slate-900">DANA</option>
                            </select>
                            <p v-if="form.errors.destination_payment_method" class="text-xs text-red-500 mt-1">{{ form.errors.destination_payment_method }}</p>
                        </div>
                    </div>

                    <!-- Balance info per rekening -->
                    <div v-if="['allocation', 'expense', 'transfer'].includes(form.type)" class="p-3 bg-indigo-50/50 dark:bg-indigo-950/10 rounded-xl border border-indigo-100 dark:border-indigo-950/30 mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Kas Balance -->
                        <div v-if="!(form.type === 'expense' && form.program_kerja_id)">
                            <h4 class="text-[10px] font-bold uppercase tracking-wider text-indigo-500 mb-2">Saldo Kas Tersedia</h4>
                            <div class="flex flex-wrap items-start gap-3 sm:gap-5">
                                <div v-for="(bal, method) in metrics.balances_by_method" :key="'kas_'+method" class="flex flex-col min-w-[70px]">
                                    <span class="text-[9px] md:text-[10px] text-slate-400 font-semibold">{{ method }}</span>
                                    <span 
                                        class="font-bold text-[11px] md:text-xs"
                                        :class="[bal > 0 ? 'text-slate-700 dark:text-slate-300' : 'text-red-500']"
                                    >
                                        {{ formatRupiah(bal) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Proker Balance (if selected) -->
                        <div v-if="form.program_kerja_id && metrics.proker_balances[form.program_kerja_id]">
                            <h4 class="text-[10px] font-bold uppercase tracking-wider text-indigo-500 mb-2">Saldo Proker Terpilih</h4>
                            <div class="flex flex-wrap items-start gap-3 sm:gap-5">
                                <div v-for="(bal, method) in metrics.proker_balances[form.program_kerja_id]" :key="'proker_'+method" class="flex flex-col min-w-[70px]">
                                    <span class="text-[9px] md:text-[10px] text-slate-400 font-semibold">{{ method }}</span>
                                    <span 
                                        class="font-bold text-[11px] md:text-xs"
                                        :class="[bal > 0 ? 'text-slate-700 dark:text-slate-300' : 'text-red-500']"
                                    >
                                        {{ formatRupiah(bal) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-3">
                        <!-- Nominal -->
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Nominal (Rp)</label>
                            <input 
                                :value="formattedAmount"
                                @input="onAmountInput"
                                type="text" 
                                placeholder="Contoh: 100.000"
                                required
                                class="w-full px-4 py-2 border border-slate-200 dark:border-slate-800 bg-transparent rounded-xl text-sm focus:outline-none focus:border-indigo-500 dark:text-white font-extrabold"
                            />
                            <p v-if="form.errors.amount" class="text-xs text-red-500 mt-1">{{ form.errors.amount }}</p>
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Tanggal</label>
                            <input 
                                v-model="form.date"
                                type="date" 
                                required
                                class="w-full px-4 py-2 border border-slate-200 dark:border-slate-800 bg-transparent rounded-xl text-sm focus:outline-none focus:border-indigo-500 dark:text-white"
                            />
                            <p v-if="form.errors.date" class="text-xs text-red-500 mt-1">{{ form.errors.date }}</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Keterangan / Rincian</label>
                        <textarea 
                            v-model="form.description"
                            rows="2"
                            placeholder="Detail belanja barang, sisa kembalian, donatur, dll."
                            class="w-full px-4 py-2 border border-slate-200 dark:border-slate-800 bg-transparent rounded-xl text-sm focus:outline-none focus:border-indigo-500 dark:text-white"
                        ></textarea>
                    </div>

                    <!-- Receipt Image File Upload -->
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Foto Nota / Bukti Belanja (Digital Receipt)</label>
                        
                        <div class="flex items-center gap-4">
                            <!-- Drag / Drop click upload area -->
                            <div 
                                @click="fileInput?.click()"
                                class="flex-1 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl p-4 flex flex-col items-center justify-center cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-950 transition-colors"
                            >
                                <input 
                                    ref="fileInput"
                                    type="file" 
                                    accept="image/*"
                                    class="hidden" 
                                    @change="handleFileChange"
                                />
                                <ImageIcon class="size-5 text-slate-400 mb-1" />
                                <span class="text-[10px] font-bold text-slate-500">PILIH FOTO NOTA</span>
                            </div>

                            <!-- Preview receipt thumbnail -->
                            <div v-if="filePreview" class="relative size-16 border rounded-xl overflow-hidden shrink-0">
                                <img :src="filePreview" alt="Receipt preview" class="size-full object-cover" />
                                <button 
                                    type="button" 
                                    @click="filePreview = null; form.receipt_file = null"
                                    class="absolute top-0.5 right-0.5 bg-black/70 text-white rounded-full p-0.5 hover:bg-black"
                                >
                                    <X class="size-3" />
                                </button>
                            </div>
                        </div>
                        <p v-if="form.errors.receipt_file" class="text-xs text-red-500 mt-1">{{ form.errors.receipt_file }}</p>
                    </div>

                    <!-- Footer actions -->
                    <div class="flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800 pt-4 mt-2">
                        <Button 
                            type="button" 
                            variant="outline"
                            class="rounded-xl"
                            @click="isModalOpen = false"
                        >
                            Batal
                        </Button>
                        <Button 
                            type="submit" 
                            class="bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl"
                            :disabled="form.processing"
                        >
                            <Spinner v-if="form.processing" class="size-4 mr-2" />
                            <span>{{ editingRecord ? 'Simpan Perubahan' : 'Catat Transaksi' }}</span>
                        </Button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tag Management Modal (no-print) -->
        <div 
            v-if="isTagModalOpen" 
            class="no-print fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4"
        >
            <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 shadow-xl relative animate-in fade-in zoom-in-95 duration-200 max-h-[90vh] overflow-y-auto">
                <button 
                    @click="isTagModalOpen = false" 
                    class="absolute top-4 right-4 p-1.5 rounded-full text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
                >
                    <X class="size-4" />
                </button>

                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
                    <Tag class="size-5 inline-block mr-1.5 -mt-0.5" />
                    Kelola Tag Keuangan
                </h3>

                <p class="text-xs text-slate-400 mb-4">
                    Tag custom yang ditambahkan hanya berlaku untuk kelompok Anda. Kelompok lain tidak akan melihat tag ini.
                </p>

                <!-- Add New Tag Form -->
                <form @submit.prevent="submitTagForm" class="flex flex-col gap-3 mb-5 p-3 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-100 dark:border-slate-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Tambah Tag Baru</h4>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <input 
                                v-model="tagForm.name"
                                type="text" 
                                placeholder="Nama tag..."
                                required
                                class="w-full px-3 py-2 border border-slate-200 dark:border-slate-800 bg-transparent rounded-xl text-sm focus:outline-none focus:border-indigo-500 dark:text-white"
                            />
                            <p v-if="tagForm.errors.name" class="text-[10px] text-red-500 mt-0.5">{{ tagForm.errors.name }}</p>
                        </div>
                        <div>
                            <select 
                                v-model="tagForm.type"
                                class="w-full px-3 py-2 border border-slate-200 dark:border-slate-800 bg-transparent rounded-xl text-sm focus:outline-none focus:border-indigo-500 dark:text-white appearance-none"
                            >
                                <option value="expense" class="dark:bg-slate-900">Pengeluaran</option>
                                <option value="income" class="dark:bg-slate-900">Pemasukan</option>
                            </select>
                        </div>
                    </div>
                    <Button 
                        type="submit" 
                        size="sm"
                        class="self-end rounded-xl bg-indigo-600 text-white hover:bg-indigo-700"
                        :disabled="tagForm.processing"
                    >
                        <Spinner v-if="tagForm.processing" class="size-3.5 mr-1.5" />
                        <CirclePlus v-else class="size-3.5 mr-1.5" />
                        <span>Tambah Tag</span>
                    </Button>
                </form>

                <!-- Existing Custom Tags -->
                <div v-if="customTags.length > 0" class="space-y-3">
                    <!-- Income Tags -->
                    <div v-if="customTags.filter(t => t.type === 'income').length > 0">
                        <h4 class="text-[10px] font-bold uppercase tracking-wider text-emerald-500 mb-1.5">Tag Pemasukan Custom</h4>
                        <div class="flex flex-wrap gap-1.5">
                            <span 
                                v-for="tag in customTags.filter(t => t.type === 'income')" 
                                :key="tag.id"
                                class="inline-flex items-center gap-1 text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400 px-2.5 py-1 rounded-lg"
                            >
                                {{ tag.name }}
                                <button 
                                    @click="deleteTag(tag)"
                                    class="ml-0.5 p-0.5 rounded-full hover:bg-emerald-200 dark:hover:bg-emerald-900/40 transition-colors"
                                    title="Hapus tag"
                                >
                                    <X class="size-3" />
                                </button>
                            </span>
                        </div>
                    </div>

                    <!-- Expense Tags -->
                    <div v-if="customTags.filter(t => t.type === 'expense').length > 0">
                        <h4 class="text-[10px] font-bold uppercase tracking-wider text-red-500 mb-1.5">Tag Pengeluaran Custom</h4>
                        <div class="flex flex-wrap gap-1.5">
                            <span 
                                v-for="tag in customTags.filter(t => t.type === 'expense')" 
                                :key="tag.id"
                                class="inline-flex items-center gap-1 text-xs font-semibold bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 px-2.5 py-1 rounded-lg"
                            >
                                {{ tag.name }}
                                <button 
                                    @click="deleteTag(tag)"
                                    class="ml-0.5 p-0.5 rounded-full hover:bg-red-200 dark:hover:bg-red-900/40 transition-colors"
                                    title="Hapus tag"
                                >
                                    <X class="size-3" />
                                </button>
                            </span>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-6 text-slate-400 text-xs">
                    Belum ada tag custom. Tag bawaan (Iuran Anggota, Konsumsi, dll.) selalu tersedia.
                </div>

                <!-- Default Tags Info -->
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Tag Bawaan (Semua Kelompok)</h4>
                    <div class="flex flex-wrap gap-1">
                        <span class="text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 px-2 py-0.5 rounded-md">Iuran Anggota</span>
                        <span class="text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 px-2 py-0.5 rounded-md">Sponsor</span>
                        <span class="text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 px-2 py-0.5 rounded-md">Donasi / Sumbangan</span>
                        <span class="text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 px-2 py-0.5 rounded-md">Dana Kampus</span>
                        <span class="text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 px-2 py-0.5 rounded-md">Konsumsi</span>
                        <span class="text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 px-2 py-0.5 rounded-md">Transportasi</span>
                        <span class="text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 px-2 py-0.5 rounded-md">Perlengkapan &amp; Bahan</span>
                        <span class="text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 px-2 py-0.5 rounded-md">Humas &amp; Publikasi</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lightbox Nota Bukti (no-print) -->
        <Transition name="fade">
            <div 
                v-if="previewImage" 
                class="no-print fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4"
                @click.self="previewImage = null"
            >
                <div class="relative max-w-[95vw] sm:max-w-3xl max-h-[90vh]">
                    <img :src="previewImage" alt="Nota Belanja Digital" class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl" />
                    <button 
                        @click="previewImage = null"
                        class="absolute top-4 right-4 bg-black/60 text-white hover:bg-black p-2 rounded-full transition-all"
                    >
                        <X class="size-5" />
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
