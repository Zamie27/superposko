<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { 
    Plus, Search, Edit3, Trash2, X, Box, CheckCircle, AlertTriangle, AlertCircle, Image as ImageIcon, User as UserIcon, Wallet, Users
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

interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
}

interface Inventory {
    id: number;
    name: string;
    quantity: number;
    unit: string;
    condition: 'good' | 'damaged' | 'lost';
    notes: string | null;
    image_path: string | null;
    owner_id: number | null;
    owner?: User | null;
    source: 'member' | 'purchase';
    purchase_price: number | null;
    finance_id: number | null;
    created_at: string;
}

const props = defineProps<{
    inventories: Inventory[];
    members: User[];
    canWrite: boolean;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Manajemen',
                href: '#',
            },
            {
                title: 'Inventaris',
                href: '/management/inventory',
            },
        ],
    },
});

const searchQuery = ref('');
const selectedCondition = ref('');

// Filtered inventories based on search and condition
const filteredInventories = computed(() => {
    return props.inventories.filter(item => {
        const nameMatch = item.name.toLowerCase().includes(searchQuery.value.toLowerCase());
        const notesMatch = item.notes ? item.notes.toLowerCase().includes(searchQuery.value.toLowerCase()) : false;
        const ownerMatch = item.owner ? item.owner.name.toLowerCase().includes(searchQuery.value.toLowerCase()) : false;
        
        const matchesSearch = nameMatch || notesMatch || ownerMatch;
        const matchesCondition = selectedCondition.value === '' || item.condition === selectedCondition.value;
        
        return matchesSearch && matchesCondition;
    });
});

// Modal state
const isModalOpen = ref(false);
const isEditMode = ref(false);
const editingInventoryId = ref<number | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);
const imagePreview = ref<string | null>(null);

const form = useForm({
    _method: 'POST',
    name: '',
    quantity: 1,
    unit: 'pcs',
    condition: 'good' as 'good' | 'damaged' | 'lost',
    notes: '',
    source: 'member' as 'member' | 'purchase',
    owner_id: '' as string | number,
    purchase_price: '' as string | number,
    image: null as File | null,
});

const openCreateModal = () => {
    isEditMode.value = false;
    editingInventoryId.value = null;
    imagePreview.value = null;
    form.reset();
    form._method = 'POST';
    form.source = 'member';
    form.owner_id = '';
    form.purchase_price = '';
    form.unit = 'pcs';
    isModalOpen.value = true;
};

const openEditModal = (item: Inventory) => {
    isEditMode.value = true;
    editingInventoryId.value = item.id;
    imagePreview.value = item.image_path ? '/storage/' + item.image_path : null;
    form.reset();
    form._method = 'POST'; // Spoofing PUT using POST + _method
    form.name = item.name;
    form.quantity = item.quantity;
    form.unit = item.unit || 'pcs';
    form.condition = item.condition;
    form.notes = item.notes || '';
    form.source = item.source ?? 'member';
    form.owner_id = item.owner_id || '';
    form.purchase_price = item.purchase_price ?? '';
    form.image = null;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    imagePreview.value = null;

    if (fileInput.value) {
        fileInput.value.value = '';
    }

    form.reset();
};

const handleImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement;

    if (target.files && target.files[0]) {
        const file = target.files[0];
        form.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submitForm = () => {
    if (isEditMode.value && editingInventoryId.value) {
        form._method = 'PUT';
        // Use POST with spoofing because files do not work natively inside PUT requests in Laravel
        form.post(`/management/inventory/${editingInventoryId.value}`, {
            onSuccess: () => {
                toast.success('Barang inventaris berhasil diperbarui.');
                closeModal();
            },
            onError: () => {
                toast.error('Gagal memperbarui barang. Periksa kembali inputan Anda.');
            }
        });
    } else {
        form._method = 'POST';
        form.post('/management/inventory', {
            onSuccess: () => {
                toast.success('Barang inventaris berhasil ditambahkan.');
                closeModal();
            },
            onError: () => {
                toast.error('Gagal menambahkan barang. Periksa kembali inputan Anda.');
            }
        });
    }
};

const confirmDelete = async (item: Inventory) => {
    const isConfirmed = await confirm({
        title: 'Hapus Barang Inventaris?',
        message: `Apakah Anda yakin ingin menghapus barang <strong>${item.name}</strong> dari inventaris posko? Tindakan ini tidak dapat dibatalkan.`,
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        variant: 'destructive',
    });

    if (isConfirmed) {
        router.delete(`/management/inventory/${item.id}`, {
            onSuccess: () => {
                toast.success('Barang inventaris berhasil dihapus.');
            },
            onError: () => {
                toast.error('Gagal menghapus barang inventaris.');
            }
        });
    }
};

const getConditionDetails = (condition: string) => {
    switch (condition) {
        case 'good':
            return {
                label: 'Bagus',
                icon: CheckCircle,
                badgeClass: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
            };
        case 'damaged':
            return {
                label: 'Rusak',
                icon: AlertTriangle,
                badgeClass: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
            };
        case 'lost':
            return {
                label: 'Hilang',
                icon: AlertCircle,
                badgeClass: 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20',
            };
        default:
            return {
                label: 'Unknown',
                icon: Box,
                badgeClass: 'bg-slate-50 text-slate-700 border-slate-200',
            };
    }
};
</script>

<template>
    <Head title="Manajemen Inventaris - SuperPosko" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-7xl mx-auto font-sans">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Manajemen Inventaris Posko</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Catat perlengkapan posko KKN, barang pinjaman warga, serta kelola kondisi aset secara berkala.</p>
            </div>
            
            <Button 
                v-if="canWrite"
                @click="openCreateModal" 
                class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-4 py-2.5 rounded-xl transition duration-200 flex items-center gap-2 cursor-pointer shadow-xs"
            >
                <Plus class="size-4" /> Tambah Barang
            </Button>
        </div>

        <!-- Filter and Search controls -->
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
            <!-- Search field -->
            <div class="relative w-full md:w-96">
                <Search class="absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari nama barang, catatan, atau pemilik..."
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-850 pl-10 pr-4 py-2 text-sm focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                />
            </div>

            <!-- Condition Filter Pills -->
            <div class="flex flex-wrap gap-2 w-full md:w-auto overflow-x-auto py-1">
                <button
                    @click="selectedCondition = ''"
                    :class="[
                        'px-3.5 py-1.5 text-xs font-semibold rounded-full border transition duration-200 cursor-pointer',
                        selectedCondition === '' 
                            ? 'bg-sky-500 text-white border-sky-500' 
                            : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-750'
                    ]"
                >
                    Semua
                </button>
                <button
                    v-for="cond in ['good', 'damaged', 'lost']"
                    :key="cond"
                    @click="selectedCondition = cond"
                    :class="[
                        'px-3.5 py-1.5 text-xs font-semibold rounded-full border transition duration-200 cursor-pointer',
                        selectedCondition === cond 
                            ? 'bg-sky-500 text-white border-sky-500' 
                            : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-750'
                    ]"
                >
                    {{ getConditionDetails(cond).label }}
                </button>
            </div>
        </div>

        <!-- Inventory List / Cards -->
        <div v-if="filteredInventories.length > 0" class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6">
            <div 
                v-for="item in filteredInventories" 
                :key="item.id"
                class="group rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-300 flex flex-col justify-between overflow-hidden"
            >
                <div>
                    <!-- Card Image Header -->
                    <div v-if="item.image_path" class="relative w-full h-32 sm:h-48 bg-slate-100 dark:bg-slate-950 overflow-hidden border-b border-slate-100 dark:border-slate-850">
                        <img :src="'/storage/' + item.image_path" alt="Foto Barang" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" />
                    </div>
                    <div v-else class="relative w-full h-16 sm:h-24 bg-gradient-to-br from-sky-50 to-indigo-50 dark:from-slate-850 dark:to-slate-950 flex items-center justify-center border-b border-slate-100 dark:border-slate-850">
                        <Box class="size-6 sm:size-8 text-sky-400/70" />
                    </div>

                    <!-- Details Area -->
                    <div class="p-3 sm:p-5">
                        <!-- Header with Condition Status -->
                        <div class="flex items-center justify-between gap-1 mb-2.5">
                            <span :class="['px-2 py-0.5 rounded-full border text-[8px] sm:text-[10px] font-semibold flex items-center gap-0.5 sm:gap-1', getConditionDetails(item.condition).badgeClass]">
                                <component :is="getConditionDetails(item.condition).icon" class="size-2.5 sm:size-3" />
                                {{ getConditionDetails(item.condition).label }}
                            </span>
                            
                            <div v-if="canWrite" class="flex gap-0.5">
                                <button 
                                    @click="openEditModal(item)" 
                                    class="p-1 text-slate-400 hover:text-sky-500 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer"
                                    title="Edit Barang"
                                >
                                    <Edit3 class="size-3.5 sm:size-4" />
                                </button>
                                <button 
                                    @click="confirmDelete(item)" 
                                    class="p-1 text-slate-400 hover:text-red-500 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer"
                                    title="Hapus Barang"
                                >
                                    <Trash2 class="size-3.5 sm:size-4" />
                                </button>
                            </div>
                        </div>

                        <!-- Name and Quantity -->
                        <h3 class="font-bold text-slate-900 dark:text-white text-xs sm:text-base mb-0.5 leading-snug truncate" :title="item.name">{{ item.name }}</h3>
                        <p class="text-[10px] sm:text-sm font-semibold text-slate-500 dark:text-slate-400 mb-2 sm:mb-4">Jumlah: {{ item.quantity }} {{ item.unit || 'pcs' }}</p>

                        <!-- Notes -->
                        <div class="p-2 sm:p-3 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-xl text-[10px] sm:text-xs text-slate-500 dark:text-slate-400 italic line-clamp-2">
                            {{ item.notes || 'Tidak ada catatan.' }}
                        </div>
                    </div>
                </div>

                <!-- Card Footer - Source / Owner Details -->
                <div class="px-3 sm:px-5 py-2 sm:py-3 border-t border-slate-100 dark:border-slate-850 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <!-- Purchase from Kas badge -->
                    <template v-if="item.source === 'purchase'">
                        <span class="text-[8px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400">Sumber</span>
                        <div class="flex items-center gap-1.5">
                            <Wallet class="size-3.5 text-emerald-500 shrink-0" />
                            <span class="text-[10px] sm:text-xs font-semibold text-emerald-700 dark:text-emerald-400 truncate">
                                Beli Kas{{ item.purchase_price ? ' · Rp ' + Number(item.purchase_price).toLocaleString('id-ID') : '' }}
                            </span>
                        </div>
                    </template>
                    <!-- Member owner -->
                    <template v-else>
                        <span class="text-[8px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400">Pemilik</span>
                        <div class="flex items-center gap-1.5 overflow-hidden">
                            <Avatar v-if="item.owner" class="size-5 sm:size-6 overflow-hidden rounded-full shrink-0">
                                <AvatarImage v-if="item.owner.avatar" :src="item.owner.avatar" :alt="item.owner.name" />
                                <AvatarFallback class="text-[8px] sm:text-[9px] bg-sky-100 text-sky-800 font-bold dark:bg-sky-950 dark:text-sky-300">
                                    {{ getInitials(item.owner.name) }}
                                </AvatarFallback>
                            </Avatar>
                            <Avatar v-else class="size-5 sm:size-6 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center shrink-0">
                                <UserIcon class="size-3 text-slate-400" />
                            </Avatar>
                            <span class="text-[10px] sm:text-xs font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[60px] sm:max-w-none">
                                {{ item.owner ? item.owner.name : 'Posko' }}
                            </span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-else class="flex flex-col items-center justify-center py-16 px-4 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900 text-center">
            <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 flex items-center justify-center text-slate-400 mb-4">
                <Box class="size-6" />
            </div>
            <h3 class="font-bold text-slate-800 dark:text-slate-200 text-base mb-1">Inventaris Kosong / Tidak Ditemukan</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm">Belum ada barang inventaris posko yang dicatat, atau tidak cocok dengan filter pencarian Anda.</p>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
            <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-950">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <Box class="size-5 text-sky-500" />
                        {{ isEditMode ? 'Edit Barang Inventaris' : 'Tambah Barang Baru' }}
                    </h3>
                    <button @click="closeModal" class="p-1 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-400 transition cursor-pointer">
                        <X class="size-5" />
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="p-5 space-y-4 max-h-[80vh] overflow-y-auto">
                    <!-- Name Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Barang</label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Contoh: Tikar Posko, Dispenser, Wajan"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                            required
                        />
                        <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <!-- Quantity & Unit Row -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Jumlah</label>
                            <input
                                v-model.number="form.quantity"
                                type="number"
                                min="1"
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                                required
                            />
                            <p v-if="form.errors.quantity" class="text-xs text-red-500">{{ form.errors.quantity }}</p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Satuan Unit</label>
                            <input
                                v-model="form.unit"
                                type="text"
                                placeholder="Contoh: pcs, unit, set"
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                                required
                            />
                            <p v-if="form.errors.unit" class="text-xs text-red-500">{{ form.errors.unit }}</p>
                        </div>
                    </div>

                    <!-- Source Toggle: Sumber Barang -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Sumber Barang</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button
                                type="button"
                                @click="form.source = 'member'; form.purchase_price = ''"
                                :class="[
                                    'flex items-center gap-2 p-3 rounded-xl border-2 text-xs font-semibold transition cursor-pointer',
                                    form.source === 'member'
                                        ? 'border-sky-500 bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400'
                                        : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-600 dark:text-slate-400 hover:border-slate-300'
                                ]"
                            >
                                <Users class="size-4 shrink-0" />
                                Milik Anggota
                            </button>
                            <button
                                type="button"
                                @click="form.source = 'purchase'; form.owner_id = ''"
                                :class="[
                                    'flex items-center gap-2 p-3 rounded-xl border-2 text-xs font-semibold transition cursor-pointer',
                                    form.source === 'purchase'
                                        ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400'
                                        : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-600 dark:text-slate-400 hover:border-slate-300'
                                ]"
                            >
                                <Wallet class="size-4 shrink-0" />
                                Beli dari Kas
                            </button>
                        </div>
                        <p v-if="form.errors.source" class="text-xs text-red-500">{{ form.errors.source }}</p>
                    </div>

                    <!-- Member Selector (only shown when source === 'member') -->
                    <div v-if="form.source === 'member'" class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Pemilik Barang (Anggota)</label>
                        <select
                            v-model="form.owner_id"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none bg-white dark:bg-slate-950 dark:text-white"
                        >
                            <option value="">Milik Posko (Bersama)</option>
                            <option v-for="member in members" :key="member.id" :value="member.id">
                                {{ member.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.owner_id" class="text-xs text-red-500">{{ form.errors.owner_id }}</p>
                    </div>

                    <!-- Purchase Price Input (only shown when source === 'purchase') -->
                    <div v-if="form.source === 'purchase'" class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Harga Satuan (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 font-semibold">Rp</span>
                            <input
                                v-model="form.purchase_price"
                                type="number"
                                min="0"
                                step="1000"
                                placeholder="0"
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-800 pl-9 pr-3 py-2 text-sm focus:border-emerald-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                            />
                        </div>
                        <div v-if="form.purchase_price && form.quantity" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                            Harga Total Pembelian: Rp {{ (Number(form.purchase_price) * Number(form.quantity)).toLocaleString('id-ID') }}
                        </div>
                        <p class="text-[10px] text-slate-400">Otomatis dicatat sebagai pengeluaran kas di E-Bendahara.</p>
                        <p v-if="form.errors.purchase_price" class="text-xs text-red-500">{{ form.errors.purchase_price }}</p>
                    </div>

                    <!-- Condition Select -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Kondisi Barang</label>
                        <select
                            v-model="form.condition"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none bg-white dark:bg-slate-950 dark:text-white"
                            required
                        >
                            <option value="good">Bagus & Layak Pakai</option>
                            <option value="damaged">Rusak / Butuh Perbaikan</option>
                            <option value="lost">Hilang / Tidak Ditemukan</option>
                        </select>
                        <p v-if="form.errors.condition" class="text-xs text-red-500">{{ form.errors.condition }}</p>
                    </div>

                    <!-- Image File Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Foto Barang (Opsional)</label>
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
                                class="rounded-xl flex items-center gap-1.5 cursor-pointer text-xs"
                            >
                                <ImageIcon class="size-4 text-sky-500" />
                                Pilih Gambar
                            </Button>
                            <span v-if="form.image" class="text-xs text-slate-500 truncate max-w-[200px]">
                                {{ form.image.name }}
                            </span>
                        </div>
                        <div v-if="imagePreview" class="mt-2 relative w-full h-32 bg-slate-50 dark:bg-slate-950 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800">
                            <img :src="imagePreview" alt="Preview" class="w-full h-full object-contain" />
                            <button 
                                type="button" 
                                @click="() => { imagePreview = null; form.image = null; if (fileInput) fileInput.value = ''; }"
                                class="absolute top-1.5 right-1.5 p-1 bg-red-500 hover:bg-red-600 text-white rounded-full transition"
                            >
                                <X class="size-3.5" />
                            </button>
                        </div>
                        <p v-if="form.errors.image" class="text-xs text-red-500">{{ form.errors.image }}</p>
                    </div>

                    <!-- Notes Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Catatan Keterangan (Opsional)</label>
                        <textarea
                            v-model="form.notes"
                            rows="2"
                            placeholder="Masukkan catatan pendukung (misal: Dipinjam dari RT 02, ditaruh di ruang tengah)"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                        ></textarea>
                        <p v-if="form.errors.notes" class="text-xs text-red-500">{{ form.errors.notes }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-850 flex justify-end gap-2">
                        <Button type="button" variant="outline" @click="closeModal" class="rounded-xl px-4 cursor-pointer">
                            Batal
                        </Button>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-5 py-2 rounded-xl flex items-center gap-2 cursor-pointer"
                        >
                            <Spinner v-if="form.processing" />
                            {{ isEditMode ? 'Simpan Perubahan' : 'Tambah Barang' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>
