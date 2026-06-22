<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Plus, Search, Edit3, Trash2, X, Briefcase, CheckSquare, Square, CheckCircle, AlertTriangle, Info, ShieldAlert
} from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { useToast } from '@/composables/useToast';
import { useConfirm } from '@/composables/useConfirm';

const { confirm } = useConfirm();
const toast = useToast();

interface PersonalBelonging {
    id: number;
    user_id: number;
    name: string;
    quantity: number;
    unit: string;
    is_packed_departure: boolean;
    is_packed_return: boolean;
    notes: string | null;
    created_at: string;
}

const props = defineProps<{
    belongings: PersonalBelonging[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Manajemen',
                href: '#',
            },
            {
                title: 'Barang Pribadi',
                href: '/personal-belongings',
            },
        ],
    },
});

const searchQuery = ref('');
const selectedFilter = ref('all'); // all, pending_departure, pending_return, mismatch

// Filters and search logic
const filteredBelongings = computed(() => {
    return props.belongings.filter(item => {
        const matchesSearch = item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (item.notes && item.notes.toLowerCase().includes(searchQuery.value.toLowerCase()));

        let matchesFilter = true;
        if (selectedFilter.value === 'pending_departure') {
            matchesFilter = !item.is_packed_departure;
        } else if (selectedFilter.value === 'pending_return') {
            matchesFilter = !item.is_packed_return;
        } else if (selectedFilter.value === 'mismatch') {
            // Mismatch: packed on departure, but not packed on return
            matchesFilter = item.is_packed_departure && !item.is_packed_return;
        }

        return matchesSearch && matchesFilter;
    });
});

// Progress calculations
const totalItems = computed(() => props.belongings.length);

const departureProgress = computed(() => {
    if (totalItems.value === 0) return 0;
    const packedCount = props.belongings.filter(item => item.is_packed_departure).length;
    return Math.round((packedCount / totalItems.value) * 100);
});

const returnProgress = computed(() => {
    if (totalItems.value === 0) return 0;
    const packedCount = props.belongings.filter(item => item.is_packed_return).length;
    return Math.round((packedCount / totalItems.value) * 100);
});

const missingReturnCount = computed(() => {
    return props.belongings.filter(item => item.is_packed_departure && !item.is_packed_return).length;
});

// Modal state
const isModalOpen = ref(false);
const isEditMode = ref(false);
const editingBelongingId = ref<number | null>(null);

const form = useForm({
    name: '',
    quantity: 1,
    unit: 'pcs',
    notes: '',
});

const openCreateModal = () => {
    isEditMode.value = false;
    editingBelongingId.value = null;
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (item: PersonalBelonging) => {
    isEditMode.value = true;
    editingBelongingId.value = item.id;
    form.name = item.name;
    form.quantity = item.quantity;
    form.unit = item.unit;
    form.notes = item.notes || '';
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
};

const submitForm = () => {
    if (isEditMode.value && editingBelongingId.value) {
        form.put(`/personal-belongings/${editingBelongingId.value}`, {
            onSuccess: () => {
                toast.success('Barang bawaan berhasil diperbarui.');
                closeModal();
            },
            onError: () => {
                toast.error('Gagal memperbarui barang. Periksa kembali inputan Anda.');
            }
        });
    } else {
        form.post('/personal-belongings', {
            onSuccess: () => {
                toast.success('Barang bawaan berhasil ditambahkan.');
                closeModal();
            },
            onError: () => {
                toast.error('Gagal menambahkan barang. Periksa kembali inputan Anda.');
            }
        });
    }
};

const confirmDelete = async (item: PersonalBelonging) => {
    const isConfirmed = await confirm({
        title: 'Hapus Barang Bawaan?',
        message: `Apakah Anda yakin ingin menghapus <strong>${item.name}</strong> dari daftar barang pribadi?`,
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        variant: 'destructive',
    });

    if (isConfirmed) {
        router.delete(`/personal-belongings/${item.id}`, {
            onSuccess: () => {
                toast.success('Barang bawaan berhasil dihapus.');
            },
            onError: () => {
                toast.error('Gagal menghapus barang bawaan.');
            }
        });
    }
};

const togglePackedStatus = (item: PersonalBelonging, type: 'departure' | 'return') => {
    router.post(`/personal-belongings/${item.id}/toggle-packed`, { type }, {
        preserveScroll: true,
        onSuccess: () => {
            // Instant feedback
        }
    });
};
</script>

<template>
    <Head title="Barang Pribadi - SuperPosko" />

    <div class="flex flex-col gap-6 p-6 w-full max-w-7xl mx-auto font-sans">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <Briefcase class="size-6 text-sky-500 shrink-0" />
                    Daftar Barang Bawaan Pribadi
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1.5 mt-1">
                    <CheckCircle class="size-4 text-emerald-500 shrink-0" />
                    Catat perlengkapan pribadi KKN. Hanya Anda sendiri yang dapat melihat dan mengelola daftar ini.
                </p>
            </div>
            
            <Button 
                @click="openCreateModal" 
                class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-4 py-2.5 rounded-xl transition duration-200 flex items-center gap-2 cursor-pointer shadow-xs"
            >
                <Plus class="size-4" /> Tambah Barang
            </Button>
        </div>

        <!-- Mismatch Warning Banner -->
        <div v-if="missingReturnCount > 0" class="flex items-start gap-3 p-4 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 rounded-2xl">
            <AlertTriangle class="size-5 text-amber-600 dark:text-amber-500 shrink-0 mt-0.5" />
            <div>
                <h4 class="text-sm font-bold text-amber-800 dark:text-amber-400">Ada Barang Belum Dikemas Pulang!</h4>
                <p class="text-xs text-amber-700 dark:text-amber-500/90 mt-0.5">
                    Terdapat <strong>{{ missingReturnCount }} barang</strong> yang Anda bawa saat berangkat KKN, namun belum dicentang sebagai "Sudah Dikemas" untuk perjalanan pulang.
                </p>
            </div>
        </div>

        <!-- Packing Progress Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/50 dark:bg-slate-900/30 p-5 rounded-2xl border border-slate-100 dark:border-slate-800/80">
            <!-- Departure Packing Card -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                        Kemasan Berangkat (Ke Lokasi KKN)
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pastikan barang yang tercatat sudah masuk ke dalam tas sebelum berangkat.</p>
                </div>
                <div class="mt-4">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        <span>Progress Kemas</span>
                        <span>{{ departureProgress }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                        <div class="bg-sky-500 h-2 rounded-full transition-all duration-500" :style="`width: ${departureProgress}%`"></div>
                    </div>
                </div>
            </div>

            <!-- Return Packing Card -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        Kemasan Pulang (Kembali dari KKN)
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Gunakan untuk memeriksa kembali barang agar tidak ada yang tertinggal di lokasi.</p>
                </div>
                <div class="mt-4">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                        <span>Progress Kemas</span>
                        <span>{{ returnProgress }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                        <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" :style="`width: ${returnProgress}%`"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search Controls -->
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-xs">
            <!-- Search field -->
            <div class="relative w-full md:w-96">
                <Search class="absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari nama barang atau catatan..."
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-850 pl-10 pr-4 py-2 text-sm focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                />
            </div>

            <!-- Status Filter Pills -->
            <div class="flex flex-wrap gap-2 w-full md:w-auto overflow-x-auto py-1">
                <button
                    @click="selectedFilter = 'all'"
                    :class="[
                        'px-3.5 py-1.5 text-xs font-semibold rounded-full border transition duration-200 cursor-pointer',
                        selectedFilter === 'all' 
                            ? 'bg-sky-500 text-white border-sky-500' 
                            : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-750'
                    ]"
                >
                    Semua Barang
                </button>
                <button
                    @click="selectedFilter = 'pending_departure'"
                    :class="[
                        'px-3.5 py-1.5 text-xs font-semibold rounded-full border transition duration-200 cursor-pointer',
                        selectedFilter === 'pending_departure' 
                            ? 'bg-sky-500 text-white border-sky-500' 
                            : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-750'
                    ]"
                >
                    Belum Dikemas Berangkat
                </button>
                <button
                    @click="selectedFilter = 'pending_return'"
                    :class="[
                        'px-3.5 py-1.5 text-xs font-semibold rounded-full border transition duration-200 cursor-pointer',
                        selectedFilter === 'pending_return' 
                            ? 'bg-sky-500 text-white border-sky-500' 
                            : 'bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-750'
                    ]"
                >
                    Belum Dikemas Pulang
                </button>
                <button
                    @click="selectedFilter = 'mismatch'"
                    :class="[
                        'px-3.5 py-1.5 text-xs font-semibold rounded-full border transition duration-200 cursor-pointer flex items-center gap-1',
                        selectedFilter === 'mismatch' 
                            ? 'bg-amber-500 text-white border-amber-500' 
                            : 'bg-amber-50/50 dark:bg-amber-950/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-900/50 hover:bg-amber-100/50'
                    ]"
                >
                    <AlertTriangle class="size-3.5 shrink-0" />
                    Warning / Tertinggal
                </button>
            </div>
        </div>

        <!-- Belongings List Table -->
        <div v-if="filteredBelongings.length > 0" class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-250 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                        <th class="p-4 pl-6">Nama Barang</th>
                        <th class="p-4 text-center">Jumlah</th>
                        <th class="p-4 text-center">Kemas Berangkat</th>
                        <th class="p-4 text-center">Kemas Pulang</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-150 dark:divide-slate-850 text-sm">
                    <tr 
                        v-for="item in filteredBelongings" 
                        :key="item.id" 
                        class="hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition duration-150"
                    >
                        <td class="p-4 pl-6">
                            <div class="font-bold text-slate-800 dark:text-white">{{ item.name }}</div>
                            <div class="text-xs text-slate-400 dark:text-slate-500 italic mt-0.5">{{ item.notes || 'Tidak ada catatan.' }}</div>
                        </td>
                        <td class="p-4 text-center font-semibold text-slate-700 dark:text-slate-300">
                            {{ item.quantity }} {{ item.unit }}
                        </td>
                        <td class="p-4 text-center">
                            <button 
                                @click="togglePackedStatus(item, 'departure')"
                                class="inline-flex items-center justify-center p-1 rounded-lg transition hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
                                :title="item.is_packed_departure ? 'Tandai belum dikemas' : 'Tandai sudah dikemas'"
                            >
                                <CheckSquare v-if="item.is_packed_departure" class="size-6 text-sky-500" />
                                <Square v-else class="size-6 text-slate-300 dark:text-slate-600" />
                            </button>
                        </td>
                        <td class="p-4 text-center">
                            <button 
                                @click="togglePackedStatus(item, 'return')"
                                class="inline-flex items-center justify-center p-1 rounded-lg transition hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
                                :title="item.is_packed_return ? 'Tandai belum dikemas' : 'Tandai sudah dikemas'"
                            >
                                <CheckSquare v-if="item.is_packed_return" class="size-6 text-emerald-500" />
                                <Square v-else class="size-6 text-slate-300 dark:text-slate-600" />
                            </button>
                        </td>
                        <td class="p-4 text-center">
                            <!-- Mismatch warning badge -->
                            <span 
                                v-if="item.is_packed_departure && !item.is_packed_return" 
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20 animate-pulse"
                            >
                                Belum Dikemas Pulang
                            </span>
                            <!-- Completed status -->
                            <span 
                                v-else-if="item.is_packed_departure && item.is_packed_return" 
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20"
                            >
                                Aman / Selesai
                            </span>
                            <!-- Draft/Pending -->
                            <span 
                                v-else 
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-50 text-slate-500 border border-slate-200 dark:bg-slate-800/40 dark:text-slate-400 dark:border-slate-850"
                            >
                                Belum Siap
                            </span>
                        </td>
                        <td class="p-4 pr-6 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button 
                                    @click="openEditModal(item)" 
                                    class="p-1.5 text-slate-400 hover:text-sky-500 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                                    title="Edit Barang"
                                >
                                    <Edit3 class="size-4" />
                                </button>
                                <button 
                                    @click="confirmDelete(item)" 
                                    class="p-1.5 text-slate-400 hover:text-red-500 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                                    title="Hapus Barang"
                                >
                                    <Trash2 class="size-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div v-else class="flex flex-col items-center justify-center py-16 px-4 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900 text-center">
            <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 flex items-center justify-center text-slate-400 mb-4">
                <Briefcase class="size-6" />
            </div>
            <h3 class="font-bold text-slate-800 dark:text-slate-200 text-base mb-1">Daftar Barang Masih Kosong</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm">Anda belum mencatat perlengkapan pribadi KKN atau tidak cocok dengan filter pencarian Anda.</p>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
            <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-950">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <Briefcase class="size-5 text-sky-500" />
                        {{ isEditMode ? 'Edit Barang Bawaan' : 'Tambah Barang Baru' }}
                    </h3>
                    <button @click="closeModal" class="p-1 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-400 transition cursor-pointer">
                        <X class="size-5" />
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="p-5 space-y-4">
                    <!-- Name Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Nama Barang</label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Contoh: Jaket KKN, Laptop, Sabun Mandi"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                            required
                        />
                        <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <!-- Quantity & Unit -->
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
                            <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Satuan</label>
                            <input
                                v-model="form.unit"
                                type="text"
                                placeholder="Contoh: pcs, unit, pasang"
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                                required
                            />
                            <p v-if="form.errors.unit" class="text-xs text-red-500">{{ form.errors.unit }}</p>
                        </div>
                    </div>

                    <!-- Notes Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Catatan Tambahan (Opsional)</label>
                        <textarea
                            v-model="form.notes"
                            rows="2"
                            placeholder="Masukkan catatan pendukung (misal: Ditaruh di tas ransel hitam, jangan sampai tertukar)"
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
