<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import PublicFooter from '@/components/PublicFooter.vue';
import { Calendar, Clock, Eye, Share2, ArrowLeft, Tag, Bookmark, Check } from '@lucide/vue';
import { useToast } from '@/composables/useToast';
import { login, register } from '@/routes';

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
    footerAbout?: string;
    footerEmail?: string;
    footerPhone?: string;
    footerCopyright?: string;
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
    <Head :title="`${article.title} - SuperPosko`" />

    <div class="min-h-screen bg-[#F4F7F7] text-slate-900 font-sans antialiased selection:bg-[#38BDF8] selection:text-slate-950 flex flex-col justify-between">
        
        <!-- Standard Unified Public Navbar Header -->
        <header class="sticky top-0 z-50 w-full border-b border-slate-200/50 bg-white shadow-md">
            <div class="mx-auto flex max-w-7xl h-16 items-center justify-between px-6 lg:px-8">
                <!-- Logo -->
                <Link href="/" class="flex items-center group">
                    <img src="/logo_superposko.png" alt="SuperPosko" class="h-9 w-auto transition-transform duration-300 group-hover:scale-105" />
                </Link>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                    <Link href="/" class="hover:text-[#38BDF8] transition-colors">Home</Link>
                    <Link href="/event" class="hover:text-[#38BDF8] transition-colors">Event</Link>
                    <Link href="/berita" class="text-[#38BDF8] font-bold transition-colors">Berita</Link>
                    <Link href="/panduan" class="hover:text-[#38BDF8] transition-colors">Panduan</Link>
                    <a href="/#fitur" class="hover:text-[#38BDF8] transition-colors">Fitur</a>
                    <a href="/#pricing" class="hover:text-[#38BDF8] transition-colors">Harga</a>
                    <a href="/#faq" class="hover:text-[#38BDF8] transition-colors">FAQ</a>
                </nav>

                <!-- Actions -->
                <div class="hidden md:flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        href="/dashboard"
                        class="rounded-lg bg-[#38BDF8] hover:bg-[#38BDF8]/90 px-4.5 py-2 text-sm font-semibold text-white transition duration-200"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="text-sm font-semibold text-slate-700 hover:text-[#38BDF8] transition-colors"
                        >
                            Masuk
                        </Link>
                        <Link
                            :href="register()"
                            class="rounded-lg bg-[#38BDF8] hover:bg-[#38BDF8]/90 px-4.5 py-2 text-sm font-semibold text-white transition duration-200"
                        >
                            Daftar Posko
                        </Link>
                    </template>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center gap-2 md:hidden">
                    <button 
                        @click="isMenuOpen = !isMenuOpen" 
                        type="button" 
                        class="rounded-lg p-2 text-slate-600 hover:bg-slate-200/50"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="!isMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Dropdown Nav -->
            <div v-if="isMenuOpen" class="border-b border-slate-200/50 bg-[#F4F7F7] px-6 py-4 md:hidden">
                <nav class="flex flex-col gap-4 text-sm font-semibold text-slate-600">
                    <Link href="/" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">Home</Link>
                    <Link href="/event" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">Event</Link>
                    <Link href="/berita" @click="isMenuOpen = false" class="text-[#38BDF8] font-bold">Berita</Link>
                    <Link href="/panduan" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">Panduan</Link>
                    <a href="/#fitur" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">Fitur</a>
                    <a href="/#pricing" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">Harga</a>
                    <a href="/#faq" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">FAQ</a>
                    <div class="h-px bg-slate-200/50 my-2"></div>
                    <Link
                        v-if="$page.props.auth.user"
                        href="/dashboard"
                        @click="isMenuOpen = false"
                        class="text-center rounded-lg bg-[#38BDF8] py-2.5 text-sm font-bold text-white"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            @click="isMenuOpen = false"
                            class="text-center py-2 text-sm font-semibold text-slate-700"
                        >
                            Masuk
                        </Link>
                        <Link
                            :href="register()"
                            @click="isMenuOpen = false"
                            class="text-center rounded-lg bg-[#38BDF8] py-2.5 text-sm font-bold text-white"
                        >
                            Daftar Posko
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Main Article Container (Light Mode) -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex-grow space-y-8">
            
            <!-- Breadcrumb Navigation -->
            <div class="flex items-center gap-2 text-xs sm:text-sm text-slate-500">
                <Link href="/berita" class="hover:text-[#38BDF8] transition-colors flex items-center gap-1 font-semibold">
                    <ArrowLeft class="w-4 h-4" />
                    <span>Kembali ke Berita</span>
                </Link>
                <span>/</span>
                <span class="text-sky-600 font-bold">{{ article.category }}</span>
            </div>

            <!-- Header Title & Badges -->
            <div class="space-y-4 max-w-4xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 text-xs font-bold rounded-md bg-sky-50 text-sky-600 border border-sky-200">
                        {{ article.category }}
                    </span>
                    <span v-for="tag in article.tags" :key="tag" class="px-2.5 py-0.5 text-xs font-semibold rounded-md bg-slate-200/80 text-slate-700">
                        #{{ tag }}
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 leading-tight tracking-tight">
                    {{ article.title }}
                </h1>

                <!-- Author & Publishing Meta -->
                <div class="pt-4 border-t border-slate-200/80 flex flex-wrap items-center justify-between gap-4">
                    <!-- Author Info with Posko Logo Avatar -->
                    <div class="flex items-center gap-3">
                        <img 
                            :src="article.posko_logo_url" 
                            alt="Logo Posko KKN" 
                            class="w-11 h-11 rounded-full object-cover border-2 border-[#38BDF8] shadow-xs" 
                        />
                        <div>
                            <div class="font-bold text-slate-900 text-sm sm:text-base">
                                {{ article.author_display_name }}
                            </div>
                            <div class="text-xs text-slate-500 flex items-center gap-3 mt-0.5">
                                <span class="flex items-center gap-1">
                                    <Calendar class="w-3.5 h-3.5 text-slate-400" />
                                    {{ article.published_at }}
                                </span>
                                <span>•</span>
                                <span class="flex items-center gap-1">
                                    <Clock class="w-3.5 h-3.5 text-slate-400" />
                                    {{ article.reading_time_minutes }} menit baca
                                </span>
                                <span>•</span>
                                <span class="flex items-center gap-1">
                                    <Eye class="w-3.5 h-3.5 text-slate-400" />
                                    {{ article.views_count }} views
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Share Button -->
                    <button 
                        @click="copyArticleLink" 
                        class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all border border-slate-200 shadow-xs cursor-pointer"
                    >
                        <Check v-if="copied" class="w-4 h-4 text-emerald-500" />
                        <Share2 v-else class="w-4 h-4 text-[#38BDF8]" />
                        <span>{{ copied ? 'Tautan Disalin!' : 'Bagikan Berita' }}</span>
                    </button>
                </div>
            </div>

            <!-- Cover Image Feature -->
            <div v-if="article.cover_image_url" class="rounded-2xl overflow-hidden bg-slate-100 border border-slate-200/80 shadow-md max-h-[500px]">
                <img :src="article.cover_image_url" :alt="article.title" class="w-full h-full object-cover max-h-[500px]" />
            </div>

            <!-- Main Layout Grid: Left Sticky TOC (4 cols) | Content (8 cols) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 pt-4">
                
                <!-- Left Column (4 cols): Sticky Table of Contents -->
                <div class="lg:col-span-4 space-y-6">
                    <div v-if="tableOfContents.length > 0" class="sticky top-24 rounded-2xl bg-white border border-slate-200/80 p-6 space-y-4 shadow-sm">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                            <Bookmark class="w-4 h-4 text-[#38BDF8]" />
                            <span>Daftar Isi Artikel</span>
                        </h3>
                        <nav class="space-y-1.5 max-h-[60vh] overflow-y-auto pr-1">
                            <button 
                                v-for="item in tableOfContents" 
                                :key="item.id" 
                                @click="scrollToHeading(item.id)" 
                                :class="[
                                    activeHeadingId === item.id 
                                        ? 'bg-sky-50 text-sky-600 font-bold border-l-2 border-[#38BDF8] pl-3' 
                                        : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 pl-2',
                                    item.level === 3 ? 'ml-3 text-xs' : 'text-sm font-medium'
                                ]"
                                class="w-full py-2 rounded-r-lg text-left transition-all block truncate cursor-pointer"
                            >
                                {{ item.text }}
                            </button>
                        </nav>
                    </div>

                    <!-- Quick Support Posko Widget -->
                    <div class="rounded-2xl bg-white border border-slate-200/80 p-6 space-y-3 shadow-sm">
                        <h4 class="text-sm font-bold text-slate-900">Tentang SuperPosko</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Platform kolaborasi digital posko KKN untuk keterbukaan informasi, presensi harian, manajemen kas, dan publikasi kegiatan desa.
                        </p>
                        <Link href="/register" class="inline-block px-4 py-2 rounded-xl bg-[#38BDF8] hover:bg-sky-500 text-white text-xs font-bold transition-colors shadow-xs">
                            Buat Posko Sekarang &rarr;
                        </Link>
                    </div>
                </div>

                <!-- Right Main Column (8 cols): Article Content -->
                <div class="lg:col-span-8 space-y-8">
                    
                    <!-- Excerpt Box -->
                    <div v-if="article.excerpt" class="p-5 rounded-2xl bg-white border-l-4 border-[#38BDF8] border-y border-r border-slate-200/80 text-slate-700 italic text-sm sm:text-base leading-relaxed shadow-xs">
                        "{{ article.excerpt }}"
                    </div>

                    <!-- HTML Rich Content Container (Light Mode Typography) -->
                    <div 
                        class="article-content bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-sm text-slate-800 text-base sm:text-lg leading-relaxed space-y-6"
                        v-html="article.content"
                    ></div>

                    <!-- Article Tags Footer -->
                    <div v-if="article.tags && article.tags.length > 0" class="pt-4 border-t border-slate-200 flex items-center gap-2 flex-wrap">
                        <Tag class="w-4 h-4 text-sky-500" />
                        <span class="text-xs font-bold text-slate-500">Tags Artikel:</span>
                        <span v-for="tag in article.tags" :key="tag" class="px-3 py-1 text-xs font-semibold rounded-lg bg-white border border-slate-200 text-slate-700 hover:border-sky-300 transition-colors cursor-pointer">
                            #{{ tag }}
                        </span>
                    </div>

                    <!-- Related Articles Slider / Grid -->
                    <div v-if="relatedArticles.length > 0" class="pt-8 border-t border-slate-200 space-y-6">
                        <h3 class="text-xl font-bold text-slate-900">Berita Posko Terkait</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <Link 
                                v-for="rel in relatedArticles" 
                                :key="rel.id" 
                                :href="`/berita/${rel.slug}`" 
                                class="group rounded-2xl bg-white border border-slate-200/80 p-3 space-y-2 hover:border-sky-300 transition-colors shadow-xs"
                            >
                                <div class="aspect-video w-full rounded-xl overflow-hidden bg-slate-100">
                                    <img :src="rel.cover_image_url || '/logo_superposko.png'" :alt="rel.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                                </div>
                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-[#38BDF8] line-clamp-2 leading-snug">
                                    {{ rel.title }}
                                </h4>
                                <span class="text-[11px] text-slate-400 block">{{ rel.published_at }}</span>
                            </Link>
                        </div>
                    </div>

                </div>

            </div>

        </main>

        <!-- Footer -->
        <PublicFooter 
            :footerAbout="footerAbout"
            :footerEmail="footerEmail"
            :footerPhone="footerPhone"
            :footerCopyright="footerCopyright"
        />

    </div>
</template>

<style>
/* Light Mode Typography styling for rendered HTML article content */
.article-content h2 {
    font-size: 1.65rem;
    font-weight: 800;
    color: #0f172a;
    margin-top: 2rem;
    margin-bottom: 1rem;
    scroll-margin-top: 100px;
}
.article-content h3 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #0284c7;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    scroll-margin-top: 100px;
}
.article-content p {
    margin-bottom: 1.25rem;
    color: #334155;
}
.article-content ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-bottom: 1.25rem;
    color: #334155;
}
.article-content ol {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin-bottom: 1.25rem;
    color: #334155;
}
.article-content blockquote {
    border-left: 4px solid #38bdf8;
    padding-left: 1rem;
    font-style: italic;
    color: #475569;
    margin: 1.5rem 0;
}
.article-content a {
    color: #0284c7;
    text-decoration: underline;
}
.article-content img {
    border-radius: 1rem;
    margin: 1.5rem 0;
    max-width: 100%;
}
</style>
