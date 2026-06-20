<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Upload, X, Download, Play, Image as ImageIcon, ChevronLeft, ChevronRight } from '@lucide/vue';
import axios from 'axios';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';

interface Asset {
    id: string;
    type: string;
    thumbnail_url: string;
    file_url: string;
    createdAt: string;
}

const props = defineProps<{
    assets: Asset[];
    error: string | null;
    success?: string | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dokumentasi',
                href: '/documentation',
            },
        ],
    },
});

type UploadItem = {
    id: string;
    file: File;
    progress: number;
    status: 'pending' | 'uploading' | 'success' | 'error';
    error?: string;
};

const uploadQueue = ref<UploadItem[]>([]);
const isUploadPanelMinimized = ref(false);

const uploadInput = ref<HTMLInputElement | null>(null);

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;

    if (target.files && target.files.length > 0) {
        Array.from(target.files).forEach(file => {
            const id = Math.random().toString(36).substring(7);
            const item: UploadItem = {
                id,
                file,
                progress: 0,
                status: 'pending',
            };
            uploadQueue.value.push(item);
            uploadFile(item);
        });
        
        if (uploadInput.value) {
uploadInput.value.value = '';
}
    }
};

const uploadFile = (item: UploadItem) => {
    item.status = 'uploading';
    
    const formData = new FormData();
    formData.append('file', item.file);

    axios.post('/documentation/upload', formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
            'Accept': 'application/json'
        },
        onUploadProgress: (progressEvent) => {
            if (progressEvent.total) {
                const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                item.progress = percentCompleted;
            }
        }
    }).then(() => {
        item.status = 'success';
        item.progress = 100;
        router.reload({ only: ['assets'] }); // Refresh the gallery
    }).catch((error) => {
        item.status = 'error';

        if (error.response?.status === 413) {
            item.error = 'Ukuran file terlalu besar (Maks 500MB)';
        } else if (error.response?.status === 422) {
            item.error = error.response.data.errors?.file?.[0] || 'Format tidak didukung';
        } else {
            item.error = error.response?.data?.message || 'Gagal mengunggah';
        }
    });
};

const clearCompleted = () => {
    uploadQueue.value = uploadQueue.value.filter(i => i.status === 'uploading' || i.status === 'pending');
};

const removeUpload = (id: string) => {
    uploadQueue.value = uploadQueue.value.filter(i => i.id !== id);
};

const activeAsset = ref<Asset | null>(null);

const activeAssetIndex = computed(() => {
    if (!activeAsset.value) {
return -1;
}

    return props.assets.findIndex(a => a.id === activeAsset.value?.id);
});

const showPrev = () => {
    const index = activeAssetIndex.value;

    if (index > 0) {
        activeAsset.value = props.assets[index - 1];
    }
};

const showNext = () => {
    const index = activeAssetIndex.value;

    if (index !== -1 && index < props.assets.length - 1) {
        activeAsset.value = props.assets[index + 1];
    }
};

const handleKeyDown = (e: KeyboardEvent) => {
    if (!activeAsset.value) {
return;
}

    if (e.key === 'ArrowLeft') {
        showPrev();
    } else if (e.key === 'ArrowRight') {
        showNext();
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
});

const openLightbox = (asset: Asset) => {
    activeAsset.value = asset;
};

const closeLightbox = () => {
    activeAsset.value = null;
};
</script>

<template>
    <Head title="Dokumentasi" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <!-- Header & Upload -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-card border rounded-lg p-4">
            <div>
                <h2 class="text-xl font-bold">Galeri Dokumentasi</h2>
                <p class="text-sm text-muted-foreground">Terkoneksi dengan server Immich</p>
            </div>
            
            <div class="flex items-center gap-2">
                <Label for="file-upload" class="cursor-pointer">
                    <div class="flex items-center gap-2 bg-primary text-primary-foreground hover:bg-primary/90 px-4 py-2 rounded-md font-medium text-sm transition-colors">
                        <Upload class="w-4 h-4" />
                        <span>Unggah</span>
                    </div>
                </Label>
                <input 
                    id="file-upload" 
                    type="file" 
                    class="hidden" 
                    ref="uploadInput"
                    accept="image/*,video/*"
                    multiple
                    @change="handleFileChange"
                />
            </div>
        </div>

        <!-- Errors -->
        <div v-if="error" class="bg-destructive/10 text-destructive p-4 rounded-md text-sm border border-destructive/20">
            {{ error }}
        </div>
        <div v-if="success" class="bg-green-500/10 text-green-500 p-4 rounded-md text-sm border border-green-500/20">
            {{ success }}
        </div>

        <!-- Gallery Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            <div v-if="assets.length === 0" class="col-span-full py-12 text-center text-muted-foreground bg-muted/30 rounded-lg border border-dashed">
                Belum ada dokumentasi.
            </div>

            <div 
                v-for="asset in assets" 
                :key="asset.id"
                class="relative aspect-square rounded-lg overflow-hidden border bg-muted group cursor-pointer hover:ring-2 ring-primary transition-all"
                @click="openLightbox(asset)"
            >
                <img 
                    v-if="asset.thumbnail_url" 
                    :src="asset.thumbnail_url" 
                    class="w-full h-full object-cover"
                    loading="lazy"
                />
                <div v-else class="w-full h-full flex items-center justify-center text-muted-foreground">
                    <ImageIcon v-if="asset.type === 'IMAGE'" class="w-8 h-8 opacity-50" />
                    <Play v-if="asset.type === 'VIDEO'" class="w-8 h-8 opacity-50" />
                </div>
                
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <Play v-if="asset.type === 'VIDEO'" class="w-12 h-12 text-white/80" />
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Manager Modal -->
    <div v-if="uploadQueue.length > 0" class="fixed bottom-4 right-4 z-50 w-96 bg-card border rounded-lg shadow-xl overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-4 py-3 bg-muted/50 border-b cursor-pointer hover:bg-muted/70 transition-colors" @click="isUploadPanelMinimized = !isUploadPanelMinimized">
            <div>
                <h3 class="font-medium text-sm text-slate-800 dark:text-slate-200">
                    Tersisa {{ uploadQueue.filter(i => i.status === 'pending' || i.status === 'uploading').length }} 
                    - Diproses {{ uploadQueue.filter(i => i.status === 'uploading').length }}/{{ uploadQueue.length }}
                </h3>
                <p class="text-xs text-muted-foreground mt-0.5">
                    Diunggah <span class="text-green-500 font-medium">{{ uploadQueue.filter(i => i.status === 'success').length }}</span>
                    - Kesalahan <span class="text-red-500 font-medium">{{ uploadQueue.filter(i => i.status === 'error').length }}</span>
                </p>
            </div>
            <div class="flex items-center gap-1 text-muted-foreground">
                <Button variant="ghost" size="icon" class="h-6 w-6" @click.stop="clearCompleted" title="Bersihkan yang selesai">
                    <X class="w-4 h-4" />
                </Button>
            </div>
        </div>

        <div v-show="!isUploadPanelMinimized" class="max-h-80 overflow-y-auto p-2 flex flex-col gap-2 bg-slate-50 dark:bg-slate-900/50">
            <div v-for="item in uploadQueue" :key="item.id" class="flex flex-col p-3 border rounded-md bg-background text-sm shadow-sm" :class="{'border-red-200 bg-red-50 dark:bg-red-950/20': item.status === 'error', 'border-green-200 bg-green-50 dark:bg-green-950/20': item.status === 'success'}">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <ImageIcon class="w-4 h-4 text-muted-foreground flex-shrink-0" />
                        <span class="truncate font-medium" :title="item.file.name">{{ item.file.name }}</span>
                    </div>
                    <Button v-if="item.status !== 'uploading'" variant="ghost" size="icon" class="h-5 w-5 flex-shrink-0" @click="removeUpload(item.id)">
                        <X class="w-3 h-3" />
                    </Button>
                </div>

                <div v-if="item.status === 'uploading'" class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden mb-1">
                    <div class="bg-primary h-2 rounded-full transition-all duration-300" :style="`width: ${item.progress}%`"></div>
                </div>
                <div v-if="item.status === 'uploading'" class="text-xs text-muted-foreground text-center">
                    Mengunggah... {{ item.progress }}%
                </div>
                
                <div v-if="item.status === 'success'" class="text-xs text-green-600 dark:text-green-400 font-medium flex items-center gap-1">
                    ✓ Berhasil diunggah
                </div>

                <div v-if="item.status === 'error'" class="text-xs text-red-600 dark:text-red-400 font-medium flex flex-col gap-1">
                    <span>! Gagal: {{ item.error }}</span>
                </div>
            </div>
        </div>
    </div>

    <Dialog :open="!!activeAsset" @update:open="!$event && closeLightbox()">
        <DialogContent 
            :show-close-button="false"
            class="max-w-[100vw] w-full h-[100vh] sm:max-w-[100vw] sm:h-[100vh] bg-black/98 border-none shadow-2xl text-white flex flex-col p-0 overflow-hidden rounded-none sm:rounded-none gap-0"
        >
            <!-- Top bar -->
            <div class="absolute top-0 inset-x-0 z-50 flex items-center justify-between p-4 bg-gradient-to-b from-black/80 via-black/45 to-transparent pointer-events-none">
                <div class="text-sm font-semibold text-white/90 drop-shadow-md select-none">
                    {{ activeAssetIndex + 1 }} / {{ assets.length }}
                </div>
                <div class="flex items-center gap-3 pointer-events-auto">
                    <a :href="(activeAsset?.file_url ?? '') + '?download=true'" class="text-white hover:text-primary transition-colors">
                        <Button variant="ghost" size="icon" class="h-10 w-10 bg-black/40 hover:bg-white/10 rounded-full" title="Unduh">
                            <Download class="w-5 h-5" />
                        </Button>
                    </a>
                    <Button variant="ghost" size="icon" class="h-10 w-10 bg-black/40 hover:bg-white/10 text-white rounded-full transition-colors" @click="closeLightbox" title="Tutup (Esc)">
                        <X class="w-5 h-5" />
                    </Button>
                </div>
            </div>

            <!-- Main media content -->
            <div class="relative flex-1 w-full h-full flex items-center justify-center p-0 select-none bg-black">
                <!-- Prev Button -->
                <button 
                    v-if="activeAssetIndex > 0"
                    @click="showPrev"
                    class="absolute left-6 z-50 p-3 bg-black/40 hover:bg-black/75 hover:scale-105 active:scale-95 text-white rounded-full transition-all border border-white/10 shadow-lg cursor-pointer"
                    title="Sebelumnya (←)"
                >
                    <ChevronLeft class="w-7 h-7" />
                </button>

                <!-- Next Button -->
                <button 
                    v-if="activeAssetIndex < assets.length - 1"
                    @click="showNext"
                    class="absolute right-6 z-50 p-3 bg-black/40 hover:bg-black/75 hover:scale-105 active:scale-95 text-white rounded-full transition-all border border-white/10 shadow-lg cursor-pointer"
                    title="Berikutnya (→)"
                >
                    <ChevronRight class="w-7 h-7" />
                </button>

                <!-- Image view -->
                <img 
                    v-if="activeAsset?.type === 'IMAGE'" 
                    :src="activeAsset?.file_url" 
                    class="w-full h-full max-w-[100vw] max-h-[100vh] object-contain select-none" 
                    alt="Media Detail"
                />
                
                <!-- Video view -->
                <video 
                    v-else-if="activeAsset?.type === 'VIDEO'" 
                    :src="activeAsset?.file_url" 
                    controls 
                    autoplay
                    class="w-full h-full max-w-[100vw] max-h-[100vh] object-contain outline-none"
                ></video>
            </div>
        </DialogContent>
    </Dialog>
</template>
