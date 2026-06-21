<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    FileText, FileSpreadsheet, FileArchive, FileImage, File, 
    Download, Trash2, Plus, Search, X, Check, Folder, Info, Loader2, Eye
} from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { useToast } from '@/composables/useToast';
import { useConfirm } from '@/composables/useConfirm';

// Preview state
const isPreviewModalOpen = ref(false);
const selectedDocForPreview = ref<any | null>(null);

const openPreviewModal = (doc: any) => {
    selectedDocForPreview.value = doc;
    isPreviewModalOpen.value = true;
};

const closePreviewModal = () => {
    isPreviewModalOpen.value = false;
    selectedDocForPreview.value = null;
};

const isImageFile = (mimeType: string) => {
    return mimeType.toLowerCase().startsWith('image/');
};

const isPdfFile = (mimeType: string) => {
    return mimeType.toLowerCase().includes('pdf');
};

const canPreviewInline = (mimeType: string) => {
    const mime = mimeType.toLowerCase();
    return mime.startsWith('image/') || mime.includes('pdf');
};

interface Document {
    id: number;
    title: string;
    description: string | null;
    category: 'proposal' | 'lpj' | 'perizinan' | 'notulensi' | 'desain' | 'lainnya';
    file_name: string;
    file_size: string;
    mime_type: string;
    uploaded_by: string;
    created_at: string;
}

const props = defineProps<{
    documents: Document[];
    filters: {
        search?: string;
        category?: string;
    };
    canWrite: boolean;
}>();

const { confirm } = useConfirm();
const toast = useToast();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Repository Proker',
                href: '/repository',
            },
        ],
    },
});

const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');

// Local searching & filtering for fast UX
const filteredDocuments = computed(() => {
    return props.documents.filter(doc => {
        const matchesSearch = 
            doc.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            doc.file_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (doc.description && doc.description.toLowerCase().includes(searchQuery.value.toLowerCase()));
        
        const matchesCategory = selectedCategory.value === '' || doc.category === selectedCategory.value;
        
        return matchesSearch && matchesCategory;
    });
});

// Modal state
const isModalOpen = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);
const selectedFile = ref<File | null>(null);

const form = useForm({
    title: '',
    category: 'proposal',
    description: '',
    file: null as File | null,
});

const openUploadModal = () => {
    selectedFile.value = null;
    form.reset();
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    selectedFile.value = null;
    form.reset();
};

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        selectedFile.value = target.files[0];
        form.file = target.files[0];
        if (!form.title) {
            // Auto-fill title with file name minus extension
            const nameWithoutExt = target.files[0].name.replace(/\.[^/.]+$/, "");
            form.title = nameWithoutExt;
        }
    }
};

const submitForm = () => {
    if (!form.file) {
        toast.error('Silakan pilih berkas dokumen terlebih dahulu.');
        return;
    }

    form.post('/repository', {
        onSuccess: () => {
            toast.success('Dokumen berhasil diunggah ke repository.');
            closeModal();
        },
        onError: (errors) => {
            if (errors.file) {
                toast.error(errors.file);
            } else {
                toast.error('Gagal mengunggah dokumen. Periksa kembali form input Anda.');
            }
        }
    });
};

const downloadDocument = (docId: number) => {
    window.location.href = `/repository/${docId}/download`;
};

const confirmDelete = async (doc: Document) => {
    const isConfirmed = await confirm({
        title: 'Hapus Dokumen?',
        message: `Apakah Anda yakin ingin menghapus dokumen <strong>${doc.title}</strong> dari repository? Tindakan ini akan menghapus berkas fisik secara permanen.`,
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        variant: 'destructive',
    });

    if (isConfirmed) {
        router.delete(`/repository/${doc.id}`, {
            onSuccess: () => {
                toast.success('Dokumen berhasil dihapus.');
            },
            onError: () => {
                toast.error('Gagal menghapus dokumen.');
            }
        });
    }
};

const getCategoryDetails = (cat: string) => {
    switch (cat) {
        case 'proposal':
            return {
                label: 'Proposal Kegiatan',
                colorClass: 'bg-indigo-50 text-indigo-700 border-indigo-200',
                pillColor: 'bg-indigo-500'
            };
        case 'lpj':
            return {
                label: 'Laporan (LPJ)',
                colorClass: 'bg-emerald-50 text-emerald-700 border-emerald-200',
                pillColor: 'bg-emerald-500'
            };
        case 'perizinan':
            return {
                label: 'Surat & Perizinan',
                colorClass: 'bg-amber-50 text-amber-700 border-amber-200',
                pillColor: 'bg-amber-500'
            };
        case 'notulensi':
            return {
                label: 'Notulensi Rapat',
                colorClass: 'bg-purple-50 text-purple-700 border-purple-200',
                pillColor: 'bg-purple-500'
            };
        case 'desain':
            return {
                label: 'Desain & Publikasi',
                colorClass: 'bg-pink-50 text-pink-700 border-pink-200',
                pillColor: 'bg-pink-500'
            };
        default:
            return {
                label: 'Berkas Lainnya',
                colorClass: 'bg-slate-50 text-slate-700 border-slate-200',
                pillColor: 'bg-slate-500'
            };
    }
};

const getFileIconAndColor = (fileName: string, mime: string) => {
    const ext = fileName.split('.').pop()?.toLowerCase();
    
    if (ext === 'pdf' || mime.includes('pdf')) {
        return { icon: FileText, color: 'text-red-500 bg-red-50 border-red-100' };
    }
    if (['doc', 'docx'].includes(ext || '') || mime.includes('word') || mime.includes('officedocument.wordprocessingml')) {
        return { icon: FileText, color: 'text-blue-500 bg-blue-50 border-blue-100' };
    }
    if (['xls', 'xlsx'].includes(ext || '') || mime.includes('excel') || mime.includes('sheet') || mime.includes('officedocument.spreadsheetml')) {
        return { icon: FileSpreadsheet, color: 'text-emerald-500 bg-emerald-50 border-emerald-100' };
    }
    if (['zip', 'rar', '7z', 'tar', 'gz'].includes(ext || '') || mime.includes('zip') || mime.includes('compressed')) {
        return { icon: FileArchive, color: 'text-purple-500 bg-purple-50 border-purple-100' };
    }
    if (['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'].includes(ext || '') || mime.includes('image')) {
        return { icon: FileImage, color: 'text-pink-500 bg-pink-50 border-pink-100' };
    }
    return { icon: File, color: 'text-slate-500 bg-slate-50 border-slate-100' };
};
</script>

<template>
    <Head title="Repository Dokumen Proker KKN" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-7xl mx-auto font-sans">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Repository Dokumen Proker</h1>
                <p class="text-sm text-slate-500">Pusat berkas posko: simpan proposal program kerja, laporan pertanggungjawaban (LPJ), berkas administrasi desa, dan materi publikasi.</p>
            </div>
            
            <Button 
                v-if="canWrite"
                @click="openUploadModal" 
                class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-4 py-2.5 rounded-xl transition duration-200 flex items-center gap-2 cursor-pointer shadow-xs"
            >
                <Plus class="size-4" /> Unggah Dokumen
            </Button>
        </div>

        <!-- Filters -->
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between rounded-xl border border-slate-200 bg-white p-4 shadow-xs">
            <div class="relative w-full md:w-96">
                <Search class="absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari nama berkas, judul, atau catatan..."
                    class="w-full rounded-xl border border-slate-200 pl-10 pr-4 py-2 text-sm focus:border-sky-500 focus:outline-none"
                />
            </div>

            <!-- Category Filter -->
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
                    v-for="cat in ['proposal', 'lpj', 'perizinan', 'notulensi', 'desain', 'lainnya']"
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

        <!-- Document Grid -->
        <div v-if="filteredDocuments.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
                v-for="doc in filteredDocuments" 
                :key="doc.id"
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs hover:shadow-md hover:border-slate-300 transition-all duration-300 flex flex-col justify-between"
            >
                <div>
                    <!-- Document Preview Container -->
                    <div class="relative w-full mb-3">
                        <img 
                            v-if="isImageFile(doc.mime_type)" 
                            :src="'/repository/' + doc.id + '/view'" 
                            class="w-full h-32 object-cover rounded-xl border border-slate-100 cursor-pointer hover:opacity-90 transition duration-200" 
                            @click="openPreviewModal(doc)" 
                        />
                        <div 
                            v-else-if="isPdfFile(doc.mime_type)" 
                            @click="openPreviewModal(doc)"
                            class="w-full h-32 bg-slate-50 border border-slate-100 rounded-xl flex flex-col items-center justify-center gap-1.5 cursor-pointer hover:bg-slate-100/70 transition duration-200 group"
                        >
                            <FileText class="size-8 text-red-500 group-hover:scale-105 transition duration-200" />
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Buka PDF</span>
                        </div>
                        <div 
                            v-else 
                            @click="openPreviewModal(doc)"
                            class="w-full h-32 bg-slate-50 border border-slate-100 rounded-xl flex flex-col items-center justify-center gap-1.5 cursor-pointer hover:bg-slate-100/70 transition duration-200"
                        >
                            <component :is="getFileIconAndColor(doc.file_name, doc.mime_type).icon" class="size-8 text-slate-400" />
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Lihat Berkas</span>
                        </div>
                    </div>

                    <!-- Header Category Badge -->
                    <div class="flex items-center justify-between gap-4 mb-3">
                        <span :class="['px-2.5 py-0.5 rounded-full border text-[10px] font-semibold flex items-center gap-1', getCategoryDetails(doc.category).colorClass]">
                            <component :is="getFileIconAndColor(doc.file_name, doc.mime_type).icon" class="size-3 shrink-0" />
                            {{ getCategoryDetails(doc.category).label }}
                        </span>
                        <span class="text-[10px] text-slate-400 font-medium font-mono">{{ doc.file_size }}</span>
                    </div>

                    <!-- Title & Details -->
                    <h3 class="font-bold text-slate-900 text-base mb-1 leading-snug break-words cursor-pointer hover:text-sky-500 transition duration-200" @click="openPreviewModal(doc)" :title="doc.title">
                        {{ doc.title }}
                    </h3>
                    <p class="text-[11px] text-slate-400 font-mono mb-3 truncate" :title="doc.file_name">
                        {{ doc.file_name }}
                    </p>

                    <!-- Description -->
                    <p v-if="doc.description" class="text-xs text-slate-600 line-clamp-2 mb-4 leading-relaxed bg-slate-50/60 p-3 rounded-xl border border-slate-100 italic">
                        "{{ doc.description }}"
                    </p>
                </div>

                <!-- Footer Stats & Actions -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3 mt-4">
                    <div class="flex flex-col gap-0.5">
                        <span class="text-[10px] text-slate-400">Pengunggah:</span>
                        <span class="text-xs font-bold text-slate-700 truncate max-w-[120px]">{{ doc.uploaded_by }}</span>
                    </div>

                    <div class="flex items-center gap-1">
                        <button 
                            @click="openPreviewModal(doc)"
                            class="p-2 text-slate-500 hover:text-sky-500 rounded-xl hover:bg-sky-50 border border-slate-100 transition cursor-pointer flex items-center gap-1 text-xs font-semibold"
                            title="Baca / Pratinjau Dokumen"
                        >
                            <Eye class="size-4" /> Baca
                        </button>
                        <button 
                            @click="downloadDocument(doc.id)"
                            class="p-2 text-slate-500 hover:text-sky-500 rounded-xl hover:bg-sky-50 border border-slate-100 transition cursor-pointer flex items-center gap-1 text-xs font-semibold"
                            title="Unduh Berkas"
                        >
                            <Download class="size-4" />
                        </button>
                        <button 
                            v-if="canWrite"
                            @click="confirmDelete(doc)"
                            class="p-2 text-slate-400 hover:text-red-500 rounded-xl hover:bg-red-50 transition cursor-pointer border border-transparent"
                            title="Hapus Dokumen"
                        >
                            <Trash2 class="size-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-else class="flex flex-col items-center justify-center py-20 px-4 border border-dashed border-slate-200 rounded-2xl bg-white text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 mb-4 shadow-inner">
                <Folder class="size-6 text-slate-400" />
            </div>
            <h3 class="font-bold text-slate-800 text-base mb-1">Dokumen Tidak Ditemukan</h3>
            <p class="text-sm text-slate-500 max-w-sm">Belum ada berkas dokumen yang cocok dengan filter atau pencarian Anda.</p>
        </div>

        <!-- Upload Document Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
            <div class="w-full max-w-md bg-white rounded-2xl border border-slate-100 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="p-5 border-b flex justify-between items-center bg-slate-50">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <Plus class="size-5 text-sky-500" />
                        Unggah Dokumen Posko
                    </h3>
                    <button @click="closeModal" class="p-1 rounded-lg hover:bg-slate-200 text-slate-400 transition cursor-pointer">
                        <X class="size-5" />
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="p-5 space-y-4">
                    <!-- File Input Box -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Pilih Berkas Dokumen</label>
                        <div 
                            @click="fileInput?.click()"
                            class="border-2 border-dashed border-slate-200 hover:border-sky-400 rounded-xl p-6 flex flex-col items-center justify-center cursor-pointer bg-slate-50/50 hover:bg-sky-50/10 transition duration-200"
                        >
                            <input 
                                ref="fileInput" 
                                type="file" 
                                class="hidden" 
                                @change="handleFileChange"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,image/*"
                            />
                            <Folder class="size-8 text-slate-400 mb-2" />
                            <span class="text-xs font-bold text-slate-600 text-center">
                                {{ selectedFile ? selectedFile.name : 'Klik untuk memilih berkas dokumen' }}
                            </span>
                            <span class="text-[10px] text-slate-400 mt-1 text-center">
                                PDF, Word, Excel, PPT, ZIP, RAR, atau Gambar (Maks. 20MB)
                            </span>
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Nama/Judul Dokumen</label>
                        <input
                            v-model="form.title"
                            type="text"
                            placeholder="Contoh: Proposal Sanitasi Dusun III"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none"
                            required
                        />
                        <p v-if="form.errors.title" class="text-xs text-red-500">{{ form.errors.title }}</p>
                    </div>

                    <!-- Category Select -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Kategori Dokumen</label>
                        <select
                            v-model="form.category"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none bg-white"
                            required
                        >
                            <option value="proposal">Proposal Kegiatan</option>
                            <option value="lpj">Laporan Pertanggungjawaban (LPJ)</option>
                            <option value="perizinan">Surat & Dokumen Perizinan</option>
                            <option value="notulensi">Notulensi Rapat Posko</option>
                            <option value="desain">Desain, Banner & Pamflet</option>
                            <option value="lainnya">Berkas Administratif Lainnya</option>
                        </select>
                        <p v-if="form.errors.category" class="text-xs text-red-500">{{ form.errors.category }}</p>
                    </div>

                    <!-- Description/Notes -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Catatan/Keterangan Berkas</label>
                        <textarea
                            v-model="form.description"
                            rows="2"
                            placeholder="Keterangan singkat mengenai dokumen..."
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none"
                        ></textarea>
                        <p v-if="form.errors.description" class="text-xs text-red-500">{{ form.errors.description }}</p>
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
                            <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                            Unggah Berkas
                        </Button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview Document Modal -->
        <div v-if="isPreviewModalOpen && selectedDocForPreview" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
            <div class="w-full max-w-4xl bg-white rounded-2xl border border-slate-100 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-200 flex flex-col max-h-[90vh]">
                <!-- Modal Header -->
                <div class="p-5 border-b flex justify-between items-center bg-slate-50 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div :class="['p-2 rounded-lg border flex items-center justify-center', getFileIconAndColor(selectedDocForPreview.file_name, selectedDocForPreview.mime_type).color]">
                            <component :is="getFileIconAndColor(selectedDocForPreview.file_name, selectedDocForPreview.mime_type).icon" class="size-5" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 leading-none mb-1">
                                {{ selectedDocForPreview.title }}
                            </h3>
                            <p class="text-[10px] text-slate-400 font-mono leading-none">
                                {{ selectedDocForPreview.file_name }} ({{ selectedDocForPreview.file_size }})
                            </p>
                        </div>
                    </div>
                    
                    <button @click="closePreviewModal" class="p-1.5 rounded-lg hover:bg-slate-200 text-slate-400 transition cursor-pointer">
                        <X class="size-5" />
                    </button>
                </div>

                <!-- Modal Body (Preview Content Area) -->
                <div class="p-6 overflow-y-auto flex-1 bg-slate-50/50 flex flex-col justify-center">
                    <!-- Inline PDF Viewer -->
                    <div v-if="isPdfFile(selectedDocForPreview.mime_type)" class="w-full h-full flex flex-col">
                        <iframe 
                            :src="'/repository/' + selectedDocForPreview.id + '/view'" 
                            class="w-full h-[65vh] border border-slate-200 rounded-xl shadow-xs"
                        ></iframe>
                    </div>

                    <!-- Inline Image Viewer -->
                    <div v-else-if="isImageFile(selectedDocForPreview.mime_type)" class="text-center">
                        <img 
                            :src="'/repository/' + selectedDocForPreview.id + '/view'" 
                            class="max-w-full max-h-[65vh] object-contain rounded-xl shadow-md mx-auto border bg-white" 
                        />
                    </div>

                    <!-- Fallback / No Preview Available -->
                    <div v-else class="text-center max-w-md mx-auto py-12">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mx-auto mb-4">
                            <component :is="getFileIconAndColor(selectedDocForPreview.file_name, selectedDocForPreview.mime_type).icon" class="size-8" />
                        </div>
                        <h4 class="font-bold text-slate-800 text-base mb-1">Pratinjau Tidak Tersedia</h4>
                        <p class="text-xs text-slate-500 mb-6 leading-relaxed">
                            Format berkas ini (<strong>{{ selectedDocForPreview.file_name.split('.').pop()?.toUpperCase() }}</strong>) tidak mendukung pratinjau langsung di dalam browser. Silakan unduh berkas untuk membacanya.
                        </p>
                        <Button 
                            @click="downloadDocument(selectedDocForPreview.id)" 
                            class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-5 py-2.5 rounded-xl transition duration-200 flex items-center gap-2 mx-auto cursor-pointer"
                        >
                            <Download class="size-4" /> Unduh Dokumen
                        </Button>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t flex justify-between items-center bg-slate-50 shrink-0">
                    <div class="text-[10px] text-slate-400">
                        Diunggah oleh <span class="font-semibold">{{ selectedDocForPreview.uploaded_by }}</span> • {{ selectedDocForPreview.created_at }}
                    </div>
                    <div class="flex gap-2">
                        <Button type="button" variant="outline" @click="closePreviewModal" class="rounded-xl px-4 cursor-pointer">
                            Tutup
                        </Button>
                        <Button 
                            v-if="canPreviewInline(selectedDocForPreview.mime_type)"
                            @click="downloadDocument(selectedDocForPreview.id)" 
                            class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-4 cursor-pointer rounded-xl flex items-center gap-2"
                        >
                            <Download class="size-4" /> Unduh
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
