<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { 
    Plus, Edit3, Trash2, Calendar, CheckCircle, Clock, BookOpen, User, Image as ImageIcon, X, FileText, CheckSquare, Target, Search, Wallet
} from '@lucide/vue';
import { ref, computed } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { useConfirm } from '@/composables/useConfirm';
import { useInitials } from '@/composables/useInitials';
import { useToast } from '@/composables/useToast';

const { confirm } = useConfirm();
const toast = useToast();
const { getInitials } = useInitials();

interface Member {
    id: number;
    name: string;
    email: string;
    avatar?: string;
}

interface ProgramKerja {
    id: number;
    name: string;
    category: 'fisik' | 'non_fisik' | 'keagamaan' | 'kesehatan' | 'pendidikan' | 'tambahan';
    description: string | null;
    progress: number;
    budget: number;
    status: 'planned' | 'in_progress' | 'completed';
    pic_id: number | null;
    pic?: Member | null;
}

interface Logbook {
    id: number;
    user_id: number;
    title: string;
    description: string;
    date: string;
    activity_type: 'internal' | 'community';
    scope: 'personal' | 'group';
    image_path: string | null;
    user?: Member | null;
}

const props = defineProps<{
    logbooks: Logbook[];
    programKerjas: ProgramKerja[];
    members: Member[];
    canWriteFinance: boolean;
    isHostOrSekretaris: boolean;
    canWriteGroupLogbook: boolean;
    authUserId: number;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Logbook & Proker',
                href: '/logbook',
            },
        ],
    },
});

// Active Tab
const activeTab = ref<'proker' | 'logbook'>('proker');

// Search Query
const prokerSearch = ref('');
const logbookSearch = ref('');
const logbookScopeFilter = ref<'all' | 'group' | 'personal'>('all');

// Format Currency
const formatRupiah = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};

// Filtered Proker
const filteredProkers = computed(() => {
    return props.programKerjas.filter(proker => {
        const matchesSearch = 
            proker.name.toLowerCase().includes(prokerSearch.value.toLowerCase()) ||
            (proker.description && proker.description.toLowerCase().includes(prokerSearch.value.toLowerCase())) ||
            (proker.pic && proker.pic.name.toLowerCase().includes(prokerSearch.value.toLowerCase()));

        return matchesSearch;
    });
});

// Filtered Logbooks
const filteredLogbooks = computed(() => {
    return props.logbooks.filter(log => {
        const matchesSearch = 
            log.title.toLowerCase().includes(logbookSearch.value.toLowerCase()) ||
            log.description.toLowerCase().includes(logbookSearch.value.toLowerCase()) ||
            (log.user && log.user.name.toLowerCase().includes(logbookSearch.value.toLowerCase()));

        const matchesScope = 
            logbookScopeFilter.value === 'all' || 
            log.scope === logbookScopeFilter.value;

        return matchesSearch && matchesScope;
    });
});

// Proker metrics
const prokerMetrics = computed(() => {
    const list = props.programKerjas;
    const total = list.length;
    const completed = list.filter(p => p.status === 'completed').length;
    const inProgress = list.filter(p => p.status === 'in_progress').length;
    const planned = list.filter(p => p.status === 'planned').length;
    
    const avgProgress = total > 0 ? Math.round(list.reduce((sum, p) => sum + p.progress, 0) / total) : 0;
    const totalBudget = list.reduce((sum, p) => sum + p.budget, 0);
    const totalSpent = list.reduce((sum, p) => sum + (p.spent || 0), 0);

    return { total, completed, inProgress, planned, avgProgress, totalBudget, totalSpent };
});

// Category labels and styles
const getCategoryDetails = (cat: string) => {
    switch (cat) {
        case 'fisik':
            return { label: 'Fisik / Infrastruktur', class: 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20' };
        case 'non_fisik':
            return { label: 'Non-Fisik / Sosial', class: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' };
        case 'keagamaan':
            return { label: 'Keagamaan', class: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20' };
        case 'kesehatan':
            return { label: 'Kesehatan & Sanitasi', class: 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20' };
        case 'pendidikan':
            return { label: 'Pendidikan & Kesenian', class: 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-500/10 dark:text-sky-400 dark:border-sky-500/20' };
        default:
            return { label: 'Tambahan / Lainya', class: 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-500/10 dark:text-slate-400 dark:border-slate-500/20' };
    }
};

// Status details
const getStatusDetails = (status: string) => {
    switch (status) {
        case 'planned':
            return { label: 'Direncanakan', icon: Target, class: 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700' };
        case 'in_progress':
            return { label: 'Berjalan', icon: Clock, class: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20' };
        case 'completed':
            return { label: 'Selesai', icon: CheckCircle, class: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' };
        default:
            return { label: 'Unknown', icon: Clock, class: 'bg-slate-100 text-slate-700' };
    }
};

// --- PROKER FINANCE MODAL ---
const isProkerFinanceModalOpen = ref(false);
const selectedProkerForFinance = ref<any>(null);

const openProkerFinanceModal = (proker: any) => {
    selectedProkerForFinance.value = proker;
    isProkerFinanceModalOpen.value = true;
};

const closeProkerFinanceModal = () => {
    isProkerFinanceModalOpen.value = false;
    selectedProkerForFinance.value = null;
};

const formattedBudget = computed(() => {
    if (!prokerForm.budget) return '';
    return Number(prokerForm.budget).toLocaleString('id-ID');
});

const onBudgetInput = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const cleanValue = target.value.replace(/\D/g, '');
    prokerForm.budget = cleanValue ? parseInt(cleanValue, 10) : 0;
};

// --- PROKER MODAL & ACTION HANDLERS ---
const isProkerModalOpen = ref(false);
const isProkerEditMode = ref(false);
const editingProkerId = ref<number | null>(null);

const prokerForm = useForm({
    name: '',
    category: 'fisik' as any,
    description: '',
    progress: 0,
    budget: 0,
    pic_id: '' as string | number,
    status: 'planned' as 'planned' | 'in_progress' | 'completed',
});

const openCreateProkerModal = () => {
    isProkerEditMode.value = false;
    editingProkerId.value = null;
    prokerForm.reset();
    prokerForm.pic_id = '';
    isProkerModalOpen.value = true;
};

const openEditProkerModal = (proker: ProgramKerja) => {
    isProkerEditMode.value = true;
    editingProkerId.value = proker.id;
    prokerForm.reset();
    prokerForm.name = proker.name;
    prokerForm.category = proker.category;
    prokerForm.description = proker.description || '';
    prokerForm.progress = proker.progress;
    prokerForm.budget = proker.budget;
    prokerForm.pic_id = proker.pic_id || '';
    prokerForm.status = proker.status;
    isProkerModalOpen.value = true;
};

const closeProkerModal = () => {
    isProkerModalOpen.value = false;
    prokerForm.reset();
};

const submitProkerForm = () => {
    if (isProkerEditMode.value && editingProkerId.value) {
        prokerForm.put(`/logbook/proker/${editingProkerId.value}`, {
            onSuccess: () => {
                toast.success('Program kerja berhasil diperbarui.');
                closeProkerModal();
            },
            onError: () => {
                toast.error('Gagal memperbarui program kerja. Periksa kembali input Anda.');
            }
        });
    } else {
        prokerForm.post('/logbook/proker', {
            onSuccess: () => {
                toast.success('Program kerja baru berhasil ditambahkan.');
                closeProkerModal();
            },
            onError: () => {
                toast.error('Gagal menambahkan program kerja. Periksa kembali input Anda.');
            }
        });
    }
};

const confirmDeleteProker = async (proker: ProgramKerja) => {
    const isConfirmed = await confirm({
        title: 'Hapus Program Kerja?',
        message: `Apakah Anda yakin ingin menghapus program kerja <strong>${proker.name}</strong>? Tindakan ini juga akan menghapus data progres terkait.`,
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        variant: 'destructive',
    });

    if (isConfirmed) {
        router.delete(`/logbook/proker/${proker.id}`, {
            onSuccess: () => {
                toast.success('Program Kerja berhasil dihapus.');
            },
            onError: () => {
                toast.error('Gagal menghapus program kerja.');
            }
        });
    }
};

// --- LOGBOOK MODAL & ACTION HANDLERS ---
const isLogbookModalOpen = ref(false);
const isLogbookEditMode = ref(false);
const editingLogbookId = ref<number | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);
const imagePreview = ref<string | null>(null);

const logbookForm = useForm({
    _method: 'POST',
    title: '',
    date: new Date().toISOString().split('T')[0],
    description: '',
    activity_type: 'internal' as 'internal' | 'community',
    scope: 'personal' as 'personal' | 'group',
    image: null as File | null,
});

const openCreateLogbookModal = () => {
    isLogbookEditMode.value = false;
    editingLogbookId.value = null;
    imagePreview.value = null;
    logbookForm.reset();
    logbookForm._method = 'POST';
    logbookForm.scope = 'personal';
    isLogbookModalOpen.value = true;
};

const openEditLogbookModal = (log: Logbook) => {
    isLogbookEditMode.value = true;
    editingLogbookId.value = log.id;
    imagePreview.value = log.image_path ? '/storage/' + log.image_path : null;
    logbookForm.reset();
    logbookForm._method = 'POST'; // Spoofing PUT using POST + _method
    logbookForm.title = log.title;
    logbookForm.date = log.date;
    logbookForm.description = log.description;
    logbookForm.activity_type = log.activity_type;
    logbookForm.scope = log.scope || 'personal';
    logbookForm.image = null;
    isLogbookModalOpen.value = true;
};

const closeLogbookModal = () => {
    isLogbookModalOpen.value = false;
    imagePreview.value = null;

    if (fileInput.value) {
        fileInput.value.value = '';
    }

    logbookForm.reset();
};

const handleImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement;

    if (target.files && target.files[0]) {
        const file = target.files[0];
        logbookForm.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submitLogbookForm = () => {
    if (isLogbookEditMode.value && editingLogbookId.value) {
        logbookForm._method = 'PUT';
        logbookForm.post(`/logbook/daily/${editingLogbookId.value}`, {
            onSuccess: () => {
                toast.success('Logbook harian berhasil diperbarui.');
                closeLogbookModal();
            },
            onError: () => {
                toast.error('Gagal memperbarui logbook. Periksa kembali input Anda.');
            }
        });
    } else {
        logbookForm._method = 'POST';
        logbookForm.post('/logbook/daily', {
            onSuccess: () => {
                toast.success('Logbook harian berhasil ditambahkan.');
                closeLogbookModal();
            },
            onError: () => {
                toast.error('Gagal menambahkan logbook. Periksa kembali input Anda.');
            }
        });
    }
};

const confirmDeleteLogbook = async (log: Logbook) => {
    const isConfirmed = await confirm({
        title: 'Hapus Catatan Logbook?',
        message: `Apakah Anda yakin ingin menghapus catatan logbook <strong>${log.title}</strong>? Tindakan ini tidak dapat dibatalkan.`,
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        variant: 'destructive',
    });

    if (isConfirmed) {
        router.delete(`/logbook/daily/${log.id}`, {
            onSuccess: () => {
                toast.success('Catatan logbook berhasil dihapus.');
            },
            onError: () => {
                toast.error('Gagal menghapus catatan logbook.');
            }
        });
    }
};
</script>

<template>
    <Head title="Digital Logbook & Proker - SuperPosko" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-7xl mx-auto font-sans">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Digital Logbook & Program Kerja</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Kelola realisasi program kerja posko serta catat laporan kegiatan harian KKN secara kolaboratif.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <Button 
                    v-if="activeTab === 'proker' && canWriteFinance"
                    @click="openCreateProkerModal" 
                    class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-4 py-2.5 rounded-xl transition duration-200 flex items-center gap-2 cursor-pointer shadow-xs"
                >
                    <Plus class="size-4" /> Tambah Proker
                </Button>

                <Button 
                    v-if="activeTab === 'logbook'"
                    @click="openCreateLogbookModal" 
                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl transition duration-200 flex items-center gap-2 cursor-pointer shadow-xs"
                >
                    <Plus class="size-4" /> Catat Logbook
                </Button>
            </div>
        </div>

        <!-- Custom tabs -->
        <div class="border-b border-slate-200 dark:border-slate-800 flex gap-4">
            <button
                @click="activeTab = 'proker'"
                :class="[
                    'pb-3 text-sm font-bold border-b-2 transition duration-200 px-1 cursor-pointer',
                    activeTab === 'proker' 
                        ? 'border-sky-500 text-sky-600 dark:text-sky-400' 
                        : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400'
                ]"
            >
                Program Kerja (Proker)
            </button>
            <button
                @click="activeTab = 'logbook'"
                :class="[
                    'pb-3 text-sm font-bold border-b-2 transition duration-200 px-1 cursor-pointer',
                    activeTab === 'logbook' 
                        ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' 
                        : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400'
                ]"
            >
                Logbook Harian
            </button>
        </div>

        <!-- TAB 1: PROGRAM KERJA -->
        <div v-if="activeTab === 'proker'" class="space-y-6 animate-in fade-in duration-200">
            <!-- Metrics Cards Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
                    <div class="flex items-center gap-2 text-slate-400 dark:text-slate-500 mb-1">
                        <Target class="size-4" />
                        <span class="text-xs font-bold uppercase tracking-wider">Total Proker</span>
                    </div>
                    <p class="text-xl font-extrabold text-slate-900 dark:text-white">{{ prokerMetrics.total }}</p>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">
                        {{ prokerMetrics.completed }} Selesai &bull; {{ prokerMetrics.inProgress }} Berjalan
                    </p>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs">
                    <div class="flex items-center gap-2 text-slate-400 dark:text-slate-500 mb-1">
                        <CheckSquare class="size-4" />
                        <span class="text-xs font-bold uppercase tracking-wider">Rata-rata Progres</span>
                    </div>
                    <p class="text-xl font-extrabold text-slate-900 dark:text-white">{{ prokerMetrics.avgProgress }}%</p>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden mt-2.5">
                        <div class="bg-sky-500 h-1.5 rounded-full transition-all" :style="`width: ${prokerMetrics.avgProgress}%`"></div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-xs col-span-2">
                    <div class="flex items-center gap-2 text-slate-400 dark:text-slate-500 mb-2">
                        <FileText class="size-4" />
                        <span class="text-xs font-bold uppercase tracking-wider">Anggaran Program Kerja</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-[10px] text-slate-400 font-semibold uppercase block">Total Rencana</span>
                            <p class="text-base font-extrabold text-slate-700 dark:text-slate-350">{{ formatRupiah(prokerMetrics.totalBudget) }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-semibold uppercase block">Total Belanja</span>
                            <p 
                                class="text-base font-extrabold"
                                :class="[prokerMetrics.totalSpent > prokerMetrics.totalBudget ? 'text-red-500' : 'text-emerald-500']"
                            >
                                {{ formatRupiah(prokerMetrics.totalSpent) }}
                            </p>
                        </div>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1 rounded-full overflow-hidden mt-3" v-if="prokerMetrics.totalBudget > 0">
                        <div 
                            class="h-1 rounded-full transition-all" 
                            :class="[prokerMetrics.totalSpent > prokerMetrics.totalBudget ? 'bg-red-500' : 'bg-emerald-500']"
                            :style="`width: ${Math.min((prokerMetrics.totalSpent / prokerMetrics.totalBudget) * 100, 100)}%`"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- Search & Filter Controls -->
            <div class="flex rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                <div class="relative w-full md:w-96">
                    <Search class="absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="prokerSearch"
                        type="text"
                        placeholder="Cari program kerja, deskripsi, atau PIC..."
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-800 pl-10 pr-4 py-2 text-sm focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                    />
                </div>
            </div>

            <!-- Proker Lists segmented by Status -->
            <div v-if="filteredProkers.length > 0" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Status Sections -->
                <div v-for="statusType in ['planned', 'in_progress', 'completed']" :key="statusType" class="space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-2">
                        <div class="flex items-center gap-2">
                            <span :class="['px-2 py-0.5 rounded-full border text-[10px] font-bold uppercase tracking-wide flex items-center gap-1', getStatusDetails(statusType).class]">
                                <component :is="getStatusDetails(statusType).icon" class="size-3" />
                                {{ getStatusDetails(statusType).label }}
                            </span>
                        </div>
                        <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">
                            {{ filteredProkers.filter(p => p.status === statusType).length }} item
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div 
                            v-for="proker in filteredProkers.filter(p => p.status === statusType)" 
                            :key="proker.id"
                            class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-xs flex flex-col justify-between hover:shadow-md transition duration-300"
                        >
                            <div>
                                <!-- Header: Category & Admin Tools -->
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span :class="['px-2 py-0.5 rounded-full border text-[9px] font-bold', getCategoryDetails(proker.category).class]">
                                        {{ getCategoryDetails(proker.category).label }}
                                    </span>

                                    <!-- Actions -->
                                    <div v-if="canWriteFinance || proker.pic_id === authUserId" class="flex gap-1">
                                        <button 
                                            @click="openEditProkerModal(proker)" 
                                            class="p-1 text-slate-400 hover:text-sky-500 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer"
                                            title="Edit Proker"
                                        >
                                            <Edit3 class="size-3.5" />
                                        </button>
                                        <button 
                                            v-if="isHostOrSekretaris"
                                            @click="confirmDeleteProker(proker)" 
                                            class="p-1 text-slate-400 hover:text-red-500 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer"
                                            title="Hapus Proker"
                                        >
                                            <Trash2 class="size-3.5" />
                                        </button>
                                    </div>
                                </div>

                                <!-- Proker Name & target description -->
                                <h3 class="font-extrabold text-slate-900 dark:text-white text-sm sm:text-base leading-snug mb-1">{{ proker.name }}</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-3 mb-3">{{ proker.description || 'Tidak ada deskripsi program.' }}</p>
                            </div>

                            <!-- Progress & Budget -->
                            <div class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-850">
                                <div>
                                    <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                        <span class="text-slate-400">Progres Kegiatan</span>
                                        <span class="text-slate-800 dark:text-slate-300 font-bold">{{ proker.progress }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                        <div 
                                            class="h-1.5 rounded-full transition-all" 
                                            :class="[proker.progress === 100 ? 'bg-emerald-500' : 'bg-sky-500']"
                                            :style="`width: ${proker.progress}%`"
                                        ></div>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-1 border-t border-slate-100 dark:border-slate-800 pt-2 mt-2">
                                    <div class="flex items-center justify-between text-[11px] md:text-xs">
                                        <span class="font-semibold text-slate-400">Estimasi Anggaran</span>
                                        <span class="font-semibold text-slate-700 dark:text-slate-350">{{ formatRupiah(proker.budget) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-[11px] md:text-xs">
                                        <span class="font-semibold text-slate-400">Realisasi Belanja</span>
                                        <span 
                                            class="font-extrabold"
                                            :class="[proker.spent > (proker.budget + proker.earned) ? 'text-red-500 dark:text-red-400' : proker.spent > 0 ? 'text-amber-500 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-450']"
                                        >
                                            {{ formatRupiah(proker.spent || 0) }}
                                        </span>
                                    </div>
                                    <div v-if="proker.earned > 0" class="flex items-center justify-between text-[11px] md:text-xs">
                                        <span class="font-semibold text-slate-400">Pemasukan Proker</span>
                                        <span class="font-bold text-emerald-600">{{ formatRupiah(proker.earned) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-[11px] md:text-xs border-t border-dashed border-slate-100 dark:border-slate-850 pt-1 mt-1">
                                        <span class="font-semibold text-slate-400">Dana Tersedia</span>
                                        <span 
                                            class="font-extrabold text-emerald-600 dark:text-emerald-450"
                                        >
                                            {{ formatRupiah((proker.spent || 0) + (proker.earned || 0)) }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1 rounded-full overflow-hidden mt-1.5" v-if="proker.budget > 0">
                                        <div 
                                            class="h-1 rounded-full transition-all" 
                                            :class="[proker.spent > proker.budget ? 'bg-red-500' : 'bg-emerald-500']"
                                            :style="`width: ${Math.min((proker.spent / proker.budget) * 100, 100)}%`"
                                        ></div>
                                    </div>
                                </div>

                                <!-- PIC Info & Finance Action -->
                                <div class="flex items-center justify-between text-xs pt-2 mt-2 border-t border-slate-100 dark:border-slate-800">
                                    <span class="font-semibold text-slate-400">Penanggung Jawab</span>
                                    <div class="flex items-center gap-1.5">
                                        <Avatar v-if="proker.pic" class="size-5 overflow-hidden rounded-full shrink-0">
                                            <AvatarImage v-if="proker.pic.avatar" :src="proker.pic.avatar" :alt="proker.pic.name" />
                                            <AvatarFallback class="text-[8px] bg-indigo-100 text-indigo-800 font-extrabold dark:bg-indigo-950 dark:text-indigo-300">
                                                {{ getInitials(proker.pic.name) }}
                                            </AvatarFallback>
                                        </Avatar>
                                        <Avatar v-else class="size-5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                                            <User class="size-3 text-slate-400" />
                                        </Avatar>
                                        <span class="font-bold text-slate-700 dark:text-slate-300 truncate max-w-[80px]">
                                            {{ proker.pic ? proker.pic.name : 'Posko' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="pt-2 flex justify-start">
                                    <button 
                                        type="button" 
                                        @click="openProkerFinanceModal(proker)"
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-sky-500 hover:text-sky-600 transition cursor-pointer"
                                    >
                                        <Wallet class="size-3.5" />
                                        <span>Aliran Kas & Transaksi</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-else class="flex flex-col items-center justify-center py-16 px-4 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900 text-center">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 flex items-center justify-center text-slate-400 mb-4">
                    <Target class="size-6" />
                </div>
                <h3 class="font-bold text-slate-800 dark:text-slate-200 text-base mb-1">Program Kerja Tidak Ditemukan</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm">Belum ada program kerja yang ditambahkan atau tidak sesuai dengan kata kunci pencarian Anda.</p>
            </div>
        </div>

        <!-- TAB 2: LOGBOOK HARIAN -->
        <div v-if="activeTab === 'logbook'" class="space-y-6 animate-in fade-in duration-200">
            <!-- Search & Scope Filter Controls -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
                <div class="relative w-full md:w-96">
                    <Search class="absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="logbookSearch"
                        type="text"
                        placeholder="Cari logbook, judul, isi laporan, atau penulis..."
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-800 pl-10 pr-4 py-2 text-sm focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                    />
                </div>

                <!-- Scope Filter Buttons -->
                <div class="flex bg-slate-100 dark:bg-slate-950 p-1 rounded-xl w-full md:w-auto md:min-w-[280px]">
                    <button
                        @click="logbookScopeFilter = 'all'"
                        :class="[
                            'flex-1 text-center py-1.5 px-3 text-xs font-bold rounded-lg transition duration-200 cursor-pointer',
                            logbookScopeFilter === 'all'
                                ? 'bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-xs'
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400'
                        ]"
                    >
                        Semua
                    </button>
                    <button
                        @click="logbookScopeFilter = 'group'"
                        :class="[
                            'flex-1 text-center py-1.5 px-3 text-xs font-bold rounded-lg transition duration-200 cursor-pointer',
                            logbookScopeFilter === 'group'
                                ? 'bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-xs'
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400'
                        ]"
                    >
                        Kelompok
                    </button>
                    <button
                        @click="logbookScopeFilter = 'personal'"
                        :class="[
                            'flex-1 text-center py-1.5 px-3 text-xs font-bold rounded-lg transition duration-200 cursor-pointer',
                            logbookScopeFilter === 'personal'
                                ? 'bg-white dark:bg-slate-800 text-slate-800 dark:text-white shadow-xs'
                                : 'text-slate-500 hover:text-slate-700 dark:text-slate-400'
                        ]"
                    >
                        Pribadi
                    </button>
                </div>
            </div>

            <!-- Timeline View -->
            <div v-if="filteredLogbooks.length > 0" class="relative pl-6 border-l border-slate-200 dark:border-slate-800 ml-4 space-y-8 py-2">
                <div 
                    v-for="log in filteredLogbooks" 
                    :key="log.id"
                    class="relative"
                >
                    <!-- Marker bullet point -->
                    <span class="absolute -left-[31px] top-1.5 flex h-4 w-4 items-center justify-center rounded-full border-2 border-white dark:border-slate-950 bg-emerald-500 shadow-xs"></span>

                    <!-- Logbook card -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden max-w-3xl hover:border-slate-300 dark:hover:border-slate-700 transition duration-300">
                        <div class="p-5">
                            
                            <!-- Header with date, activity type & actions -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3 pb-3 border-b border-slate-100 dark:border-slate-850">
                                <div class="flex flex-wrap items-center gap-2">
                                    <div class="flex items-center gap-1.5 text-xs font-bold text-slate-500 dark:text-slate-400">
                                        <Calendar class="size-3.5 text-slate-400" />
                                        {{ log.date }}
                                    </div>
                                    <span :class="[
                                        'px-2 py-0.5 rounded-full border text-[9px] font-bold uppercase tracking-wider',
                                        log.activity_type === 'community' 
                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' 
                                            : 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-850 dark:text-slate-300 dark:border-slate-800'
                                    ]">
                                        {{ log.activity_type === 'community' ? 'Bakti Masyarakat' : 'Internal Posko' }}
                                    </span>
                                    <span :class="[
                                        'px-2 py-0.5 rounded-full border text-[9px] font-bold uppercase tracking-wider',
                                        log.scope === 'group' 
                                            ? 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20' 
                                            : 'bg-teal-50 text-teal-700 border-teal-200 dark:bg-teal-500/10 dark:text-teal-400 dark:border-teal-500/20'
                                    ]">
                                        {{ log.scope === 'group' ? 'Kelompok' : 'Pribadi' }}
                                    </span>
                                </div>

                                <!-- Actions: must be author or host/sekretaris -->
                                <div v-if="log.user_id === authUserId || isHostOrSekretaris" class="flex gap-1 justify-end">
                                    <button 
                                        @click="openEditLogbookModal(log)" 
                                        class="p-1 text-slate-400 hover:text-sky-500 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer"
                                        title="Edit Logbook"
                                    >
                                        <Edit3 class="size-3.5" />
                                    </button>
                                    <button 
                                        @click="confirmDeleteLogbook(log)" 
                                        class="p-1 text-slate-400 hover:text-red-500 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer"
                                        title="Hapus Logbook"
                                    >
                                        <Trash2 class="size-3.5" />
                                    </button>
                                </div>
                            </div>

                            <!-- Content -->
                            <h3 class="font-extrabold text-slate-900 dark:text-white text-base sm:text-lg mb-2 leading-snug">{{ log.title }}</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-350 whitespace-pre-line leading-relaxed mb-4">{{ log.description }}</p>

                            <!-- Documentation Image -->
                            <div v-if="log.image_path" class="mt-4 relative rounded-xl overflow-hidden max-h-80 bg-slate-100 dark:bg-slate-950 border border-slate-150 dark:border-slate-850">
                                <img :src="'/storage/' + log.image_path" alt="Dokumentasi Kegiatan" class="w-full max-h-80 object-contain mx-auto" />
                            </div>
                        </div>

                        <!-- Footer: Logged By -->
                        <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-850 bg-slate-50/50 dark:bg-slate-900/50 flex items-center gap-2">
                            <Avatar v-if="log.user" class="size-6 overflow-hidden rounded-full shrink-0">
                                <AvatarImage v-if="log.user.avatar" :src="log.user.avatar" :alt="log.user.name" />
                                <AvatarFallback class="text-[9px] bg-emerald-100 text-emerald-800 font-extrabold dark:bg-emerald-950 dark:text-emerald-300">
                                    {{ getInitials(log.user.name) }}
                                </AvatarFallback>
                            </Avatar>
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                                Dilaporkan oleh <strong class="text-slate-700 dark:text-slate-300 font-bold">{{ log.user ? log.user.name : 'Anggota Posko' }}</strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-else class="flex flex-col items-center justify-center py-16 px-4 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900 text-center">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 flex items-center justify-center text-slate-400 mb-4">
                    <BookOpen class="size-6" />
                </div>
                <h3 class="font-bold text-slate-800 dark:text-slate-200 text-base mb-1">Belum Ada Catatan Logbook</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm">Tulis catatan kegiatan harian kelompok KKN Anda sekarang untuk mendokumentasikan setiap proses program.</p>
            </div>
        </div>

        <!-- PROKER FORM MODAL -->
        <div v-if="isProkerModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
            <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-950">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <Target class="size-5 text-sky-500" />
                        {{ isProkerEditMode ? 'Edit Program Kerja' : 'Tambah Program Kerja Baru' }}
                    </h3>
                    <button @click="closeProkerModal" class="p-1 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-400 transition cursor-pointer">
                        <X class="size-5" />
                    </button>
                </div>

                <form @submit.prevent="submitProkerForm" class="p-5 space-y-4 max-h-[80vh] overflow-y-auto">
                    <!-- Name Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Program Kerja</label>
                        <input
                            v-model="prokerForm.name"
                            type="text"
                            placeholder="Contoh: Pengadaan Plang Dusun, Mengajar TPA"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                            required
                        />
                        <p v-if="prokerForm.errors.name" class="text-xs text-red-500">{{ prokerForm.errors.name }}</p>
                    </div>

                    <!-- Category Select -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Kategori Program</label>
                        <select
                            v-model="prokerForm.category"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none bg-white dark:bg-slate-950 dark:text-white"
                            required
                        >
                            <option value="fisik">Fisik / Pembangunan Infrastruktur</option>
                            <option value="non_fisik">Non-Fisik / Kegiatan Sosial</option>
                            <option value="keagamaan">Keagamaan & Pembinaan Keagamaan</option>
                            <option value="kesehatan">Kesehatan & Sanitasi Dusun</option>
                            <option value="pendidikan">Pendidikan, Bahasa, & Kesenian</option>
                            <option value="tambahan">Tambahan / Pendukung</option>
                        </select>
                        <p v-if="prokerForm.errors.category" class="text-xs text-red-500">{{ prokerForm.errors.category }}</p>
                    </div>

                    <!-- Description Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Deskripsi & Sasaran Kegiatan</label>
                        <textarea
                            v-model="prokerForm.description"
                            rows="3"
                            placeholder="Tuliskan detail program, target sasaran, serta output program yang diharapkan..."
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                        ></textarea>
                        <p v-if="prokerForm.errors.description" class="text-xs text-red-500">{{ prokerForm.errors.description }}</p>
                    </div>

                    <!-- PIC Select -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Penanggung Jawab (PIC)</label>
                        <select
                            v-model="prokerForm.pic_id"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none bg-white dark:bg-slate-950 dark:text-white"
                        >
                            <option value="">Posko Bersama</option>
                            <option v-for="member in members" :key="member.id" :value="member.id">
                                {{ member.name }}
                            </option>
                        </select>
                        <p v-if="prokerForm.errors.pic_id" class="text-xs text-red-500">{{ prokerForm.errors.pic_id }}</p>
                    </div>

                    <!-- Budget Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Estimasi Pengeluaran (Rp)</label>
                        <input
                            :value="formattedBudget"
                            @input="onBudgetInput"
                            type="text"
                            placeholder="Contoh: 150.000"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                            required
                        />
                        <p v-if="prokerForm.errors.budget" class="text-xs text-red-500">{{ prokerForm.errors.budget }}</p>
                    </div>

                    <!-- Progress Input -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs font-semibold">
                            <label class="text-slate-700 dark:text-slate-300">Progres ({{ prokerForm.progress }}%)</label>
                        </div>
                        <input
                            v-model.number="prokerForm.progress"
                            type="range"
                            min="0"
                            max="100"
                            step="5"
                            class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-sky-500 focus:outline-none"
                        />
                        <p v-if="prokerForm.errors.progress" class="text-xs text-red-500">{{ prokerForm.errors.progress }}</p>
                    </div>

                    <!-- Status Select -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Status Program</label>
                        <select
                            v-model="prokerForm.status"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none bg-white dark:bg-slate-950 dark:text-white"
                            required
                        >
                            <option value="planned">Direncanakan (Planned)</option>
                            <option value="in_progress">Berjalan (In Progress)</option>
                            <option value="completed">Selesai (Completed)</option>
                        </select>
                        <p v-if="prokerForm.errors.status" class="text-xs text-red-500">{{ prokerForm.errors.status }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-850 flex justify-end gap-2">
                        <Button type="button" variant="outline" @click="closeProkerModal" class="rounded-xl px-4 cursor-pointer">
                            Batal
                        </Button>
                        <Button
                            type="submit"
                            :disabled="prokerForm.processing"
                            class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-5 py-2 rounded-xl flex items-center gap-2 cursor-pointer"
                        >
                            <Spinner v-if="prokerForm.processing" />
                            {{ isProkerEditMode ? 'Simpan' : 'Tambah Proker' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>

        <!-- LOGBOOK HARIAN FORM MODAL -->
        <div v-if="isLogbookModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
            <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-950">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <BookOpen class="size-5 text-emerald-500" />
                        {{ isLogbookEditMode ? 'Edit Catatan Logbook' : 'Tambah Catatan Logbook Baru' }}
                    </h3>
                    <button @click="closeLogbookModal" class="p-1 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-400 transition cursor-pointer">
                        <X class="size-5" />
                    </button>
                </div>

                <form @submit.prevent="submitLogbookForm" class="p-5 space-y-4 max-h-[80vh] overflow-y-auto">
                    <!-- Title Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Judul Kegiatan / Laporan</label>
                        <input
                            v-model="logbookForm.title"
                            type="text"
                            placeholder="Contoh: Sosialisasi PHBS di Sekolah Dasar"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-emerald-550 focus:outline-none dark:bg-slate-950 dark:text-white"
                            required
                        />
                        <p v-if="logbookForm.errors.title" class="text-xs text-red-500">{{ logbookForm.errors.title }}</p>
                    </div>

                    <!-- Date Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Tanggal Kegiatan</label>
                        <input
                            v-model="logbookForm.date"
                            type="date"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-emerald-550 focus:outline-none dark:bg-slate-950 dark:text-white"
                            required
                        />
                        <p v-if="logbookForm.errors.date" class="text-xs text-red-500">{{ logbookForm.errors.date }}</p>
                    </div>

                    <!-- Activity Type Select -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Jenis Kegiatan</label>
                        <select
                            v-model="logbookForm.activity_type"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-emerald-550 focus:outline-none bg-white dark:bg-slate-950 dark:text-white"
                            required
                        >
                            <option value="internal">Internal Posko (Koordinasi, Rapat, Piket)</option>
                            <option value="community">Bakti Masyarakat (Program Kerja, Baksos)</option>
                        </select>
                        <p v-if="logbookForm.errors.activity_type" class="text-xs text-red-500">{{ logbookForm.errors.activity_type }}</p>
                    </div>

                    <!-- Scope Select -->
                    <div v-if="canWriteGroupLogbook" class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Lingkup Laporan</label>
                        <select
                            v-model="logbookForm.scope"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-emerald-550 focus:outline-none bg-white dark:bg-slate-950 dark:text-white"
                            required
                        >
                            <option value="personal">Logbook Pribadi (Laporan Individu)</option>
                            <option value="group">Logbook Kelompok (Kegiatan Bersama)</option>
                        </select>
                        <p v-if="logbookForm.errors.scope" class="text-xs text-red-500">{{ logbookForm.errors.scope }}</p>
                    </div>

                    <!-- Description Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Rincian Laporan Kegiatan</label>
                        <textarea
                            v-model="logbookForm.description"
                            rows="4"
                            placeholder="Uraikan detail jalannya kegiatan, siapa saja yang berpartisipasi, serta hasil/dampak kegiatan..."
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-emerald-550 focus:outline-none dark:bg-slate-950 dark:text-white"
                            required
                        ></textarea>
                        <p v-if="logbookForm.errors.description" class="text-xs text-red-500">{{ logbookForm.errors.description }}</p>
                    </div>

                    <!-- Image File Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Foto Dokumentasi (Opsional)</label>
                        <div class="flex items-center gap-3">
                            <input
                                ref="fileInput"
                                type="file"
                                @change="handleImageChange"
                                accept="image/*"
                                class="hidden"
                            />
                            <Button 
                                type="button" 
                                variant="outline" 
                                @click="fileInput?.click()"
                                class="rounded-xl flex items-center gap-1.5 cursor-pointer text-xs border border-slate-200 dark:border-slate-800"
                            >
                                <ImageIcon class="size-4 text-emerald-600" />
                                Pilih Foto
                            </Button>
                            <span v-if="logbookForm.image" class="text-xs text-slate-500 truncate max-w-[200px]">
                                {{ logbookForm.image.name }}
                            </span>
                        </div>
                        <div v-if="imagePreview" class="mt-2 relative w-full h-32 bg-slate-50 dark:bg-slate-950 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800">
                            <img :src="imagePreview" alt="Preview" class="w-full h-full object-contain" />
                            <button 
                                type="button" 
                                @click="() => { imagePreview = null; logbookForm.image = null; if (fileInput) fileInput.value = ''; }"
                                class="absolute top-1.5 right-1.5 p-1 bg-red-500 hover:bg-red-600 text-white rounded-full transition"
                            >
                                <X class="size-3.5" />
                            </button>
                        </div>
                        <p v-if="logbookForm.errors.image" class="text-xs text-red-500">{{ logbookForm.errors.image }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-850 flex justify-end gap-2">
                        <Button type="button" variant="outline" @click="closeLogbookModal" class="rounded-xl px-4 cursor-pointer">
                            Batal
                        </Button>
                        <Button
                            type="submit"
                            :disabled="logbookForm.processing"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-2 rounded-xl flex items-center gap-2 cursor-pointer"
                        >
                            <Spinner v-if="logbookForm.processing" />
                            {{ isLogbookEditMode ? 'Simpan Laporan' : 'Tambah Laporan' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Proker Finance details modal -->
        <div v-if="isProkerFinanceModalOpen && selectedProkerForFinance" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
            <div class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <!-- Header -->
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-950">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <Wallet class="size-5 text-sky-500" />
                            Aliran Kas & Keuangan Program Kerja
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">{{ selectedProkerForFinance.name }}</p>
                    </div>
                    <button @click="closeProkerFinanceModal" class="p-1 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-400 transition cursor-pointer">
                        <X class="size-5" />
                    </button>
                </div>

                <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto font-sans">
                    <!-- Metrics Summary -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="p-3 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-xl text-center">
                            <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Estimasi Anggaran</p>
                            <p class="text-xs sm:text-sm font-extrabold text-slate-800 dark:text-slate-200 mt-1">
                                {{ formatRupiah(selectedProkerForFinance.budget) }}
                            </p>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-xl text-center">
                            <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Pemasukan Proker</p>
                            <p class="text-xs sm:text-sm font-extrabold text-emerald-600 dark:text-emerald-450 mt-1">
                                {{ formatRupiah(selectedProkerForFinance.earned || 0) }}
                            </p>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-xl text-center">
                            <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Total Pengeluaran</p>
                            <p class="text-xs sm:text-sm font-extrabold text-red-500 mt-1">
                                {{ formatRupiah(selectedProkerForFinance.spent) }}
                            </p>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-xl text-center">
                            <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Dana Tersedia</p>
                            <p 
                                class="text-xs sm:text-sm font-extrabold mt-1 text-emerald-500"
                            >
                                {{ formatRupiah((selectedProkerForFinance.spent || 0) + (selectedProkerForFinance.earned || 0)) }}
                            </p>
                        </div>
                    </div>

                    <!-- Transaction List -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rincian Transaksi Proker</h4>
                        
                        <div v-if="selectedProkerForFinance.finances && selectedProkerForFinance.finances.length > 0" class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-250 dark:border-slate-800 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        <th class="py-2.5 px-3">Tanggal</th>
                                        <th class="py-2.5 px-3">Transaksi</th>
                                        <th class="py-2.5 px-3 text-center">Tipe</th>
                                        <th class="py-2.5 px-3 text-right">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                                    <tr 
                                        v-for="item in selectedProkerForFinance.finances" 
                                        :key="item.id"
                                        class="hover:bg-slate-50/50 dark:hover:bg-slate-850/20"
                                    >
                                        <td class="py-2.5 px-3 whitespace-nowrap text-slate-500">
                                            {{ new Date(item.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                                        </td>
                                        <td class="py-2.5 px-3 font-semibold">{{ item.title }}</td>
                                        <td class="py-2.5 px-3 text-center">
                                            <span 
                                                class="px-1.5 py-0.5 rounded-full text-[9px] font-bold"
                                                :class="[(item.type === 'allocation' && item.category === 'Kas ke Proker') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-400' : (item.type === 'allocation' && item.category === 'Proker ke Kas') ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/20 dark:text-amber-400' : 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-400']"
                                            >
                                                {{ (item.type === 'allocation' && item.category === 'Kas ke Proker') ? 'Dana Masuk' : (item.type === 'allocation' && item.category === 'Proker ke Kas') ? 'Dana Keluar' : 'Belanja' }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-bold" :class="[(item.type === 'allocation' && item.category === 'Kas ke Proker') ? 'text-emerald-500' : 'text-slate-900 dark:text-white']">
                                            {{ (item.type === 'allocation' && item.category === 'Kas ke Proker') ? '+' : '-' }} {{ formatRupiah(item.amount) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-8 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl text-slate-400 text-xs italic">
                            Belum ada transaksi kas yang dicatat untuk Program Kerja ini.
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <Button type="button" @click="closeProkerFinanceModal" class="rounded-xl px-4 cursor-pointer text-xs">
                        Tutup
                    </Button>
                </div>
            </div>
        </div>

    </div>
</template>
