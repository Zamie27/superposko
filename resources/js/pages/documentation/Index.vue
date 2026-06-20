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
    immichUrl?: string;
    immichEmail?: string;
    immichPassword?: string;
    error: string | null;
    success?: string | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dokumentasi',
                href: '/host/documentation',
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

const CHUNK_SIZE = 15 * 1024 * 1024; // 15MB chunks
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
            uploadFileInChunks(item);
        });
        
        if (uploadInput.value) {
            uploadInput.value.value = '';
        }
    }
};

const uploadFileInChunks = async (item: UploadItem) => {
    item.status = 'uploading';
    const file = item.file;
    const totalSize = file.size;
    const totalChunks = Math.ceil(totalSize / CHUNK_SIZE);
    const uploadUuid = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);

    for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
        if ((item.status as string) === 'error') {
            break;
        }

        const start = chunkIndex * CHUNK_SIZE;
        const end = Math.min(start + CHUNK_SIZE, totalSize);
        const chunk = file.slice(start, end);

        const formData = new FormData();
        formData.append('file', chunk);
        formData.append('chunkIndex', chunkIndex.toString());
        formData.append('totalChunks', totalChunks.toString());
        formData.append('uploadUuid', uploadUuid);
        formData.append('filename', file.name);

        try {
            const response = await axios.post('/host/documentation/upload-chunk', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Accept': 'application/json'
                },
                onUploadProgress: (progressEvent) => {
                    if (progressEvent.total) {
                        const chunkPercent = (progressEvent.loaded / progressEvent.total);
                        const totalProgress = Math.round(((chunkIndex + chunkPercent) / totalChunks) * 100);
                        item.progress = Math.min(totalProgress, 99);
                    }
                }
            });

            if (response.data.status === 'success') {
                item.status = 'success';
                item.progress = 100;
                router.reload({ only: ['assets'] });
            }
        } catch (error: any) {
            item.status = 'error';
            item.error = error.response?.data?.message || 'Gagal mengunggah potongan file.';
            break;
        }
    }
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
        <!-- Header & Upload Info -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 bg-card border rounded-lg p-5 shadow-xs">
            <div>
                <h2 class="text-xl font-bold tracking-tight">Galeri Dokumentasi</h2>
                <p class="text-sm text-muted-foreground mt-1">Terkoneksi dengan server Immich.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-4 w-full lg:w-auto">
                <!-- Credentials Info -->
                <div v-if="immichEmail || immichPassword" class="flex flex-col sm:flex-row gap-4 p-3 bg-muted/40 border border-slate-100 dark:border-slate-800 rounded-lg text-xs text-muted-foreground flex-1 sm:flex-none">
                    <div v-if="immichEmail">
                        <span class="font-semibold block text-slate-700 dark:text-slate-300">Email Login Immich:</span>
                        <code class="bg-background px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700 select-all font-mono">{{ immichEmail }}</code>
                    </div>
                    <div v-if="immichPassword">
                        <span class="font-semibold block text-slate-700 dark:text-slate-300">Password:</span>
                        <code class="bg-background px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700 select-all font-mono">{{ immichPassword }}</code>
                    </div>
                </div>

                <!-- Local Upload Button -->
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <Label for="file-upload" class="cursor-pointer w-full sm:w-auto">
                        <div class="flex items-center gap-2 bg-primary text-primary-foreground hover:bg-primary/90 px-5 py-2.5 rounded-md font-semibold text-sm transition-colors shadow-xs justify-center">
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

                    <!-- External Immich link -->
                    <a 
                        v-if="immichUrl" 
                        :href="immichUrl" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="flex items-center justify-center p-2.5 text-muted-foreground hover:text-foreground hover:bg-muted border rounded-md transition-colors h-[38px] w-[38px]"
                        title="Buka Web Immich"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-external-link"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                    </a>
                </div>
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
