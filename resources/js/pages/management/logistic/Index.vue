<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Plus, Search, Edit3, Trash2, X, ClipboardList, CheckCircle, AlertTriangle, AlertCircle, ArrowUpFromLine, Minus
} from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { useToast } from '@/composables/useToast';
import { useConfirm } from '@/composables/useConfirm';

const { confirm } = useConfirm();
const toast = useToast();

interface Logistic {
    id: number;
    name: string;
    quantity: number;
    unit: string;
    status: 'sufficient' | 'low' | 'out';
    notes: string | null;
    created_at: string;
}

const props = defineProps<{
    logistics: Logistic[];
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
                title: 'Logistik',
                href: '/management/logistic',
            },
        ],
    },
});

const searchQuery = ref('');
const selectedStatus = ref('');

// Filtered logistics based on search and status
const filteredLogistics = computed(() => {
    return props.logistics.filter(item => {
        const matchesSearch = 
            item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (item.notes && item.notes.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
            item.unit.toLowerCase().includes(searchQuery.value.toLowerCase());
        
        const matchesStatus = selectedStatus.value === '' || item.status === selectedStatus.value;
        
        return matchesSearch && matchesStatus;
    });
});

// Modal state
const isModalOpen = ref(false);
const isEditMode = ref(false);
const editingLogisticId = ref<number | null>(null);

const form = useForm({
    name: '',
    quantity: 0,
    unit: 'pcs',
    status: 'sufficient' as 'sufficient' | 'low' | 'out',
    notes: '',
});

const openCreateModal = () => {
    isEditMode.value = false;
    editingLogisticId.value = null;
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (item: Logistic) => {
    isEditMode.value = true;
    editingLogisticId.value = item.id;
    form.name = item.name;
    form.quantity = item.quantity;
    form.unit = item.unit;
    form.status = item.status;
    form.notes = item.notes || '';
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
};

const submitForm = () => {
    if (isEditMode.value && editingLogisticId.value) {
        form.put(`/management/logistic/${editingLogisticId.value}`, {
            onSuccess: () => {
                toast.success('Bahan logistik berhasil diperbarui.');
                closeModal();
            },
            onError: () => {
                toast.error('Gagal memperbarui bahan logistik. Periksa inputan Anda.');
            }
        });
    } else {
        form.post('/management/logistic', {
            onSuccess: () => {
                toast.success('Bahan logistik berhasil ditambahkan.');
                closeModal();
            },
            onError: () => {
                toast.error('Gagal menambahkan bahan logistik. Periksa inputan Anda.');
            }
        });
    }
};

const confirmDelete = async (item: Logistic) => {
    const isConfirmed = await confirm({
        title: 'Hapus Bahan Logistik?',
        message: `Apakah Anda yakin ingin menghapus bahan logistik <strong>${item.name}</strong>? Tindakan ini tidak dapat dibatalkan.`,
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        variant: 'destructive',
    });

    if (isConfirmed) {
        router.delete(`/management/logistic/${item.id}`, {
            onSuccess: () => {
                toast.success('Bahan logistik berhasil dihapus.');
            },
            onError: () => {
                toast.error('Gagal menghapus bahan logistik.');
            }
        });
    }
};

// Barang Keluar modal state
const isBarangKeluarModalOpen = ref(false);
const barangKeluarItems = ref<Array<{ id: number; name: string; maxQuantity: number; unit: string; amount: number }>>([]);

const barangKeluarForm = useForm({
    items: [] as Array<{ id: number; amount: number }>,
});

const openBarangKeluarModal = () => {
    barangKeluarItems.value = props.logistics
        .filter(item => item.quantity > 0)
        .map(item => ({
            id: item.id,
            name: item.name,
            maxQuantity: item.quantity,
            unit: item.unit,
            amount: 0,
        }));
    isBarangKeluarModalOpen.value = true;
};

const closeBarangKeluarModal = () => {
    isBarangKeluarModalOpen.value = false;
    barangKeluarItems.value = [];
    barangKeluarForm.reset();
};

const submitBarangKeluar = () => {
    const itemsKeluar = barangKeluarItems.value
        .filter(item => item.amount > 0)
        .map(item => ({
            id: item.id,
            amount: item.amount,
        }));

    if (itemsKeluar.length === 0) {
        toast.error('Silakan isi jumlah barang keluar minimal untuk satu item.');
        return;
    }

    barangKeluarForm.items = itemsKeluar;
    barangKeluarForm.post('/management/logistic/barang-keluar', {
        onSuccess: () => {
            toast.success('Barang keluar berhasil dicatat.');
            closeBarangKeluarModal();
        },
        onError: (errors) => {
            toast.error(errors.items || 'Gagal mencatat barang keluar.');
        }
    });
};

const getStatusDetails = (status: string) => {
    switch (status) {
        case 'sufficient':
            return {
                label: 'Cukup',
                icon: CheckCircle,
                badgeClass: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
            };
        case 'low':
            return {
                label: 'Menipis',
                icon: AlertTriangle,
                badgeClass: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
            };
        case 'out':
            return {
                label: 'Habis',
                icon: AlertCircle,
                badgeClass: 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20',
            };
        default:
            return {
                label: 'Unknown',
                icon: ClipboardList,
                badgeClass: 'bg-slate-50 text-slate-700 border-slate-200',
            };
    }
};
</script>

<template>
    <Head title="Manajemen Logistik - SuperPosko" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-7xl mx-auto font-sans">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Manajemen Logistik & Konsumsi</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Catat bahan makanan habis pakai, perlengkapan medis posko, sembako, dan ketersediaan logistik harian.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Barang Keluar Button -->
                <Button 
                    v-if="logistics.some(item => item.quantity > 0)"
                    @click="openBarangKeluarModal"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl transition duration-200 flex items-center gap-2 cursor-pointer shadow-xs"
                >
                    <ArrowUpFromLine class="size-4" /> Barang Keluar
                </Button>

                <!-- Add Button -->
                <Button 
                    v-if="canWrite"
                    @click="openCreateModal" 
                    class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-4 py-2.5 rounded-xl transition duration-200 flex items-center gap-2 cursor-pointer shadow-xs"
                >
                    <Plus class="size-4" /> Tambah Bahan
                </Button>
            </div>
        </div>

        <!-- Filter and Search controls -->
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
            <!-- Search field -->
            <div class="relative w-full md:w-96">
                <Search class="absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari logistik atau catatan..."
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-800 pl-10 pr-4 py-2 text-sm focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                />
            </div>

            <!-- Status Filter Pills -->
            <div class="flex flex-wrap gap-2 w-full md:w-auto overflow-x-auto py-1">
                <button
                    @click="selectedStatus = ''"
                    :class="[
                        'px-3.5 py-1.5 text-xs font-semibold rounded-full border transition duration-200 cursor-pointer',
                        selectedStatus === '' 
                            ? 'bg-sky-500 text-white border-sky-500' 
                            : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-750'
                    ]"
                >
                    Semua
                </button>
                <button
                    v-for="st in ['sufficient', 'low', 'out']"
                    :key="st"
                    @click="selectedStatus = st"
                    :class="[
                        'px-3.5 py-1.5 text-xs font-semibold rounded-full border transition duration-200 cursor-pointer',
                        selectedStatus === st 
                            ? 'bg-sky-500 text-white border-sky-500' 
                            : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-750'
                    ]"
                >
                    {{ getStatusDetails(st).label }}
                </button>
            </div>
        </div>

        <!-- Logistics List / Cards -->
        <div v-if="filteredLogistics.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
                v-for="item in filteredLogistics" 
                :key="item.id"
                class="group rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-300 flex flex-col justify-between"
            >
                <div>
                    <!-- Header with Status Badge -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span :class="['px-2.5 py-0.5 rounded-full border text-[10px] font-semibold flex items-center gap-1', getStatusDetails(item.status).badgeClass]">
                            <component :is="getStatusDetails(item.status).icon" class="size-3" />
                            {{ getStatusDetails(item.status).label }}
                        </span>
                        
                        <div v-if="canWrite" class="flex gap-1">
                            <button 
                                @click="openEditModal(item)" 
                                class="p-1 text-slate-400 hover:text-sky-500 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-850 transition cursor-pointer"
                                title="Edit Bahan"
                            >
                                <Edit3 class="size-4" />
                            </button>
                            <button 
                                @click="confirmDelete(item)" 
                                class="p-1 text-slate-400 hover:text-red-500 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-850 transition cursor-pointer"
                                title="Hapus Bahan"
                            >
                                <Trash2 class="size-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Name and Quantity -->
                    <h3 class="font-bold text-slate-900 dark:text-white text-base mb-0.5 leading-snug">{{ item.name }}</h3>
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 mb-4">Stok: {{ Number(item.quantity) }} {{ item.unit }}</p>

                    <!-- Notes -->
                    <div class="p-3 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-xl text-xs text-slate-500 dark:text-slate-400 italic">
                        {{ item.notes || 'Tidak ada catatan tambahan.' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-else class="flex flex-col items-center justify-center py-16 px-4 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900 text-center">
            <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 flex items-center justify-center text-slate-400 mb-4">
                <ClipboardList class="size-6" />
            </div>
            <h3 class="font-bold text-slate-800 dark:text-slate-200 text-base mb-1">Logistik Kosong / Tidak Ditemukan</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm">Belum ada bahan logistik posko yang dicatat, atau tidak cocok dengan filter pencarian Anda.</p>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
            <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-950">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <ClipboardList class="size-5 text-sky-500" />
                        {{ isEditMode ? 'Edit Bahan Logistik' : 'Tambah Bahan Baru' }}
                    </h3>
                    <button @click="closeModal" class="p-1 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-400 transition cursor-pointer">
                        <X class="size-5" />
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="p-5 space-y-4">
                    <!-- Name Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Bahan / Logistik</label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Contoh: Beras, Telur, Air Galon, Parasetamol"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                            required
                        />
                        <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <!-- Quantity & Unit Row -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Jumlah Stok</label>
                            <input
                                v-model.number="form.quantity"
                                type="number"
                                step="any"
                                min="0"
                                placeholder="Contoh: 10, 2.5"
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                                required
                            />
                            <p v-if="form.errors.quantity" class="text-xs text-red-500">{{ form.errors.quantity }}</p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Satuan</label>
                            <input
                                v-model="form.unit"
                                type="text"
                                placeholder="Contoh: kg, Dus, Pcs, Liter"
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                                required
                            />
                            <p v-if="form.errors.unit" class="text-xs text-red-500">{{ form.errors.unit }}</p>
                        </div>
                    </div>

                    <!-- Status Select -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Status Ketersediaan</label>
                        <select
                            v-model="form.status"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none bg-white dark:bg-slate-950 dark:text-white"
                            required
                        >
                            <option value="sufficient">Cukup (Stok Aman)</option>
                            <option value="low">Menipis (Perlu Dibeli Lagi)</option>
                            <option value="out">Habis / Kosong</option>
                        </select>
                        <p v-if="form.errors.status" class="text-xs text-red-500">{{ form.errors.status }}</p>
                    </div>

                    <!-- Notes Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Catatan Keterangan (Opsional)</label>
                        <textarea
                            v-model="form.notes"
                            rows="2"
                            placeholder="Masukkan catatan pendukung (misal: Bantuan dari donatur desa, diletakkan di lemari dapur)"
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
                            {{ isEditMode ? 'Simpan Perubahan' : 'Tambah Bahan' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Barang Keluar Modal -->
        <div v-if="isBarangKeluarModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
            <div class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-950">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <ArrowUpFromLine class="size-5 text-emerald-500" />
                        Catat Barang Keluar
                    </h3>
                    <button @click="closeBarangKeluarModal" class="p-1 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-400 transition cursor-pointer">
                        <X class="size-5" />
                    </button>
                </div>

                <form @submit.prevent="submitBarangKeluar" class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">
                    <p class="text-xs text-slate-500 dark:text-slate-400">Masukkan jumlah logistik yang keluar atau digunakan oleh posko. Stok akan otomatis dikurangi.</p>
                    
                    <div class="space-y-3 divide-y divide-slate-100 dark:divide-slate-850">
                        <div 
                            v-for="item in barangKeluarItems" 
                            :key="item.id" 
                            class="pt-3 first:pt-0 flex items-center justify-between gap-4"
                        >
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ item.name }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Tersedia: {{ item.maxQuantity }} {{ item.unit }}</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <button 
                                    type="button"
                                    @click="item.amount = Math.max(0, item.amount - 1)"
                                    class="p-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition cursor-pointer"
                                >
                                    <Minus class="size-3.5" />
                                </button>
                                
                                <input
                                    v-model.number="item.amount"
                                    type="number"
                                    step="any"
                                    min="0"
                                    :max="item.maxQuantity"
                                    class="w-20 text-center rounded-xl border border-slate-200 dark:border-slate-800 py-1.5 text-xs font-semibold focus:border-emerald-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                                />

                                <button 
                                    type="button"
                                    @click="item.amount = Math.min(item.maxQuantity, item.amount + 1)"
                                    class="p-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition cursor-pointer"
                                >
                                    <Plus class="size-3.5" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-850 flex justify-end gap-2">
                        <Button type="button" variant="outline" @click="closeBarangKeluarModal" class="rounded-xl px-4 cursor-pointer">
                            Batal
                        </Button>
                        <Button
                            type="submit"
                            :disabled="barangKeluarForm.processing"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-2 rounded-xl flex items-center gap-2 cursor-pointer"
                        >
                            <Spinner v-if="barangKeluarForm.processing" />
                            Catat Barang Keluar
                        </Button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>
