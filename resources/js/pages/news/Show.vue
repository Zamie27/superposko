<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import PublicFooter from '@/components/PublicFooter.vue';
import { Calendar, Clock, Eye, Share2, ArrowLeft, ArrowRight, Tag, Bookmark, Check } from '@lucide/vue';
import { useToast } from '@/composables/useToast';

const props = defineProps<{
    article: {
        id: number;
        title: string;
        slug: string;
        category: string;
        tags: string[];
        excerpt: string;
        content: string;
        cover_image_url?: string;
        reading_time_minutes: number;
        views_count: number;
        published_at: string;
        author_display_name: string;
        posko_logo_url: string;
    };
    tableOfContents: Array<{
        id: string;
        text: string;
        level: number;
    }>;
    relatedArticles: any[];
}>();

const toast = useToast();
const copied = ref(false);
const activeHeadingId = ref('');
const isMenuOpen = ref(false);

const scrollToHeading = (headingId: string) => {
    activeHeadingId.value = headingId;
    const el = document.getElementById(headingId);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

const copyArticleLink = () => {
    navigator.clipboard.writeText(window.location.href);
    copied.value = true;
    toast.success('Tautan artikel berhasil disalin!');
    setTimeout(() => { copied.value = false; }, 2500);
};

onMounted(() => {
    // Observer for active TOC heading highlight on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                activeHeadingId.value = entry.target.id;
            }
        });
    }, { rootMargin: '-80px 0px -60% 0px' });

    props.tableOfContents.forEach((item) => {
        const el = document.getElementById(item.id);
        if (el) observer.observe(el);
    });
});
</script>

<template>
    <Head :title="`${article.title} - SuperPosko News`" />

    <div class="min-h-screen bg-[#0B0F19] text-slate-100 font-sans flex flex-col justify-between selection:bg-pink-500 selection:text-white">
        
        <!-- Public Navbar -->
        <header class="sticky top-0 z-50 backdrop-blur-xl bg-[#0B0F19]/80 border-b border-slate-800/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <Link href="/" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-sky-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-sky-500/20 group-hover:scale-105 transition-transform duration-300">
                        <img src="/logo_superposko.png" alt="SuperPosko" class="w-6 h-6 object-contain filter brightness-0 invert" />
                    </div>
                    <span class="font-extrabold text-xl tracking-tight bg-gradient-to-r from-white via-slate-200 to-sky-400 bg-clip-text text-transparent">SuperPosko</span>
                </Link>

                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300">
                    <Link href="/" class="hover:text-white transition-colors">Beranda</Link>
                    <Link href="/event" class="hover:text-white transition-colors">Event Testing</Link>
                    <Link href="/berita" class="text-sky-400 font-bold flex items-center gap-1.5">
                        <span>Berita</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                    </Link>
                    <Link href="/panduan" class="hover:text-white transition-colors">Panduan</Link>
                    <Link href="/about" class="hover:text-white transition-colors">Tentang Kami</Link>
                </nav>

                <div class="hidden md:flex items-center gap-3">
                    <Link href="/login" class="px-4 py-2 text-sm font-semibold text-slate-300 hover:text-white transition-colors">Masuk</Link>
                    <Link href="/register" class="px-4 py-2 text-sm font-semibold rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 text-white shadow-lg shadow-sky-500/25 hover:opacity-95 transition-all">Daftar Posko</Link>
                </div>

                <!-- Mobile Hamburger Button -->
                <button @click="isMenuOpen = !isMenuOpen" class="md:hidden p-2 text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="!isMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Drawer -->
            <div v-if="isMenuOpen" class="md:hidden border-b border-slate-800 bg-[#0F172A] px-4 pt-2 pb-6 space-y-3">
                <Link href="/" @click="isMenuOpen = false" class="block py-2 text-slate-300 hover:text-white font-medium">Beranda</Link>
                <Link href="/event" @click="isMenuOpen = false" class="block py-2 text-slate-300 hover:text-white font-medium">Event Testing</Link>
                <Link href="/berita" @click="isMenuOpen = false" class="block py-2 text-sky-400 font-bold">Berita & Artikel</Link>
                <Link href="/panduan" @click="isMenuOpen = false" class="block py-2 text-slate-300 hover:text-white font-medium">Panduan</Link>
                <Link href="/about" @click="isMenuOpen = false" class="block py-2 text-slate-300 hover:text-white font-medium">Tentang Kami</Link>
                <div class="pt-4 flex flex-col gap-2">
                    <Link href="/login" class="w-full text-center py-2.5 text-sm font-semibold text-slate-300 bg-slate-800 rounded-xl">Masuk</Link>
                    <Link href="/register" class="w-full text-center py-2.5 text-sm font-semibold bg-gradient-to-r from-sky-500 to-indigo-600 text-white rounded-xl">Daftar Posko</Link>
                </div>
            </div>
        </header>

        <!-- Main Article Container -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex-grow space-y-8">
            
            <!-- Breadcrumb Navigation -->
            <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-400">
                <Link href="/berita" class="hover:text-sky-400 transition-colors flex items-center gap-1">
                    <ArrowLeft class="w-4 h-4" />
                    <span>Kembali ke Berita</span>
                </Link>
                <span>/</span>
                <span class="text-pink-400 font-bold">{{ article.category }}</span>
            </div>

            <!-- Header Title & Badges -->
            <div class="space-y-4 max-w-4xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 text-xs font-bold rounded-lg bg-pink-500/20 text-pink-400 border border-pink-500/30">
                        {{ article.category }}
                    </span>
                    <span v-for="tag in article.tags" :key="tag" class="px-2.5 py-0.5 text-xs font-semibold rounded-md bg-slate-800 text-slate-300">
                        #{{ tag }}
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white leading-tight tracking-tight">
                    {{ article.title }}
                </h1>

                <!-- Author & Publishing Meta -->
                <div class="pt-4 border-t border-slate-800/80 flex flex-wrap items-center justify-between gap-4">
                    <!-- Author Info with Posko Logo Avatar -->
                    <div class="flex items-center gap-3">
                        <img 
                            :src="article.posko_logo_url" 
                            alt="Logo Posko KKN" 
                            class="w-11 h-11 rounded-full object-cover border-2 border-sky-500 shadow-md shadow-sky-500/20" 
                        />
                        <div>
                            <div class="font-bold text-slate-100 text-sm sm:text-base">
                                {{ article.author_display_name }}
                            </div>
                            <div class="text-xs text-slate-400 flex items-center gap-3 mt-0.5">
                                <span class="flex items-center gap-1">
                                    <Calendar class="w-3.5 h-3.5 text-slate-500" />
                                    {{ article.published_at }}
                                </span>
                                <span>•</span>
                                <span class="flex items-center gap-1">
                                    <Clock class="w-3.5 h-3.5 text-slate-500" />
                                    {{ article.reading_time_minutes }} menit baca
                                </span>
                                <span>•</span>
                                <span class="flex items-center gap-1">
                                    <Eye class="w-3.5 h-3.5 text-slate-500" />
                                    {{ article.views_count }} views
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Share Button -->
                    <button 
                        @click="copyArticleLink" 
                        class="flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all border border-slate-700"
                    >
                        <Check v-if="copied" class="w-4 h-4 text-emerald-400" />
                        <Share2 v-else class="w-4 h-4 text-sky-400" />
                        <span>{{ copied ? 'Tautan Disalin!' : 'Bagikan Berita' }}</span>
                    </button>
                </div>
            </div>

            <!-- Cover Image Feature -->
            <div v-if="article.cover_image_url" class="rounded-3xl overflow-hidden bg-slate-900 border border-slate-800 shadow-2xl max-h-[500px]">
                <img :src="article.cover_image_url" :alt="article.title" class="w-full h-full object-cover max-h-[500px]" />
            </div>

            <!-- Main Layout Grid: Left Sticky TOC (4 cols) | Content (8 cols) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 pt-4">
                
                <!-- Left Column (4 cols): Sticky Table of Contents -->
                <div class="lg:col-span-4 space-y-6">
                    <div v-if="tableOfContents.length > 0" class="sticky top-24 rounded-2xl bg-[#131927] border border-slate-800 p-6 space-y-4 shadow-xl">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 border-b border-slate-800 pb-3">
                            <Bookmark class="w-4 h-4 text-sky-400" />
                            <span>Daftar Isi Artikel</span>
                        </h3>
                        <nav class="space-y-1.5 max-h-[60vh] overflow-y-auto pr-1">
                            <button 
                                v-for="item in tableOfContents" 
                                :key="item.id" 
                                @click="scrollToHeading(item.id)" 
                                :class="[
                                    activeHeadingId === item.id 
                                        ? 'bg-sky-500/20 text-sky-400 font-bold border-l-2 border-sky-400 pl-3' 
                                        : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 pl-2',
                                    item.level === 3 ? 'ml-3 text-xs' : 'text-sm font-medium'
                                ]"
                                class="w-full py-2 rounded-r-lg text-left transition-all block truncate"
                            >
                                {{ item.text }}
                            </button>
                        </nav>
                    </div>

                    <!-- Quick Support Posko Widget -->
                    <div class="rounded-2xl bg-gradient-to-br from-indigo-900/40 to-slate-900 border border-indigo-500/30 p-6 space-y-3">
                        <h4 class="text-sm font-bold text-white">Tentang SuperPosko</h4>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Platform kolaborasi digital posko KKN untuk keterbukaan informasi, presensi harian, manajemen kas, dan publikasi kegiatan desa.
                        </p>
                        <Link href="/register" class="inline-block px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold transition-colors">
                            Buat Posko Sekarang &rarr;
                        </Link>
                    </div>
                </div>

                <!-- Right Main Column (8 cols): Article Content -->
                <div class="lg:col-span-8 space-y-8">
                    
                    <!-- Excerpt Box -->
                    <div v-if="article.excerpt" class="p-5 rounded-2xl bg-slate-900/90 border-l-4 border-pink-500 border-y border-r border-slate-800 text-slate-300 italic text-sm sm:text-base leading-relaxed">
                        "{{ article.excerpt }}"
                    </div>

                    <!-- HTML Rich Content Container -->
                    <div 
                        class="article-content text-slate-200 text-base sm:text-lg leading-relaxed space-y-6"
                        v-html="article.content"
                    ></div>

                    <!-- Article Tags Footer -->
                    <div v-if="article.tags && article.tags.length > 0" class="pt-6 border-t border-slate-800 flex items-center gap-2 flex-wrap">
                        <Tag class="w-4 h-4 text-purple-400" />
                        <span class="text-xs font-bold text-slate-400">Tags Artikel:</span>
                        <span v-for="tag in article.tags" :key="tag" class="px-3 py-1 text-xs font-semibold rounded-lg bg-slate-800 text-slate-300 hover:text-white transition-colors cursor-pointer">
                            #{{ tag }}
                        </span>
                    </div>

                    <!-- Related Articles Slider / Grid -->
                    <div v-if="relatedArticles.length > 0" class="pt-10 border-t border-slate-800 space-y-6">
                        <h3 class="text-xl font-bold text-white">Berita Posko Terkait</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <Link 
                                v-for="rel in relatedArticles" 
                                :key="rel.id" 
                                :href="`/berita/${rel.slug}`" 
                                class="group rounded-2xl bg-[#131927] border border-slate-800 p-3 space-y-2 hover:border-slate-700 transition-colors"
                            >
                                <div class="aspect-video w-full rounded-xl overflow-hidden bg-slate-900">
                                    <img :src="rel.cover_image_url || '/logo_superposko.png'" :alt="rel.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                                </div>
                                <h4 class="text-xs font-bold text-white group-hover:text-sky-400 line-clamp-2 leading-snug">
                                    {{ rel.title }}
                                </h4>
                                <span class="text-[11px] text-slate-500 block">{{ rel.published_at }}</span>
                            </Link>
                        </div>
                    </div>

                </div>

            </div>

        </main>

        <!-- Footer -->
        <PublicFooter />

    </div>
</template>

<style>
/* Styling for rendered HTML article content */
.article-content h2 {
    font-size: 1.75rem;
    font-weight: 800;
    color: #ffffff;
    margin-top: 2rem;
    margin-bottom: 1rem;
    scroll-margin-top: 100px;
}
.article-content h3 {
    font-size: 1.35rem;
    font-weight: 700;
    color: #38bdf8;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    scroll-margin-top: 100px;
}
.article-content p {
    margin-bottom: 1.25rem;
    color: #cbd5e1;
}
.article-content ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-bottom: 1.25rem;
    color: #cbd5e1;
}
.article-content ol {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin-bottom: 1.25rem;
    color: #cbd5e1;
}
.article-content blockquote {
    border-left: 4px solid #ec4899;
    padding-left: 1rem;
    font-style: italic;
    color: #94a3b8;
    margin: 1.5rem 0;
}
.article-content a {
    color: #38bdf8;
    text-decoration: underline;
}
.article-content img {
    border-radius: 1rem;
    margin: 1.5rem 0;
    max-width: 100%;
}
</style>
