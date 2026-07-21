<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { ArrowLeft, Save, Upload, Image as ImageIcon, Heading1, Heading2, Bold, Italic, List, Link as LinkIcon, Quote, Sparkles } from '@lucide/vue';
import { useToast } from '@/composables/useToast';

const props = defineProps<{
    article?: any;
    categories: string[];
}>();

const toast = useToast();

const isEditing = computed(() => !!props.article?.id);

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
const editorRef = ref<HTMLTextAreaElement | null>(null);

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        form.cover_image = file;
        previewCoverUrl.value = URL.createObjectURL(file);
    }
};

const insertTag = (openTag: string, closeTag: string = '') => {
    const textarea = editorRef.value;
    if (!textarea) return;

    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = form.content.substring(start, end);
    const replacement = `${openTag}${selectedText}${closeTag}`;

    form.content = form.content.substring(0, start) + replacement + form.content.substring(end);
};

const submitForm = () => {
    // Process tags into array
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
</script>

<template>
    <Head :title="isEditing ? 'Edit Artikel Berita' : 'Tulis Artikel Berita Baru'" />

    <AppLayout>
        <div class="relative flex flex-col gap-6 rounded-xl p-4 sm:p-6 min-h-[400px] w-full max-w-full">
            
            <!-- Page Top Bar -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="space-y-1">
                    <Link href="/management/news" class="text-xs font-bold text-slate-500 hover:text-sky-500 flex items-center gap-1">
                        <ArrowLeft class="w-3.5 h-3.5" />
                        <span>Kembali ke Daftar Berita</span>
                    </Link>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                        <span>{{ isEditing ? 'Edit Artikel Berita Posko' : 'Tulis Artikel Berita Baru' }}</span>
                        <Sparkles class="w-5 h-5 text-pink-500" />
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <Link href="/management/news">
                        <Button variant="outline" class="cursor-pointer">Batal</Button>
                    </Link>
                    <Button 
                        @click="submitForm" 
                        :disabled="form.processing"
                        class="bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-600 hover:to-indigo-700 text-white font-bold shadow-md cursor-pointer"
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
                        <label class="text-sm font-bold text-slate-900 dark:text-white">Judul Berita / Artikel <span class="text-rose-500">*</span></label>
                        <input 
                            v-model="form.title" 
                            type="text" 
                            placeholder="Contoh: Sosialisasi Digitalisasi UMKM Desa Tebet oleh Posko 18" 
                            class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl px-4 py-3 text-base text-slate-900 dark:text-white font-bold placeholder-slate-400 focus:outline-none focus:border-sky-500"
                        />
                        <InputError :message="form.errors.title" />
                    </div>

                    <!-- Content HTML / Rich Text Editor -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-bold text-slate-900 dark:text-white">Isi Konten Artikel <span class="text-rose-500">*</span></label>
                            <span class="text-xs text-slate-500">Mendukung format HTML / Heading</span>
                        </div>

                        <!-- Editor Toolbar Helper Buttons -->
                        <div class="flex flex-wrap items-center gap-1.5 p-2 rounded-t-xl bg-slate-100 dark:bg-slate-800 border border-b-0 border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200">
                            <button @click="insertTag('<h2>', '</h2>')" type="button" class="p-1.5 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold flex items-center gap-1" title="Heading 2">
                                <Heading1 class="w-4 h-4 text-sky-500" />
                                <span>H2</span>
                            </button>
                            <button @click="insertTag('<h3>', '</h3>')" type="button" class="p-1.5 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold flex items-center gap-1" title="Heading 3">
                                <Heading2 class="w-4 h-4 text-sky-500" />
                                <span>H3</span>
                            </button>
                            <div class="h-4 w-px bg-slate-300 dark:bg-slate-700 mx-1"></div>
                            <button @click="insertTag('<b>', '</b>')" type="button" class="p-1.5 rounded hover:bg-slate-200 dark:hover:bg-slate-700" title="Tebal">
                                <Bold class="w-4 h-4" />
                            </button>
                            <button @click="insertTag('<i>', '</i>')" type="button" class="p-1.5 rounded hover:bg-slate-200 dark:hover:bg-slate-700" title="Miring">
                                <Italic class="w-4 h-4" />
                            </button>
                            <button @click="insertTag('<blockquote>', '</blockquote>')" type="button" class="p-1.5 rounded hover:bg-slate-200 dark:hover:bg-slate-700" title="Kutipan">
                                <Quote class="w-4 h-4 text-pink-500" />
                            </button>
                            <button @click="insertTag('<ul>\n  <li>', '</li>\n</ul>')" type="button" class="p-1.5 rounded hover:bg-slate-200 dark:hover:bg-slate-700" title="Daftar List">
                                <List class="w-4 h-4 text-indigo-500" />
                            </button>
                            <button @click="insertTag('<p>', '</p>')" type="button" class="p-1.5 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold px-2">
                                Paragraf
                            </button>
                        </div>

                        <!-- Main Editor Textarea -->
                        <textarea 
                            ref="editorRef"
                            v-model="form.content" 
                            rows="16" 
                            placeholder="Tulis artikel berita kegiatan Anda di sini... Gunakan tombol toolbar H2/H3 di atas untuk membuat bab/sub-bagian artikel."
                            class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-b-xl px-4 py-3 text-sm text-slate-900 dark:text-white font-mono leading-relaxed placeholder-slate-400 focus:outline-none focus:border-sky-500"
                        ></textarea>
                        <InputError :message="form.errors.content" />
                    </div>

                    <!-- Excerpt Textarea -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-900 dark:text-white">Ringkasan Singkat (Excerpt)</label>
                        <textarea 
                            v-model="form.excerpt" 
                            rows="3" 
                            placeholder="Ringkasan singkat 1-2 kalimat yang akan tampil di kartu depan berita."
                            class="w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-sky-500"
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
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white font-semibold focus:outline-none focus:border-sky-500"
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
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-sky-500"
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
                                <ImageIcon class="w-8 h-8 mx-auto text-sky-400" />
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
                                class="w-5 h-5 accent-sky-500 rounded cursor-pointer" 
                            />
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </AppLayout>
</template>
