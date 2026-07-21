<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import PublicFooter from '@/components/PublicFooter.vue';
import { Search, Calendar, Clock, Tag, ArrowRight, User, Eye, Sparkles, BookOpen, Layers } from '@lucide/vue';

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
    <Head title="Berita & Artikel Posko KKN - SuperPosko" />

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
                    <Link href="/" class="hover:text-white transition-colors">Home</Link>
                    <Link href="/event" class="hover:text-white transition-colors">Event</Link>
                    <Link href="/berita" class="text-sky-400 font-bold flex items-center gap-1.5">
                        <span>Berita</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                    </Link>
                    <Link href="/panduan" class="hover:text-white transition-colors">Panduan</Link>
                    <a href="/#fitur" class="hover:text-white transition-colors">Fitur</a>
                    <a href="/#pricing" class="hover:text-white transition-colors">Harga</a>
                    <a href="/#faq" class="hover:text-white transition-colors">FAQ</a>
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
                <Link href="/" @click="isMenuOpen = false" class="block py-2 text-slate-300 hover:text-white font-medium">Home</Link>
                <Link href="/event" @click="isMenuOpen = false" class="block py-2 text-slate-300 hover:text-white font-medium">Event</Link>
                <Link href="/berita" @click="isMenuOpen = false" class="block py-2 text-sky-400 font-bold">Berita & Artikel</Link>
                <Link href="/panduan" @click="isMenuOpen = false" class="block py-2 text-slate-300 hover:text-white font-medium">Panduan</Link>
                <a href="/#fitur" @click="isMenuOpen = false" class="block py-2 text-slate-300 hover:text-white font-medium">Fitur</a>
                <a href="/#pricing" @click="isMenuOpen = false" class="block py-2 text-slate-300 hover:text-white font-medium">Harga</a>
                <a href="/#faq" @click="isMenuOpen = false" class="block py-2 text-slate-300 hover:text-white font-medium">FAQ</a>
                <div class="pt-4 flex flex-col gap-2">
                    <Link href="/login" class="w-full text-center py-2.5 text-sm font-semibold text-slate-300 bg-slate-800 rounded-xl">Masuk</Link>
                    <Link href="/register" class="w-full text-center py-2.5 text-sm font-semibold bg-gradient-to-r from-sky-500 to-indigo-600 text-white rounded-xl">Daftar Posko</Link>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex-grow">
            
            <!-- Page Title Header -->
            <div class="mb-10">
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white flex items-center gap-3">
                    <span>Blog & Berita Posko KKN</span>
                    <Sparkles class="w-7 h-7 text-pink-500 animate-pulse" />
                </h1>
                <p class="text-slate-400 text-base sm:text-lg mt-2">Kabar kegiatan harian, cerita edukasi, dan dokumentasi inspiratif dari posko KKN di seluruh Indonesia.</p>
            </div>

            <!-- Active Tag / Search Filter Info Bar -->
            <div v-if="filters.q || filters.category || filters.tag" class="mb-6 flex flex-wrap items-center justify-between gap-3 p-4 rounded-2xl bg-slate-900/90 border border-slate-800">
                <div class="flex items-center gap-2 text-sm text-slate-300">
                    <span>Menampilkan artikel untuk:</span>
                    <span v-if="filters.q" class="font-bold text-sky-400">Pencarian "{{ filters.q }}"</span>
                    <span v-if="filters.category" class="font-bold text-pink-400">Kategori "{{ filters.category }}"</span>
                    <span v-if="filters.tag" class="font-bold text-indigo-400">Tag "#{{ filters.tag }}"</span>
                </div>
                <button @click="resetFilters" class="text-xs font-semibold text-slate-400 hover:text-white underline">Bersihkan Filter</button>
            </div>

            <!-- Content Grid Layout: Main Left (Featured & Grid) | Right Sidebar -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left Column (8 cols) -->
                <div class="lg:col-span-8 space-y-10">
                    
                    <!-- Featured Hero Article Card -->
                    <div v-if="featuredArticle" class="group relative rounded-3xl overflow-hidden bg-[#131927] border border-slate-800 hover:border-slate-700 transition-all duration-300 shadow-2xl">
                        <!-- Cover Image -->
                        <div class="aspect-video w-full overflow-hidden bg-slate-900 relative">
                            <img 
                                :src="featuredArticle.cover_image_url || '/logo_superposko.png'" 
                                :alt="featuredArticle.title" 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-[#131927] via-transparent to-transparent opacity-80"></div>
                        </div>

                        <!-- Content Body -->
                        <div class="p-6 sm:p-8 space-y-4">
                            <!-- Badges -->
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-3 py-1 text-xs font-bold rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-md">Featured</span>
                                <span class="px-3 py-1 text-xs font-bold rounded-lg bg-pink-500/20 text-pink-400 border border-pink-500/30">{{ featuredArticle.category }}</span>
                            </div>

                            <!-- Title -->
                            <Link :href="`/berita/${featuredArticle.slug}`">
                                <h2 class="text-2xl sm:text-3xl font-extrabold text-white group-hover:text-sky-400 transition-colors leading-tight">
                                    {{ featuredArticle.title }}
                                </h2>
                            </Link>

                            <!-- Meta Info -->
                            <div class="flex flex-wrap items-center gap-4 text-xs sm:text-sm text-slate-400">
                                <div class="flex items-center gap-2 font-medium text-slate-300">
                                    <img :src="featuredArticle.posko_logo_url" alt="Logo Posko" class="w-5 h-5 rounded-full object-cover border border-slate-700" />
                                    <span>{{ featuredArticle.author_display_name }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <Calendar class="w-4 h-4 text-slate-500" />
                                    <span>{{ featuredArticle.published_at }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <Clock class="w-4 h-4 text-slate-500" />
                                    <span>{{ featuredArticle.reading_time_minutes }} menit baca</span>
                                </div>
                            </div>

                            <!-- Excerpt -->
                            <p class="text-slate-400 text-sm sm:text-base line-clamp-3 leading-relaxed">
                                {{ featuredArticle.excerpt }}
                            </p>

                            <!-- Action Button -->
                            <div class="pt-2">
                                <Link 
                                    :href="`/berita/${featuredArticle.slug}`" 
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-sky-500 text-white text-sm font-bold shadow-lg shadow-indigo-500/25 hover:shadow-sky-500/40 hover:opacity-95 transition-all"
                                >
                                    <span>Baca Selengkapnya</span>
                                    <ArrowRight class="w-4 h-4" />
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Article Grid Header -->
                    <div class="flex items-center justify-between pt-4 border-t border-slate-800/80">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <BookOpen class="w-5 h-5 text-sky-400" />
                            <span>Semua Berita & Artikel</span>
                        </h3>
                        <span class="text-xs text-slate-400">{{ articles.length }} artikel</span>
                    </div>

                    <!-- Articles Grid (2 columns on sm/md) -->
                    <div v-if="articles.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div 
                            v-for="art in articles" 
                            :key="art.id" 
                            class="group rounded-2xl bg-[#131927] border border-slate-800/90 hover:border-slate-700 transition-all duration-300 flex flex-col justify-between overflow-hidden shadow-xl"
                        >
                            <!-- Article Image Thumbnail -->
                            <div class="aspect-[16/10] w-full overflow-hidden bg-slate-900 relative">
                                <img 
                                    :src="art.cover_image_url || '/logo_superposko.png'" 
                                    :alt="art.title" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                />
                                <div class="absolute top-3 left-3">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-md bg-pink-600/90 text-white backdrop-blur-md shadow-md">
                                        {{ art.category }}
                                    </span>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-5 flex-grow flex flex-col justify-between space-y-4">
                                <div class="space-y-2">
                                    <!-- Meta -->
                                    <div class="flex items-center justify-between text-xs text-slate-400">
                                        <span>{{ art.published_at }}</span>
                                        <span>{{ art.reading_time_minutes }} menit</span>
                                    </div>

                                    <!-- Title -->
                                    <Link :href="`/berita/${art.slug}`">
                                        <h4 class="text-lg font-bold text-white group-hover:text-sky-400 transition-colors line-clamp-2 leading-snug">
                                            {{ art.title }}
                                        </h4>
                                    </Link>

                                    <!-- Excerpt -->
                                    <p class="text-xs text-slate-400 line-clamp-3 leading-relaxed">
                                        {{ art.excerpt }}
                                    </p>
                                </div>

                                <!-- Card Footer -->
                                <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
                                    <!-- Author Info (Logo Posko + Group - Author) -->
                                    <div class="flex items-center gap-2 text-xs text-slate-300 max-w-[70%] truncate">
                                        <img :src="art.posko_logo_url" alt="Posko Logo" class="w-4 h-4 rounded-full object-cover border border-slate-700 flex-shrink-0" />
                                        <span class="truncate font-medium">{{ art.author_display_name }}</span>
                                    </div>

                                    <Link 
                                        :href="`/berita/${art.slug}`" 
                                        class="text-xs font-bold text-sky-400 hover:text-sky-300 flex items-center gap-1 group-hover:translate-x-1 transition-transform"
                                    >
                                        <span>Baca</span>
                                        <ArrowRight class="w-3.5 h-3.5" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="rounded-3xl bg-[#131927] border border-slate-800 p-12 text-center space-y-4">
                        <div class="w-16 h-16 rounded-full bg-slate-800/80 flex items-center justify-center mx-auto text-slate-500">
                            <BookOpen class="w-8 h-8" />
                        </div>
                        <h3 class="text-lg font-bold text-white">Belum Ada Artikel</h3>
                        <p class="text-sm text-slate-400 max-w-md mx-auto">Tidak ada berita yang cocok dengan kriteria pencarian atau filter yang Anda pilih.</p>
                        <button @click="resetFilters" class="px-4 py-2 text-xs font-bold rounded-xl bg-sky-500 text-white hover:bg-sky-600 transition-colors">
                            Tampilkan Semua Artikel
                        </button>
                    </div>
                </div>

                <!-- Right Sidebar Column (4 cols) -->
                <div class="lg:col-span-4 space-y-8">
                    
                    <!-- Search Widget -->
                    <div class="rounded-2xl bg-[#131927] border border-slate-800 p-5 space-y-3 shadow-xl">
                        <h4 class="text-base font-bold text-white flex items-center gap-2">
                            <Search class="w-4 h-4 text-sky-400" />
                            <span>Cari Artikel</span>
                        </h4>
                        <div class="relative flex items-center">
                            <input 
                                v-model="searchQuery" 
                                @keyup.enter="handleSearch"
                                type="text" 
                                placeholder="Ketik kata kunci berita..." 
                                class="w-full bg-[#0B0F19] border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 transition-colors pr-10"
                            />
                            <button @click="handleSearch" class="absolute right-2 p-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-500 transition-colors">
                                <Search class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Categories Widget -->
                    <div class="rounded-2xl bg-[#131927] border border-slate-800 p-5 space-y-4 shadow-xl">
                        <h4 class="text-base font-bold text-white flex items-center gap-2">
                            <Layers class="w-4 h-4 text-pink-400" />
                            <span>Kategori</span>
                        </h4>
                        <div class="space-y-1.5">
                            <button 
                                @click="filterByCategory('Semua')" 
                                :class="selectedCategory === 'Semua' ? 'bg-sky-500/20 text-sky-400 font-bold border-sky-500/40' : 'text-slate-300 hover:bg-slate-800/60 border-transparent'"
                                class="w-full px-3 py-2 rounded-xl text-sm border flex items-center justify-between transition-colors text-left"
                            >
                                <span class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                                    <span>Semua Artikel</span>
                                </span>
                            </button>
                            <button 
                                v-for="cat in categories" 
                                :key="cat.name" 
                                @click="filterByCategory(cat.name)" 
                                :class="selectedCategory === cat.name ? 'bg-sky-500/20 text-sky-400 font-bold border-sky-500/40' : 'text-slate-300 hover:bg-slate-800/60 border-transparent'"
                                class="w-full px-3 py-2 rounded-xl text-sm border flex items-center justify-between transition-colors text-left"
                            >
                                <span class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-pink-500"></span>
                                    <span>{{ cat.name }}</span>
                                </span>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-slate-800 text-slate-400">{{ cat.count }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Recent Articles Widget -->
                    <div class="rounded-2xl bg-[#131927] border border-slate-800 p-5 space-y-4 shadow-xl">
                        <h4 class="text-base font-bold text-white flex items-center gap-2">
                            <Clock class="w-4 h-4 text-indigo-400" />
                            <span>Artikel Terbaru</span>
                        </h4>
                        <div class="space-y-3">
                            <Link 
                                v-for="recent in recentArticles" 
                                :key="recent.id" 
                                :href="`/berita/${recent.slug}`" 
                                class="flex items-start gap-3 group"
                            >
                                <div class="w-14 h-14 rounded-xl overflow-hidden bg-slate-900 flex-shrink-0 border border-slate-800">
                                    <img :src="recent.cover_image_url || '/logo_superposko.png'" :alt="recent.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                                </div>
                                <div class="space-y-1">
                                    <h5 class="text-xs font-bold text-slate-200 group-hover:text-sky-400 transition-colors line-clamp-2 leading-tight">
                                        {{ recent.title }}
                                    </h5>
                                    <p class="text-[11px] text-slate-500">{{ recent.published_at }}</p>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <!-- Popular Tags Widget -->
                    <div v-if="popularTags.length > 0" class="rounded-2xl bg-[#131927] border border-slate-800 p-5 space-y-4 shadow-xl">
                        <h4 class="text-base font-bold text-white flex items-center gap-2">
                            <Tag class="w-4 h-4 text-purple-400" />
                            <span>Tags Populer</span>
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            <button 
                                v-for="t in popularTags" 
                                :key="t.name" 
                                @click="filterByTag(t.name)"
                                :class="filters.tag === t.name ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-800/80 text-slate-300 hover:bg-slate-700'"
                                class="px-3 py-1 rounded-lg text-xs transition-colors flex items-center gap-1"
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
        <PublicFooter />

    </div>
</template>
