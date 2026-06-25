<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { BookOpen, ChevronRight, Menu, X, ArrowLeft } from '@lucide/vue';
import { login, register, dashboard } from '@/routes';

const props = defineProps<{
    outline: Array<{
        title: string;
        slug: string;
        group: string;
        items: Array<{
            title: string;
            slug: string;
        }>;
    }>;
    topicsContent: Record<string, string>;
    footerAbout: string;
    footerEmail: string;
    footerPhone: string;
    footerCopyright: string;
}>();

const isMenuOpen = ref(false);
const activeTab = ref(props.outline[0]?.items[0]?.slug || '');

const formattedPhone = computed(() => {
    if (!props.footerPhone) return '';
    let clean = props.footerPhone.replace(/[^0-9]/g, '');
    if (clean.startsWith('62')) {
        clean = clean.substring(2);
    } else if (clean.startsWith('0')) {
        clean = clean.substring(1);
    }
    const part1 = clean.substring(0, 3);
    const part2 = clean.substring(3, 7);
    const part3 = clean.substring(7);
    let result = '+62';
    if (part1) result += ' ' + part1;
    if (part2) result += '-' + part2;
    if (part3) result += '-' + part3;
    return result;
});

const activeTopicContent = computed(() => {
    return props.topicsContent[activeTab.value] || '<p class="text-slate-400">Pilih panduan di menu sebelah kiri.</p>';
});

const selectTopic = (slug: string) => {
    activeTab.value = slug;
    // Scroll content to top
    const contentEl = document.getElementById('doc-content-area');
    if (contentEl) {
        contentEl.scrollTop = 0;
    }
};
</script>

<template>
    <Head title="Panduan & Dokumentasi Penggunaan - SuperPosko" />

    <div class="min-h-screen bg-[#F4F7F7] text-slate-900 font-sans antialiased selection:bg-[#38BDF8] selection:text-slate-950 flex flex-col">
        <!-- Navigation Header -->
        <header class="sticky top-0 z-50 w-full border-b border-slate-200/50 bg-white shadow-md">
            <div class="mx-auto flex max-w-7xl h-16 items-center justify-between px-6 lg:px-8">
                <Link href="/" class="flex items-center group">
                    <img src="/logo_superposko.png" alt="SuperPosko" class="h-9 w-auto transition-transform duration-300 group-hover:scale-105" />
                </Link>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                    <Link href="/" class="hover:text-[#38BDF8] transition-colors">Home</Link>
                    <Link href="/event" class="hover:text-[#38BDF8] transition-colors">Event</Link>
                    <Link href="/panduan" class="text-[#38BDF8] transition-colors">Panduan</Link>
                    <a href="/#fitur" class="hover:text-[#38BDF8] transition-colors">Fitur</a>
                    <a href="/#pricing" class="hover:text-[#38BDF8] transition-colors">Harga</a>
                </nav>

                <!-- Actions -->
                <div class="hidden md:flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
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
                        <Menu v-if="!isMenuOpen" class="h-6 w-6" />
                        <X v-else class="h-6 w-6" />
                    </button>
                </div>
            </div>

            <!-- Mobile Dropdown Nav -->
            <div v-if="isMenuOpen" class="border-b border-slate-200/50 bg-[#F4F7F7] px-6 py-4 md:hidden">
                <nav class="flex flex-col gap-4 text-sm font-semibold text-slate-600">
                    <Link href="/" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">Home</Link>
                    <Link href="/event" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">Event</Link>
                    <Link href="/panduan" @click="isMenuOpen = false" class="text-[#38BDF8]">Panduan</Link>
                    <a href="/#fitur" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">Fitur</a>
                    <a href="/#pricing" @click="isMenuOpen = false" class="hover:text-[#38BDF8]">Harga</a>
                    <div class="h-px bg-slate-200/50 my-2"></div>
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
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

        <!-- Main Workspace: Sidebar & Article Layout -->
        <div class="flex-grow flex w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 gap-8">
            
            <!-- Left Sidebar Navigation (Desktop) -->
            <aside class="hidden lg:block w-72 shrink-0 self-start sticky top-24 max-h-[calc(100vh-8rem)] overflow-y-auto pr-4 border-r border-slate-200/60">
                <div class="space-y-6">
                    <div v-for="section in outline" :key="section.title" class="space-y-2">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 px-3">
                            {{ section.title }}
                        </h3>
                        <nav class="space-y-1">
                            <button
                                v-for="item in section.items"
                                :key="item.slug"
                                @click="selectTopic(item.slug)"
                                class="w-full text-left px-3 py-2 text-xs font-semibold rounded-lg transition-colors flex items-center justify-between group"
                                :class="activeTab === item.slug ? 'bg-sky-500 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
                            >
                                <span class="truncate">{{ item.title }}</span>
                                <ChevronRight class="size-3.5 opacity-0 group-hover:opacity-100 transition-opacity" :class="activeTab === item.slug ? 'text-white' : 'text-slate-400'" />
                            </button>
                        </nav>
                    </div>
                </div>
            </aside>

            <!-- Right Article Area -->
            <main id="doc-content-area" class="flex-grow min-w-0 bg-white rounded-2xl border border-slate-200/80 p-6 md:p-10 shadow-xs prose prose-slate max-w-none prose-headings:font-bold prose-a:text-[#38BDF8] hover:prose-a:text-sky-600 prose-img:rounded-xl">
                <!-- Mobile Navigation Selector -->
                <div class="lg:hidden mb-6 p-4 bg-slate-50 border rounded-xl flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-500 uppercase">Pilih Topik Panduan:</label>
                    <select 
                        v-model="activeTab" 
                        @change="selectTopic(activeTab)"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-sky-500"
                    >
                        <optgroup v-for="section in outline" :key="section.title" :label="section.title">
                            <option v-for="item in section.items" :key="item.slug" :value="item.slug">
                                {{ item.title }}
                            </option>
                        </optgroup>
                    </select>
                </div>

                <!-- Parsed HTML Document Content -->
                <div class="markdown-body" v-html="activeTopicContent"></div>
            </main>
        </div>

        <!-- Footer -->
        <footer class="bg-slate-900 py-16 text-slate-400 border-t border-slate-800/50 mt-auto">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-12 md:grid-cols-4">
                    <div class="md:col-span-2">
                        <div class="flex items-center gap-2 text-white">
                            <img src="/icon_superposko.png" alt="SuperPosko Icon" class="h-10 w-auto" />
                        </div>
                        <p class="mt-4 max-w-md text-sm leading-relaxed">
                            {{ footerAbout }}
                        </p>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-white">Pintasan</h4>
                        <ul class="mt-4 space-y-2.5 text-sm">
                            <li><Link href="/" class="hover:text-[#38BDF8] transition-colors">Home Page</Link></li>
                            <li><a href="/#fitur" class="hover:text-[#38BDF8] transition-colors">Fitur Utama</a></li>
                            <li><a href="/#pricing" class="hover:text-[#38BDF8] transition-colors">Harga Paket</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-white">Hubungi Kami</h4>
                        <ul class="mt-4 space-y-2.5 text-sm">
                            <li>Email: {{ footerEmail }}</li>
                            <li>Telepon: {{ formattedPhone }}</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-12 border-t border-slate-800 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs">
                    <p>
                        &copy; 2026 
                        <a href="https://kuukok.my.id" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors underline font-medium">{{ footerCopyright }}</a> 
                        - Solusi digital profesional. Hak Cipta Dilindungi.
                    </p>
                    <p>Dibuat dengan dedikasi untuk memajukan pengabdian masyarakat mahasiswa Indonesia.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style>
/* Custom Markdown styles for pristine documentation viewing experience */
.markdown-body h1 {
    font-size: 2.25rem;
    font-weight: 800;
    color: #0f172a;
    border-b: 1px solid #e2e8f0;
    padding-bottom: 0.5rem;
    margin-top: 2rem;
    margin-bottom: 1.5rem;
}
.markdown-body h2 {
    font-size: 1.5rem;
    font-weight: 800;
    color: #1e293b;
    margin-top: 2rem;
    margin-bottom: 1rem;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 0.25rem;
}
.markdown-body h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #334155;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}
.markdown-body h4 {
    font-size: 1rem;
    font-weight: 700;
    color: #475569;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
    background-color: #f8fafc;
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
    border-left: 4px solid #38bdf8;
}
.markdown-body p {
    font-size: 0.925rem;
    line-height: 1.7;
    color: #334155;
    margin-bottom: 1rem;
}
.markdown-body ul, .markdown-body ol {
    margin-bottom: 1.25rem;
    padding-left: 1.5rem;
}
.markdown-body li {
    font-size: 0.9rem;
    line-height: 1.6;
    color: #475569;
    margin-bottom: 0.5rem;
    list-style-type: disc;
}
.markdown-body li strong {
    color: #0f172a;
}
.markdown-body hr {
    border: 0;
    border-top: 1px solid #e2e8f0;
    margin: 2.5rem 0;
}
</style>
