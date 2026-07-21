<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch } from 'vue';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { 
    ArrowLeft, Save, Image as ImageIcon, Heading1, Heading2, 
    Bold, Italic, List, Quote, Sparkles, Eye, Edit3, Wand2, FileText, Code 
} from '@lucide/vue';
import { useToast } from '@/composables/useToast';

import axios from 'axios';

const props = defineProps<{
    article?: any;
    categories: string[];
}>();

const toast = useToast();

const isEditing = computed(() => !!props.article?.id);
const activeTab = ref<'editor' | 'preview'>('editor');
const isCodeMode = ref(false);

const form = useForm({
    title: props.article?.title || '',
    category: props.article?.category || 'Kegiatan Posko',
    tags: props.article?.tags ? props.article.tags.join(', ') : '',
    excerpt: props.article?.excerpt || '',
    content: props.article?.content || '',
    is_published: props.article?.is_published !== undefined ? props.article.is_published : true,
    cover_image: null as File | null,
});

const previewCoverUrl = ref<string | null>(props.article?.cover_image_url || null);
const visualEditorRef = ref<HTMLDivElement | null>(null);
const inlineImageInputRef = ref<HTMLInputElement | null>(null);
const isUploadingInlineImage = ref(false);

const triggerInlineImageSelect = () => {
    inlineImageInputRef.value?.click();
};

const uploadInlineImage = async (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (!target.files || !target.files[0]) return;

    const file = target.files[0];
    const formData = new FormData();
    formData.append('image', file);

    isUploadingInlineImage.value = true;
    try {
        const response = await axios.post('/management/news/upload-image', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });

        const imageUrl = response.data.url;
        if (imageUrl) {
            execFormat('insertImage', imageUrl);
            toast.success('Gambar berhasil disisipkan ke dalam artikel!');
        }
    } catch (err: any) {
        toast.error('Gagal mengunggah gambar artikel.');
    } finally {
        isUploadingInlineImage.value = false;
        if (target) target.value = '';
    }
};

// Sync visual editor content to form.content
const syncContentFromVisual = () => {
    if (visualEditorRef.value && !isCodeMode.value) {
        form.content = visualEditorRef.value.innerHTML;
    }
};

// Exec WYSIWYG formatting commands
const execFormat = (command: string, value: string | null = null) => {
    if (isCodeMode.value) return;
    
    document.execCommand(command, false, value ?? undefined);
    syncContentFromVisual();
    
    if (visualEditorRef.value) {
        visualEditorRef.value.focus();
    }
};

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        form.cover_image = file;
        previewCoverUrl.value = URL.createObjectURL(file);
    }
};

const loadSampleTemplate = () => {
    if (form.content && !confirm('Isi artikel saat ini akan diganti dengan struktur template berita posko. Lanjutkan?')) {
        return;
    }

    form.title = form.title || 'Sosialisasi & Program Pengabdian Masyarakat oleh Posko KKN';
    form.category = 'Kegiatan Posko';
    form.tags = 'KKN, Pengabdian, Desa, Kegiatan';
    
    const sampleHtml = `<h2>Latar Belakang Kegiatan</h2>
<p>Dalam rangka mendukung pemberdayaan masyarakat desa, tim mahasiswa KKN Posko melaksanakan program kerja utama berupa sosialisasi dan pendampingan warga secara langsung.</p>

<h2>Tujuan & Sasaran</h2>
<p>Program kerja ini bertujuan untuk memberikan edukasi dan solusi praktis bagi permasalahan warga desa. Beberapa poin utama pelaksanaan meliputi:</p>
<ul>
  <li>Meningkatkan pemahaman warga mengenai tata kelola dan digitalisasi.</li>
  <li>Mendorong partisipasi aktif pemuda desa dalam program pengabdian.</li>
  <li>Membangun silaturahmi yang erat antara mahasiswa KKN dan tokoh masyarakat.</li>
</ul>

<blockquote>"Melalui kegiatan pengabdian ini, kami berharap dapat memberikan dampak positif yang berkelanjutan bagi perkembangan warga desa."</blockquote>

<h2>Hasil & Impact Pengabdian</h2>
<p>Kegiatan berjalan dengan lancar dan disambut antusias oleh seluruh warga. Seluruh rangkaian program terlaksana sesuai dengan jadwal perencanaan posko.</p>`;

    form.content = sampleHtml;
    if (visualEditorRef.value) {
        visualEditorRef.value.innerHTML = sampleHtml;
    }

    form.excerpt = 'Sosialisasi dan pendampingan masyarakat desa oleh tim mahasiswa KKN Posko secara langsung untuk peningkatan kesejahteraan warga.';
    toast.success('Template berita posko berhasil dimuat!');
};

const generateAutoExcerpt = () => {
    if (!form.content) {
        toast.error('Tuliskan konten artikel terlebih dahulu.');
        return;
    }

    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = form.content;
    const plainText = tempDiv.textContent || tempDiv.innerText || '';
    
    const trimmed = plainText.trim().substring(0, 160);
    form.excerpt = trimmed ? `${trimmed}...` : '';
    toast.success('Ringkasan singkat berhasil dibuat otomatis!');
};

const toggleCodeMode = () => {
    if (isCodeMode.value) {
        // Exiting code mode: apply textarea content to visual editor
        if (visualEditorRef.value) {
            visualEditorRef.value.innerHTML = form.content;
        }
        isCodeMode.value = false;
    } else {
        // Entering code mode: get HTML from visual editor
        syncContentFromVisual();
        isCodeMode.value = true;
    }
};

const submitForm = () => {
    syncContentFromVisual();

    if (!form.title.trim()) {
        toast.error('Judul berita tidak boleh kosong.');
        return;
    }
    if (!form.content.trim() || form.content === '<br>') {
        toast.error('Isi konten berita tidak boleh kosong.');
        return;
    }

    const tagsArray = form.tags
        .split(',')
        .map((t) => t.trim())
        .filter((t) => t.length > 0);

    const formData = new FormData();
    formData.append('title', form.title);
    formData.append('category', form.category);
    formData.append('excerpt', form.excerpt);
    formData.append('content', form.content);
    formData.append('is_published', form.is_published ? '1' : '0');
    
    tagsArray.forEach((tag, idx) => {
        formData.append(`tags[${idx}]`, tag);
    });

    if (form.cover_image) {
        formData.append('cover_image', form.cover_image);
    }

    if (isEditing.value) {
        router.post(`/management/news/${props.article.id}`, formData, {
            onSuccess: () => toast.success('Artikel berita posko berhasil diperbarui!'),
        });
    } else {
        router.post('/management/news', formData, {
            onSuccess: () => toast.success('Artikel berita posko baru berhasil diterbitkan!'),
        });
    }
};

onMounted(() => {
    if (visualEditorRef.value && form.content) {
        visualEditorRef.value.innerHTML = form.content;
    }
});
</script>

<template>
    <Head :title="isEditing ? 'Edit Artikel Berita' : 'Tulis Artikel Berita Baru'" />

    <div class="relative flex flex-col gap-6 rounded-xl p-4 sm:p-6 min-h-[400px] w-full max-w-full">
            
        <!-- Page Top Bar -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-200 dark:border-slate-800 pb-4">
            <div class="space-y-1">
                <Link href="/management/news" class="text-xs font-bold text-slate-500 hover:text-[#38BDF8] flex items-center gap-1">
                    <ArrowLeft class="w-3.5 h-3.5" />
                    <span>Kembali ke Daftar Berita</span>
                </Link>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <span>{{ isEditing ? 'Edit Artikel Berita Posko' : 'Tulis Artikel Berita Baru' }}</span>
                    <Sparkles class="w-5 h-5 text-amber-500" />
                </h1>
            </div>

            <!-- Header Action Buttons -->
            <div class="flex items-center gap-3">
                <button 
                    @click="loadSampleTemplate"
                    type="button"
                    class="px-3.5 py-2 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 hover:bg-amber-100 text-xs font-bold border border-amber-300 dark:border-amber-800 flex items-center gap-1.5 cursor-pointer shadow-2xs transition-colors"
                    title="Isi otomatis dengan struktur berita posko standar"
                >
                    <Wand2 class="w-4 h-4 text-amber-500" />
                    <span>Gunakan Template Berita</span>
                </button>

                <Link href="/management/news">
                    <Button variant="outline" class="cursor-pointer">Batal</Button>
                </Link>
                <Button 
                    @click="submitForm" 
                    :disabled="form.processing"
                    class="bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white font-bold shadow-xs cursor-pointer rounded-lg px-4 py-2"
                >
                    <Save class="w-4 h-4 mr-2" />
                    <span>{{ form.processing ? 'Menyimpan...' : 'Simpan & Publis' }}</span>
                </Button>
            </div>
        </div>

        <!-- Main Form Card -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Form Column (8 cols) -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Title Input -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-900 dark:text-white block">
                        Judul Berita / Artikel <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        v-model="form.title" 
                        type="text" 
                        placeholder="Contoh: Sosialisasi Digitalisasi UMKM Desa Tebet oleh Posko 18" 
                        class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl px-4 py-3 text-base text-slate-900 dark:text-white font-bold placeholder-slate-400 focus:outline-none focus:border-[#38BDF8]"
                    />
                    <InputError :message="form.errors.title" />
                </div>

                <!-- Editor Container -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-bold text-slate-900 dark:text-white block">
                            Isi Konten Artikel <span class="text-rose-500">*</span>
                        </label>
                        
                        <!-- Mode Tabs (Visual Editor vs Preview) -->
                        <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700">
                            <button 
                                @click="activeTab = 'editor'" 
                                type="button"
                                :class="activeTab === 'editor' ? 'bg-white dark:bg-slate-900 text-[#38BDF8] font-bold shadow-2xs' : 'text-slate-600 dark:text-slate-400'"
                                class="px-3 py-1 text-xs rounded-lg flex items-center gap-1.5 transition-all cursor-pointer"
                            >
                                <Edit3 class="w-3.5 h-3.5" />
                                <span>Editor Visual (WYSIWYG)</span>
                            </button>
                            <button 
                                @click="activeTab = 'preview'" 
                                type="button"
                                :class="activeTab === 'preview' ? 'bg-white dark:bg-slate-900 text-[#38BDF8] font-bold shadow-2xs' : 'text-slate-600 dark:text-slate-400'"
                                class="px-3 py-1 text-xs rounded-lg flex items-center gap-1.5 transition-all cursor-pointer"
                            >
                                <Eye class="w-3.5 h-3.5" />
                                <span>Pratinjau Hasil</span>
                            </button>
                        </div>
                    </div>

                    <!-- WYSIWYG Visual Editor Mode -->
                    <div v-show="activeTab === 'editor'" class="rounded-xl border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-2xs">
                        
                        <!-- Visual Toolbar Buttons -->
                        <div class="flex flex-wrap items-center justify-between gap-1.5 p-2 bg-slate-100 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <button 
                                    @click="execFormat('formatBlock', '<h2>')" 
                                    type="button" 
                                    class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:border-[#38BDF8] text-xs font-bold flex items-center gap-1 text-sky-600 dark:text-sky-400 cursor-pointer shadow-2xs" 
                                    title="Judul Utama Bab (H2)"
                                >
                                    <Heading1 class="w-3.5 h-3.5" />
                                    <span>Judul Bab (H2)</span>
                                </button>

                                <button 
                                    @click="execFormat('formatBlock', '<h3>')" 
                                    type="button" 
                                    class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:border-[#38BDF8] text-xs font-bold flex items-center gap-1 text-sky-600 dark:text-sky-400 cursor-pointer shadow-2xs" 
                                    title="Sub-Bab (H3)"
                                >
                                    <Heading2 class="w-3.5 h-3.5" />
                                    <span>Sub-Bab (H3)</span>
                                </button>

                                <div class="h-4 w-px bg-slate-300 dark:bg-slate-700 mx-1"></div>

                                <button 
                                    @click="execFormat('bold')" 
                                    type="button" 
                                    class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:border-[#38BDF8] text-xs font-bold flex items-center gap-1 cursor-pointer shadow-2xs" 
                                    title="Cetak Tebal"
                                >
                                    <Bold class="w-3.5 h-3.5" />
                                    <span>Tebal</span>
                                </button>

                                <button 
                                    @click="execFormat('italic')" 
                                    type="button" 
                                    class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:border-[#38BDF8] text-xs font-bold flex items-center gap-1 cursor-pointer shadow-2xs" 
                                    title="Cetak Miring"
                                >
                                    <Italic class="w-3.5 h-3.5" />
                                    <span>Miring</span>
                                </button>

                                <button 
                                    @click="execFormat('formatBlock', '<blockquote>')" 
                                    type="button" 
                                    class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:border-[#38BDF8] text-xs font-bold flex items-center gap-1 text-amber-600 dark:text-amber-400 cursor-pointer shadow-2xs" 
                                    title="Format Kutipan"
                                >
                                    <Quote class="w-3.5 h-3.5" />
                                    <span>Kutipan</span>
                                </button>

                                <button 
                                    @click="execFormat('insertUnorderedList')" 
                                    type="button" 
                                    class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:border-[#38BDF8] text-xs font-bold flex items-center gap-1 text-indigo-600 dark:text-indigo-400 cursor-pointer shadow-2xs" 
                                    title="Daftar Poin"
                                >
                                    <List class="w-3.5 h-3.5" />
                                    <span>Daftar Poin</span>
                                </button>

                                <button 
                                    @click="execFormat('formatBlock', '<p>')" 
                                    type="button" 
                                    class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:border-[#38BDF8] text-xs font-bold cursor-pointer shadow-2xs" 
                                    title="Normal Paragraf"
                                >
                                    <span>Paragraf Normal</span>
                                </button>

                                <div class="h-4 w-px bg-slate-300 dark:bg-slate-700 mx-1"></div>

                                <button 
                                    @click="triggerInlineImageSelect" 
                                    type="button" 
                                    :disabled="isUploadingInlineImage"
                                    class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:border-[#38BDF8] text-xs font-bold flex items-center gap-1 text-emerald-600 dark:text-emerald-400 cursor-pointer shadow-2xs" 
                                    title="Unggah & Sisipkan Gambar ke Isi Artikel"
                                >
                                    <ImageIcon class="w-3.5 h-3.5" />
                                    <span>{{ isUploadingInlineImage ? 'Mengunggah...' : 'Sisipkan Gambar' }}</span>
                                </button>

                                <input 
                                    ref="inlineImageInputRef" 
                                    type="file" 
                                    accept="image/*" 
                                    @change="uploadInlineImage" 
                                    class="hidden" 
                                />
                            </div>

                            <!-- Mode HTML Toggle -->
                            <button 
                                @click="toggleCodeMode" 
                                type="button" 
                                class="px-2.5 py-1 rounded-lg text-[11px] font-semibold text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center gap-1 border border-transparent hover:border-slate-300 dark:hover:border-slate-600 transition-colors cursor-pointer"
                                title="Beralih ke mode Kode HTML"
                            >
                                <Code class="w-3.5 h-3.5 text-slate-400" />
                                <span>{{ isCodeMode ? 'Beralih ke Mode Visual' : 'Mode Kode HTML' }}</span>
                            </button>
                        </div>

                        <!-- 1. Real Visual ContentEditable Editor (Default) -->
                        <div 
                            v-if="!isCodeMode"
                            ref="visualEditorRef"
                            contenteditable="true"
                            @input="syncContentFromVisual"
                            @blur="syncContentFromVisual"
                            @keyup="syncContentFromVisual"
                            class="visual-wysiwyg-editor min-h-[350px] p-5 font-sans text-slate-900 dark:text-white leading-relaxed outline-none cursor-text"
                            placeholder="Ketik artikel Anda di sini secara langsung seperti di Word..."
                        ></div>

                        <!-- 2. Code HTML Editor (Advanced Mode) -->
                        <textarea 
                            v-else
                            v-model="form.content" 
                            rows="16" 
                            placeholder="Kode HTML artikel..."
                            class="w-full bg-slate-900 text-sky-300 font-mono text-xs p-4 outline-none leading-relaxed"
                        ></textarea>
                    </div>

                    <!-- Live Preview Mode Container -->
                    <div v-show="activeTab === 'preview'" class="rounded-xl border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 min-h-[350px]">
                        <div v-if="form.content" class="visual-wysiwyg-editor space-y-4" v-html="form.content"></div>
                        <div v-else class="text-center py-16 text-slate-400 space-y-2">
                            <FileText class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-700" />
                            <p class="font-medium">Belum ada teks yang ditulis.</p>
                            <p class="text-xs text-slate-500">Mulai ketik pada tab Editor Visual.</p>
                        </div>
                    </div>

                    <InputError :message="form.errors.content" />
                </div>

                <!-- Excerpt Input with Auto Generator -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-bold text-slate-900 dark:text-white">Ringkasan Singkat (Excerpt)</label>
                        <button 
                            @click="generateAutoExcerpt" 
                            type="button" 
                            class="text-xs font-bold text-[#38BDF8] hover:text-sky-600 flex items-center gap-1 cursor-pointer"
                        >
                            <Wand2 class="w-3.5 h-3.5" />
                            <span>Isi Ringkasan Otomatis</span>
                        </button>
                    </div>
                    <textarea 
                        v-model="form.excerpt" 
                        rows="3" 
                        placeholder="Ringkasan singkat 1-2 kalimat yang akan tampil di kartu depan berita publik."
                        class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#38BDF8]"
                    ></textarea>
                    <InputError :message="form.errors.excerpt" />
                </div>

            </div>

            <!-- Right Settings Column (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- Category Selector -->
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 space-y-3 shadow-xs">
                    <label class="text-sm font-bold text-slate-900 dark:text-white block">Kategori Berita <span class="text-rose-500">*</span></label>
                    <select 
                        v-model="form.category" 
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-900 dark:text-white font-semibold focus:outline-none focus:border-[#38BDF8]"
                    >
                        <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                    </select>
                    <InputError :message="form.errors.category" />
                </div>

                <!-- Tags Input -->
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 space-y-3 shadow-xs">
                    <label class="text-sm font-bold text-slate-900 dark:text-white block">Tags (Pisahkan dengan koma)</label>
                    <input 
                        v-model="form.tags" 
                        type="text" 
                        placeholder="Contoh: KKN, UMKM, Digitalisasi, Desa" 
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2.5 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#38BDF8]"
                    />
                    <p class="text-[11px] text-slate-400">Tag membantu artikel Anda mudah ditemukan pembaca.</p>
                    <InputError :message="form.errors.tags" />
                </div>

                <!-- Cover Image Upload Box -->
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 space-y-3 shadow-xs">
                    <label class="text-sm font-bold text-slate-900 dark:text-white block">Gambar Sampul (Cover Image)</label>
                    
                    <div class="relative group aspect-video w-full rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 border-2 border-dashed border-slate-300 dark:border-slate-700 flex flex-col items-center justify-center text-center p-4">
                        <img v-if="previewCoverUrl" :src="previewCoverUrl" alt="Cover Preview" class="w-full h-full object-cover absolute inset-0" />
                        
                        <div class="relative z-10 space-y-2 bg-slate-900/60 p-3 rounded-xl backdrop-blur-xs text-white">
                            <ImageIcon class="w-8 h-8 mx-auto text-[#38BDF8]" />
                            <p class="text-xs font-bold">Pilih Gambar Sampul</p>
                            <p class="text-[10px] text-slate-300">Format PNG, JPG, WebP (Maks 5MB)</p>
                        </div>

                        <input type="file" @change="handleFileSelect" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-20" />
                    </div>
                    <InputError :message="form.errors.cover_image" />
                </div>

                <!-- Publication Toggle Switch -->
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 space-y-3 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-bold text-slate-900 dark:text-white block">Publikasikan Berita</label>
                            <p class="text-xs text-slate-400">Tampilkan di halaman berita publik SuperPosko.</p>
                        </div>
                        <input 
                            v-model="form.is_published" 
                            type="checkbox" 
                            class="w-5 h-5 accent-[#38BDF8] rounded cursor-pointer" 
                        />
                    </div>
                </div>

            </div>

        </div>

    </div>
</template>

<style>
/* Real visual WYSIWYG editor styling - zero raw tags visible */
.visual-wysiwyg-editor h2 {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0f172a;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
}
.visual-wysiwyg-editor h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0284c7;
    margin-top: 1rem;
    margin-bottom: 0.5rem;
}
.visual-wysiwyg-editor p {
    margin-bottom: 0.75rem;
    line-height: 1.6;
}
.visual-wysiwyg-editor ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-bottom: 0.75rem;
}
.visual-wysiwyg-editor ol {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin-bottom: 0.75rem;
}
.visual-wysiwyg-editor blockquote {
    border-left: 4px solid #38bdf8;
    padding-left: 1rem;
    font-style: italic;
    color: #475569;
    margin: 1rem 0;
    background-color: #f8fafc;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
}
.visual-wysiwyg-editor b, .visual-wysiwyg-editor strong {
    font-weight: 700;
}
.visual-wysiwyg-editor i, .visual-wysiwyg-editor em {
    font-style: italic;
}
</style>
