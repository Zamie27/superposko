<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import PublicFooter from '@/components/PublicFooter.vue';
import { Search, Calendar, Clock, Tag, ArrowRight, BookOpen, Layers, Sparkles } from '@lucide/vue';
import { login, register } from '@/routes';

const props = defineProps<{
    featuredArticle?: any;
    articles: any[];
    categories: any[];
    recentArticles: any[];
    popularTags: any[];
    filters: {
        q?: string;
        category?: string;
        tag?: string;
    };
    footerAbout?: string;
    footerEmail?: string;
    footerPhone?: string;
    footerCopyright?: string;
}>();

const searchQuery = ref(props.filters.q || '');
const selectedCategory = ref(props.filters.category || 'Semua');
const isMenuOpen = ref(false);

const handleSearch = () => {
    router.get('/berita', {
        q: searchQuery.value || undefined,
        category: selectedCategory.value !== 'Semua' ? selectedCategory.value : undefined,
        tag: props.filters.tag || undefined,
    }, { preserveState: true });
};

const filterByCategory = (catName: string) => {
    selectedCategory.value = catName;
    router.get('/berita', {
        q: searchQuery.value || undefined,
        category: catName !== 'Semua' ? catName : undefined,
        tag: props.filters.tag || undefined,
    });
};

const filterByTag = (tagName: string) => {
    router.get('/berita', {
        q: searchQuery.value || undefined,
        category: selectedCategory.value !== 'Semua' ? selectedCategory.value : undefined,
        tag: tagName,
    });
};

const resetFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = 'Semua';
    router.get('/berita');
};
</script>

<template>
    <Head title="Blog & Berita Posko KKN - SuperPosko" />

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

        <!-- Main Content Area (Light Mode) -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex-grow">
            
            <!-- Page Title Header -->
            <div class="mb-10 text-center max-w-3xl mx-auto">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#38BDF8]/10 px-3.5 py-1.5 text-xs font-bold text-sky-600">
                    📰 Portal Berita & Informasi Posko KKN
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mt-4 leading-tight">
                    Blog & Berita Posko KKN
                </h1>
                <p class="text-slate-600 text-base sm:text-lg mt-3 leading-relaxed">
                    Kabar kegiatan harian, cerita edukasi, dan dokumentasi pengabdian masyarakat dari posko KKN di seluruh Indonesia.
                </p>
            </div>

            <!-- Active Tag / Search Filter Info Bar -->
            <div v-if="filters.q || filters.category || filters.tag" class="mb-6 flex flex-wrap items-center justify-between gap-3 p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                <div class="flex items-center gap-2 text-sm text-slate-700">
                    <span>Menampilkan artikel untuk:</span>
                    <span v-if="filters.q" class="font-bold text-sky-600">Pencarian "{{ filters.q }}"</span>
                    <span v-if="filters.category" class="font-bold text-sky-600">Kategori "{{ filters.category }}"</span>
                    <span v-if="filters.tag" class="font-bold text-indigo-600">Tag "#{{ filters.tag }}"</span>
                </div>
                <button @click="resetFilters" class="text-xs font-semibold text-slate-500 hover:text-slate-900 underline">Bersihkan Filter</button>
            </div>

            <!-- Content Grid Layout: Main Left (Featured & Grid) | Right Sidebar -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left Column (8 cols) -->
                <div class="lg:col-span-8 space-y-8">
                    
                    <!-- Featured Hero Article Card (Light Mode - Entirely Clickable) -->
                    <Link 
                        v-if="featuredArticle" 
                        :href="`/berita/${featuredArticle.slug}`"
                        class="block group relative rounded-2xl overflow-hidden bg-white border border-slate-200/80 shadow-md hover:shadow-xl transition-all duration-300 cursor-pointer"
                    >
                        <!-- Cover Image -->
                        <div class="aspect-video w-full overflow-hidden bg-slate-100 relative">
                            <img 
                                :src="featuredArticle.cover_image_url || '/logo_superposko.png'" 
                                :alt="featuredArticle.title" 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                            />
                        </div>

                        <!-- Content Body -->
                        <div class="p-6 sm:p-8 space-y-4">
                            <!-- Badges -->
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-[#38BDF8] text-white shadow-xs">Featured</span>
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-sky-50 text-sky-600 border border-sky-200">{{ featuredArticle.category }}</span>
                            </div>

                            <!-- Title -->
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 group-hover:text-[#38BDF8] transition-colors leading-tight">
                                {{ featuredArticle.title }}
                            </h2>

                            <!-- Meta Info with Total Views -->
                            <div class="flex flex-wrap items-center gap-4 text-xs sm:text-sm text-slate-500">
                                <div class="flex items-center gap-2 font-bold text-slate-800">
                                    <img :src="featuredArticle.posko_logo_url" alt="Logo Posko" class="w-5 h-5 rounded-full object-cover border border-slate-200" />
                                    <span>{{ featuredArticle.author_display_name }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <Calendar class="w-4 h-4 text-slate-400" />
                                    <span>{{ featuredArticle.published_at }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <Clock class="w-4 h-4 text-slate-400" />
                                    <span>{{ featuredArticle.reading_time_minutes }} menit baca</span>
                                </div>
                                <div class="flex items-center gap-1.5 font-bold text-sky-600 bg-sky-50 px-2.5 py-1 rounded-md border border-sky-100">
                                    <Eye class="w-4 h-4" />
                                    <span>{{ featuredArticle.views_count || 0 }} dilihat</span>
                                </div>
                            </div>

                            <!-- Excerpt -->
                            <p class="text-slate-600 text-sm sm:text-base line-clamp-3 leading-relaxed">
                                {{ featuredArticle.excerpt }}
                            </p>

                            <!-- Action Button -->
                            <div class="pt-2">
                                <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white text-sm font-bold shadow-md transition-all">
                                    <span>Baca Selengkapnya</span>
                                    <ArrowRight class="w-4 h-4" />
                                </span>
                            </div>
                        </div>
                    </Link>

                    <!-- Article Grid Header -->
                    <div class="flex items-center justify-between pt-2">
                        <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                            <BookOpen class="w-5 h-5 text-sky-500" />
                            <span>Semua Berita & Artikel</span>
                        </h3>
                        <span class="text-xs font-semibold text-slate-500 bg-white px-3 py-1 rounded-full border border-slate-200">{{ articles.length }} artikel</span>
                    </div>

                    <!-- Articles Grid (2 columns - Entire Card Clickable + Views Count) -->
                    <div v-if="articles.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <Link 
                            v-for="art in articles" 
                            :key="art.id" 
                            :href="`/berita/${art.slug}`"
                            class="block group rounded-2xl bg-white border border-slate-200/80 hover:border-sky-300 transition-all duration-300 flex flex-col justify-between overflow-hidden shadow-sm hover:shadow-md cursor-pointer"
                        >
                            <!-- Article Image Thumbnail -->
                            <div class="aspect-[16/10] w-full overflow-hidden bg-slate-100 relative">
                                <img 
                                    :src="art.cover_image_url || '/logo_superposko.png'" 
                                    :alt="art.title" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                />
                                <div class="absolute top-3 left-3">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-md bg-white/95 text-sky-600 shadow-xs border border-slate-200">
                                        {{ art.category }}
                                    </span>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-5 flex-grow flex flex-col justify-between space-y-4">
                                <div class="space-y-2">
                                    <!-- Meta Info with Views Count -->
                                    <div class="flex items-center justify-between text-xs text-slate-500">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ art.published_at }}</span>
                                            <span>•</span>
                                            <span>{{ art.reading_time_minutes }} menit</span>
                                        </div>
                                        <div class="flex items-center gap-1 text-sky-600 font-bold bg-sky-50 px-2 py-0.5 rounded-md border border-sky-100">
                                            <Eye class="w-3.5 h-3.5" />
                                            <span>{{ art.views_count || 0 }} dilihat</span>
                                        </div>
                                    </div>

                                    <!-- Title -->
                                    <h4 class="text-base font-bold text-slate-900 group-hover:text-[#38BDF8] transition-colors line-clamp-2 leading-snug">
                                        {{ art.title }}
                                    </h4>

                                    <!-- Excerpt -->
                                    <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                                        {{ art.excerpt }}
                                    </p>
                                </div>

                                <!-- Card Footer -->
                                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                    <!-- Author Info -->
                                    <div class="flex items-center gap-2 text-xs text-slate-700 max-w-[70%] truncate font-medium">
                                        <img :src="art.posko_logo_url" alt="Posko Logo" class="w-4 h-4 rounded-full object-cover border border-slate-200 flex-shrink-0" />
                                        <span class="truncate font-semibold">{{ art.author_display_name }}</span>
                                    </div>

                                    <span class="text-xs font-bold text-[#38BDF8] flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                        <span>Baca</span>
                                        <ArrowRight class="w-3.5 h-3.5" />
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="rounded-2xl bg-white border border-slate-200/80 p-12 text-center space-y-4 shadow-sm">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                            <BookOpen class="w-8 h-8" />
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Belum Ada Artikel</h3>
                        <p class="text-sm text-slate-600 max-w-md mx-auto">Tidak ada berita yang cocok dengan kriteria pencarian atau filter yang Anda pilih.</p>
                        <button @click="resetFilters" class="px-4 py-2 text-xs font-bold rounded-xl bg-[#38BDF8] text-white hover:bg-sky-500 transition-colors shadow-sm">
                            Tampilkan Semua Artikel
                        </button>
                    </div>
                </div>

                <!-- Right Sidebar Column (4 cols) -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- Search Widget -->
                    <div class="rounded-2xl bg-white border border-slate-200/80 p-5 space-y-3 shadow-sm">
                        <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <Search class="w-4 h-4 text-sky-500" />
                            <span>Cari Artikel</span>
                        </h4>
                        <div class="relative flex items-center">
                            <input 
                                v-model="searchQuery" 
                                @keyup.enter="handleSearch"
                                type="text" 
                                placeholder="Ketik kata kunci berita..." 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#38BDF8] transition-colors pr-10"
                            />
                            <button @click="handleSearch" class="absolute right-2 p-1.5 rounded-lg bg-[#38BDF8] text-white hover:bg-sky-500 transition-colors cursor-pointer">
                                <Search class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Categories Widget -->
                    <div class="rounded-2xl bg-white border border-slate-200/80 p-5 space-y-3 shadow-sm">
                        <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <Layers class="w-4 h-4 text-sky-500" />
                            <span>Kategori</span>
                        </h4>
                        <div class="space-y-1.5">
                            <button 
                                @click="filterByCategory('Semua')" 
                                :class="selectedCategory === 'Semua' ? 'bg-sky-50 text-sky-600 font-bold border-sky-200' : 'text-slate-600 hover:bg-slate-50 border-transparent'"
                                class="w-full px-3 py-2 rounded-xl text-sm border flex items-center justify-between transition-colors text-left cursor-pointer"
                            >
                                <span class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[#38BDF8]"></span>
                                    <span>Semua Artikel</span>
                                </span>
                            </button>
                            <button 
                                v-for="cat in categories" 
                                :key="cat.name" 
                                @click="filterByCategory(cat.name)" 
                                :class="selectedCategory === cat.name ? 'bg-sky-50 text-sky-600 font-bold border-sky-200' : 'text-slate-600 hover:bg-slate-50 border-transparent'"
                                class="w-full px-3 py-2 rounded-xl text-sm border flex items-center justify-between transition-colors text-left cursor-pointer"
                            >
                                <span class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                                    <span>{{ cat.name }}</span>
                                </span>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-slate-100 text-slate-500">{{ cat.count }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Recent Articles Widget -->
                    <div class="rounded-2xl bg-white border border-slate-200/80 p-5 space-y-4 shadow-sm">
                        <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <Clock class="w-4 h-4 text-sky-500" />
                            <span>Artikel Terbaru</span>
                        </h4>
                        <div class="space-y-3">
                            <Link 
                                v-for="recent in recentArticles" 
                                :key="recent.id" 
                                :href="`/berita/${recent.slug}`" 
                                class="flex items-start gap-3 group"
                            >
                                <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0 border border-slate-200">
                                    <img :src="recent.cover_image_url || '/logo_superposko.png'" :alt="recent.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                                </div>
                                <div class="space-y-0.5">
                                    <h5 class="text-xs font-bold text-slate-800 group-hover:text-[#38BDF8] transition-colors line-clamp-2 leading-tight">
                                        {{ recent.title }}
                                    </h5>
                                    <p class="text-[11px] text-slate-400">{{ recent.published_at }}</p>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <!-- Popular Tags Widget -->
                    <div v-if="popularTags.length > 0" class="rounded-2xl bg-white border border-slate-200/80 p-5 space-y-3 shadow-sm">
                        <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <Tag class="w-4 h-4 text-sky-500" />
                            <span>Tags Populer</span>
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            <button 
                                v-for="t in popularTags" 
                                :key="t.name" 
                                @click="filterByTag(t.name)"
                                :class="filters.tag === t.name ? 'bg-[#38BDF8] text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                class="px-3 py-1 rounded-lg text-xs transition-colors flex items-center gap-1 cursor-pointer"
                            >
                                <span>#{{ t.name }}</span>
                                <span class="text-[10px] opacity-70">({{ t.count }})</span>
                            </button>
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
