<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Phone, Mail, Plus, Search, MessageSquare, Edit3, Trash2, 
    Building, Shield, BookOpen, HeartPulse, Sparkles, X, Check, Eye
} from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { useToast } from '@/composables/useToast';
import { useConfirm } from '@/composables/useConfirm';

const { confirm } = useConfirm();

interface Contact {
    id: number;
    name: string;
    role_title: string;
    category: 'aparat_desa' | 'kesehatan' | 'keamanan' | 'akademik' | 'mitra';
    phone: string | null;
    email: string | null;
    notes: string | null;
}

const props = defineProps<{
    contacts: Contact[];
    filters: {
        search?: string;
        category?: string;
    };
    canWrite: boolean;
}>();

const toast = useToast();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Buku Kontak',
                href: '/contacts',
            },
        ],
    },
});

const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');

// Local filtering and searching (for instant performance alongside backend requests)
const filteredContacts = computed(() => {
    return props.contacts.filter(contact => {
        const matchesSearch = 
            contact.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            contact.role_title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (contact.notes && contact.notes.toLowerCase().includes(searchQuery.value.toLowerCase()));
        
        const matchesCategory = selectedCategory.value === '' || contact.category === selectedCategory.value;
        
        return matchesSearch && matchesCategory;
    });
});

// Modal state
const isModalOpen = ref(false);
const isEditMode = ref(false);
const editingContactId = ref<number | null>(null);

const form = useForm({
    name: '',
    role_title: '',
    category: 'aparat_desa',
    phone: '',
    email: '',
    notes: '',
});

const openCreateModal = () => {
    isEditMode.value = false;
    editingContactId.value = null;
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (contact: Contact) => {
    isEditMode.value = true;
    editingContactId.value = contact.id;
    form.name = contact.name;
    form.role_title = contact.role_title;
    form.category = contact.category;
    form.phone = contact.phone || '';
    form.email = contact.email || '';
    form.notes = contact.notes || '';
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
};

const submitForm = () => {
    if (isEditMode.value && editingContactId.value) {
        form.put(`/contacts/${editingContactId.value}`, {
            onSuccess: () => {
                toast.success('Kontak berhasil diperbarui.');
                closeModal();
            },
            onError: () => {
                toast.error('Gagal memperbarui kontak. Periksa kembali inputan Anda.');
            }
        });
    } else {
        form.post('/contacts', {
            onSuccess: () => {
                toast.success('Kontak baru berhasil ditambahkan.');
                closeModal();
            },
            onError: () => {
                toast.error('Gagal menambahkan kontak. Periksa kembali inputan Anda.');
            }
        });
    }
};

const confirmDelete = async (contact: Contact) => {
    const isConfirmed = await confirm({
        title: 'Hapus Kontak?',
        message: `Apakah Anda yakin ingin menghapus kontak <strong>${contact.name}</strong> dari buku kontak posko? Tindakan ini tidak dapat dibatalkan.`,
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        variant: 'destructive',
    });

    if (isConfirmed) {
        router.delete(`/contacts/${contact.id}`, {
            onSuccess: () => {
                toast.success('Kontak berhasil dihapus.');
            },
            onError: () => {
                toast.error('Gagal menghapus kontak.');
            }
        });
    }
};

const getCategoryDetails = (cat: string) => {
    switch (cat) {
        case 'aparat_desa':
            return {
                label: 'Aparat Desa',
                icon: Building,
                colorClass: 'bg-purple-50 text-purple-700 border-purple-200',
                pillColor: 'bg-purple-500'
            };
        case 'kesehatan':
            return {
                label: 'Kesehatan',
                icon: HeartPulse,
                colorClass: 'bg-emerald-50 text-emerald-700 border-emerald-200',
                pillColor: 'bg-emerald-500'
            };
        case 'keamanan':
            return {
                label: 'Keamanan',
                icon: Shield,
                colorClass: 'bg-amber-50 text-amber-700 border-amber-200',
                pillColor: 'bg-amber-500'
            };
        case 'akademik':
            return {
                label: 'Akademik',
                icon: BookOpen,
                colorClass: 'bg-sky-50 text-sky-700 border-sky-200',
                pillColor: 'bg-sky-500'
            };
        case 'mitra':
            return {
                label: 'Mitra Kerja',
                icon: Sparkles,
                colorClass: 'bg-rose-50 text-rose-700 border-rose-200',
                pillColor: 'bg-rose-500'
            };
        default:
            return {
                label: 'Lainnya',
                icon: Sparkles,
                colorClass: 'bg-slate-50 text-slate-700 border-slate-200',
                pillColor: 'bg-slate-500'
            };
    }
};

const getWhatsAppLink = (phone: string, name: string) => {
    let cleanPhone = phone.replace(/[^0-9]/g, '');
    if (cleanPhone.startsWith('0')) {
        cleanPhone = '62' + cleanPhone.substring(1);
    }
    const message = encodeURIComponent(`Halo, saya dari Posko KKN. Ingin berkoordinasi terkait program kerja posko.`);
    return `https://wa.me/${cleanPhone}?text=${message}`;
};

const getPhoneCallLink = (phone: string) => {
    let cleanPhone = phone.replace(/[^0-9]/g, '');
    if (cleanPhone.startsWith('0')) {
        cleanPhone = '+62' + cleanPhone.substring(1);
    } else if (cleanPhone.startsWith('62')) {
        cleanPhone = '+' + cleanPhone;
    }
    return `tel:${cleanPhone}`;
};
</script>

<template>
    <Head title="Buku Kontak - Posko KKN" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-7xl mx-auto font-sans">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Buku Kontak Desa & Mitra</h1>
                <p class="text-sm text-slate-500">Daftar nomor kontak darurat, aparatur desa, pimpinan dusun, tenaga kesehatan, dan mitra pendukung program kerja.</p>
            </div>
            
            <Button 
                v-if="canWrite"
                @click="openCreateModal" 
                class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-4 py-2.5 rounded-xl transition duration-200 flex items-center gap-2 cursor-pointer shadow-xs"
            >
                <Plus class="size-4" /> Tambah Kontak
            </Button>
        </div>

        <!-- Filter and Search controls -->
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between rounded-xl border border-slate-200 bg-white p-4 shadow-xs">
            <!-- Search field -->
            <div class="relative w-full md:w-96">
                <Search class="absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari nama, jabatan, atau catatan..."
                    class="w-full rounded-xl border border-slate-200 pl-10 pr-4 py-2 text-sm focus:border-sky-500 focus:outline-none"
                />
            </div>

            <!-- Category Filter Pills -->
            <div class="flex flex-wrap gap-2 w-full md:w-auto overflow-x-auto py-1">
                <button
                    @click="selectedCategory = ''"
                    :class="[
                        'px-3.5 py-1.5 text-xs font-semibold rounded-full border transition duration-200 cursor-pointer',
                        selectedCategory === '' 
                            ? 'bg-sky-500 text-white border-sky-500' 
                            : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100'
                    ]"
                >
                    Semua
                </button>
                <button
                    v-for="cat in ['aparat_desa', 'kesehatan', 'keamanan', 'akademik', 'mitra']"
                    :key="cat"
                    @click="selectedCategory = cat"
                    :class="[
                        'px-3.5 py-1.5 text-xs font-semibold rounded-full border transition duration-200 cursor-pointer',
                        selectedCategory === cat 
                            ? 'bg-sky-500 text-white border-sky-500' 
                            : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100'
                    ]"
                >
                    {{ getCategoryDetails(cat).label }}
                </button>
            </div>
        </div>

        <!-- Contact Grid -->
        <div v-if="filteredContacts.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
                v-for="contact in filteredContacts" 
                :key="contact.id"
                class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-xs hover:shadow-md hover:border-slate-300 transition-all duration-300 flex flex-col justify-between"
            >
                <div>
                    <!-- Header with Category -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span :class="['px-2.5 py-0.5 rounded-full border text-[10px] font-semibold flex items-center gap-1', getCategoryDetails(contact.category).colorClass]">
                            <component :is="getCategoryDetails(contact.category).icon" class="size-3" />
                            {{ getCategoryDetails(contact.category).label }}
                        </span>
                        
                        <div v-if="canWrite" class="flex gap-1">
                            <button 
                                @click="openEditModal(contact)" 
                                class="p-1 text-slate-400 hover:text-sky-500 rounded-lg hover:bg-slate-50 transition cursor-pointer"
                                title="Edit Kontak"
                            >
                                <Edit3 class="size-4" />
                            </button>
                            <button 
                                @click="confirmDelete(contact)" 
                                class="p-1 text-slate-400 hover:text-red-500 rounded-lg hover:bg-slate-50 transition cursor-pointer"
                                title="Hapus Kontak"
                            >
                                <Trash2 class="size-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Name and Position -->
                    <h3 class="font-bold text-slate-900 text-base mb-0.5 leading-snug">{{ contact.name }}</h3>
                    <p class="text-xs font-semibold text-slate-500 mb-4">{{ contact.role_title }}</p>

                    <!-- Contact Details -->
                    <div class="space-y-2 text-xs text-slate-600 mb-4">
                        <div v-if="contact.phone" class="flex items-center gap-2">
                            <Phone class="size-3.5 text-slate-400 shrink-0" />
                            <a :href="getPhoneCallLink(contact.phone)" class="hover:text-sky-600 font-mono transition-colors">
                                +{{ contact.phone }}
                            </a>
                        </div>
                        <div v-if="contact.email" class="flex items-center gap-2">
                            <Mail class="size-3.5 text-slate-400 shrink-0" />
                            <a :href="'mailto:' + contact.email" class="hover:text-sky-600 truncate transition-colors" :title="contact.email">
                                {{ contact.email }}
                            </a>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div v-if="contact.notes" class="p-3 bg-slate-50 border border-slate-100 rounded-xl text-xs text-slate-500 italic mb-4">
                        {{ contact.notes }}
                    </div>
                </div>

                <!-- Call Actions -->
                <div class="flex gap-2 pt-3 border-t border-slate-100">
                    <a 
                        v-if="contact.phone" 
                        :href="getWhatsAppLink(contact.phone, contact.name)"
                        target="_blank"
                        class="flex-1 flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-bold text-emerald-600 bg-emerald-50 hover:bg-emerald-100/70 border border-emerald-100 rounded-xl transition duration-200"
                    >
                        <MessageSquare class="size-3.5" /> WhatsApp
                    </a>
                    <a 
                        v-if="contact.phone" 
                        :href="getPhoneCallLink(contact.phone)"
                        class="flex-1 flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-bold text-sky-600 bg-sky-50 hover:bg-sky-100/70 border border-sky-100 rounded-xl transition duration-200"
                    >
                        <Phone class="size-3.5" /> Telepon
                    </a>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-else class="flex flex-col items-center justify-center py-16 px-4 border border-dashed border-slate-200 rounded-2xl bg-white text-center">
            <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 mb-4">
                <Search class="size-6" />
            </div>
            <h3 class="font-bold text-slate-800 text-base mb-1">Kontak Tidak Ditemukan</h3>
            <p class="text-sm text-slate-500 max-w-sm">Tidak ada kontak yang cocok dengan filter atau kata kunci pencarian Anda.</p>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
            <div class="w-full max-w-md bg-white rounded-2xl border border-slate-100 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="p-5 border-b flex justify-between items-center bg-slate-50">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <Plus v-if="!isEditMode" class="size-5 text-sky-500" />
                        <Edit3 v-else class="size-5 text-sky-500" />
                        {{ isEditMode ? 'Edit Kontak Desa / Mitra' : 'Tambah Kontak Baru' }}
                    </h3>
                    <button @click="closeModal" class="p-1 rounded-lg hover:bg-slate-200 text-slate-400 transition cursor-pointer">
                        <X class="size-5" />
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="p-5 space-y-4">
                    <!-- Name Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Nama Lengkap</label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Contoh: Budi Santoso"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none"
                            required
                        />
                        <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <!-- Role Title Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Jabatan / Peran</label>
                        <input
                            v-model="form.role_title"
                            type="text"
                            placeholder="Contoh: Kepala Desa / DPL KKN"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none"
                            required
                        />
                        <p v-if="form.errors.role_title" class="text-xs text-red-500">{{ form.errors.role_title }}</p>
                    </div>

                    <!-- Category Select -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Kategori Kontak</label>
                        <select
                            v-model="form.category"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none bg-white"
                            required
                        >
                            <option value="aparat_desa">Aparat Desa (Kades/Lurah/RT/RW)</option>
                            <option value="kesehatan">Kesehatan (Puskesmas/Bidan/Kader)</option>
                            <option value="keamanan">Keamanan & Ketertiban (Bhabinkamtibmas/Babinsa)</option>
                            <option value="akademik">Akademik KKN (DPL/Korlap)</option>
                            <option value="mitra">Mitra & Lembaga Eksternal</option>
                        </select>
                        <p v-if="form.errors.category" class="text-xs text-red-500">{{ form.errors.category }}</p>
                    </div>

                    <!-- Phone Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Nomor Telepon / WhatsApp</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3 text-xs font-semibold text-slate-400">+62</span>
                            <input
                                v-model="form.phone"
                                type="text"
                                placeholder="8123456789"
                                class="w-full rounded-xl border border-slate-200 pl-11 pr-3 py-2 text-sm focus:border-sky-500 focus:outline-none"
                            />
                        </div>
                        <p v-if="form.errors.phone" class="text-xs text-red-500">{{ form.errors.phone }}</p>
                    </div>

                    <!-- Email Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Alamat Email (Opsional)</label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="nama@domain.com"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none"
                        />
                        <p v-if="form.errors.email" class="text-xs text-red-500">{{ form.errors.email }}</p>
                    </div>

                    <!-- Notes Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Catatan Tambahan (Jam kerja, dll)</label>
                        <textarea
                            v-model="form.notes"
                            rows="2"
                            placeholder="Masukkan catatan pendukung (misal: Hanya menerima WA pada hari kerja)"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none"
                        ></textarea>
                        <p v-if="form.errors.notes" class="text-xs text-red-500">{{ form.errors.notes }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 border-t flex justify-end gap-2">
                        <Button type="button" variant="outline" @click="closeModal" class="rounded-xl px-4 cursor-pointer">
                            Batal
                        </Button>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-5 py-2 rounded-xl flex items-center gap-2 cursor-pointer"
                        >
                            <Spinner v-if="form.processing" />
                            {{ isEditMode ? 'Simpan Perubahan' : 'Tambah Kontak' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>
