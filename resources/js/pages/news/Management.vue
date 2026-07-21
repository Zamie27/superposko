<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Plus, Edit, Trash2, Eye, ExternalLink, Newspaper, Calendar, CheckCircle2, Clock, Share2 } from '@lucide/vue';
import { useToast } from '@/composables/useToast';

const props = defineProps<{
    articles: any[];
}>();

const toast = useToast();
const selectedCtaArticle = ref<any | null>(null);
const isCtaModalOpen = ref(false);

const openCtaModal = (article: any) => {
    selectedCtaArticle.value = article;
    isCtaModalOpen.value = true;
};

const deleteArticle = (article: any) => {
    if (confirm(`Apakah Anda yakin ingin menghapus artikel "${article.title}"?`)) {
        router.delete(`/management/news/${article.id}`, {
            onSuccess: () => toast.success('Artikel berita posko berhasil dihapus.'),
        });
    }
};
</script>

<template>
    <Head title="Manajemen Berita Posko" />

    <div class="relative flex flex-col gap-6 rounded-xl p-4 sm:p-6 min-h-[450px] w-full max-w-full">
            
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                        <Newspaper class="w-6 h-6 text-sky-500" />
                        <span>Berita & Publikasi Posko KKN</span>
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Kelola berita, kegiatan desa, dan artikel posko yang dapat dibaca oleh publik di landing page SuperPosko.
                    </p>
                </div>

                <Link href="/management/news/create">
                    <Button class="bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white font-semibold shadow-xs cursor-pointer rounded-lg px-4 py-2">
                        <Plus class="w-4 h-4 mr-2" />
                        <span>Tulis Artikel Baru</span>
                    </Button>
                </Link>
            </div>

            <!-- Articles Data Table Container with min-h-[450px] so tooltip is NEVER clipped -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm min-h-[450px] relative">
                <div class="overflow-x-auto sm:overflow-visible pb-48">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 text-slate-600 dark:text-slate-400 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4">Artikel</th>
                                <th class="p-4">Kategori & Tags</th>
                                <th class="p-4">Penulis (Anggota)</th>
                                <th class="p-4">Pembaca</th>
                                <th class="p-4">Total CTA</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            <tr v-for="art in articles" :key="art.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                
                                <!-- Title & Thumbnail -->
                                <td class="p-4 max-w-xs">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex-shrink-0">
                                            <img :src="art.cover_image_url || '/logo_superposko.png'" :alt="art.title" class="w-full h-full object-cover" />
                                        </div>
                                        <div class="space-y-1 truncate">
                                            <Link :href="`/berita/${art.slug}`" target="_blank" class="font-bold text-slate-900 dark:text-white hover:text-sky-500 transition-colors flex items-center gap-1.5 truncate">
                                                <span class="truncate">{{ art.title }}</span>
                                                <ExternalLink class="w-3.5 h-3.5 flex-shrink-0 text-slate-400" />
                                            </Link>
                                            <p class="text-xs text-slate-400 truncate">{{ art.created_at }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category & Tags -->
                                <td class="p-4">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-md bg-pink-100 dark:bg-pink-500/20 text-pink-700 dark:text-pink-400">
                                        {{ art.category }}
                                    </span>
                                </td>

                                <!-- Author -->
                                <td class="p-4">
                                    <span class="text-slate-700 dark:text-slate-300 font-medium">{{ art.author_name }}</span>
                                </td>

                                <!-- Views Count -->
                                <td class="p-4">
                                    <span class="flex items-center gap-1 text-slate-600 dark:text-slate-400 font-medium">
                                        <Eye class="w-4 h-4 text-sky-500" />
                                        <span>{{ art.views_count }} views</span>
                                    </span>
                                </td>

                                <!-- Total CTA with Hover Breakdown Tooltip & Click Modal -->
                                <td class="p-4 relative">
                                    <div class="relative group/cta inline-block">
                                        <button 
                                            @click="openCtaModal(art)"
                                            type="button"
                                            class="flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 hover:scale-105 transition-all cursor-pointer"
                                            title="Klik atau hover untuk melihat detail interaksi CTA"
                                        >
                                            <span class="text-sm">💬</span>
                                            <span>{{ art.total_cta_count }} CTA</span>
                                        </button>

                                        <!-- Hover Popup Tooltip -->
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-2 hidden group-hover/cta:block w-56 p-3.5 bg-slate-900 text-white rounded-2xl shadow-2xl text-xs space-y-2 z-[100] border border-slate-700 pointer-events-none">
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 -mb-1 border-4 border-transparent border-b-slate-900"></div>
                                            
                                            <div class="font-bold border-b border-slate-700 pb-1.5 text-sky-400 flex items-center justify-between">
                                                <span>Detail Interaksi CTA</span>
                                                <span class="text-[10px] bg-slate-800 px-2 py-0.5 rounded-md text-slate-300 font-mono">{{ art.total_cta_count }} Total</span>
                                            </div>
                                            <div class="space-y-1.5 pt-0.5">
                                                <div class="flex items-center justify-between text-emerald-400">
                                                    <span class="flex items-center gap-1.5 font-medium">
                                                        <svg class="w-3.5 h-3.5 fill-current text-[#25D366]" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-1.157 4.228 4.305-1.129z"/></svg>
                                                        WhatsApp
                                                    </span>
                                                    <span class="font-mono font-bold text-white bg-slate-800 px-1.5 py-0.5 rounded">{{ art.cta_wa_count || 0 }}</span>
                                                </div>
                                                <div class="flex items-center justify-between text-blue-400">
                                                    <span class="flex items-center gap-1.5 font-medium">
                                                        <svg class="w-3.5 h-3.5 fill-current text-[#1877F2]" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                                        Facebook
                                                    </span>
                                                    <span class="font-mono font-bold text-white bg-slate-800 px-1.5 py-0.5 rounded">{{ art.cta_fb_count || 0 }}</span>
                                                </div>
                                                <div class="flex items-center justify-between text-pink-400">
                                                    <span class="flex items-center gap-1.5 font-medium">
                                                        <svg class="w-3.5 h-3.5 fill-current text-[#dc2743]" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                                        Instagram
                                                    </span>
                                                    <span class="font-mono font-bold text-white bg-slate-800 px-1.5 py-0.5 rounded">{{ art.cta_ig_count || 0 }}</span>
                                                </div>
                                                <div class="flex items-center justify-between text-amber-400">
                                                    <span class="flex items-center gap-1.5 font-medium">
                                                        <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2 text-amber-400" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                                        Salin Link
                                                    </span>
                                                    <span class="font-mono font-bold text-white bg-slate-800 px-1.5 py-0.5 rounded">{{ art.cta_copy_count || 0 }}</span>
                                                </div>
                                            </div>
                                            <div class="pt-1 border-t border-slate-800 text-[10px] text-slate-400 text-center italic">
                                                Klik badge untuk modal lengkap
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="p-4">
                                    <span v-if="art.is_published" class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 flex items-center gap-1 w-max">
                                        <CheckCircle2 class="w-3.5 h-3.5" />
                                        <span>Dipublikasikan</span>
                                    </span>
                                    <span v-else class="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 flex items-center gap-1 w-max">
                                        <Clock class="w-3.5 h-3.5" />
                                        <span>Draf</span>
                                    </span>
                                </td>

                                <!-- Action Buttons -->
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="`/management/news/${art.id}/edit`">
                                            <Button variant="outline" size="sm" class="h-8 w-8 p-0 cursor-pointer">
                                                <Edit class="w-4 h-4 text-sky-500" />
                                            </Button>
                                        </Link>
                                        <Button @click="deleteArticle(art)" variant="outline" size="sm" class="h-8 w-8 p-0 border-rose-200 dark:border-rose-900/50 hover:bg-rose-50 dark:hover:bg-rose-950/50 cursor-pointer">
                                            <Trash2 class="w-4 h-4 text-rose-500" />
                                        </Button>
                                    </div>
                                </td>

                            </tr>

                            <tr v-if="articles.length === 0">
                                <td colspan="7" class="p-12 text-center text-slate-500 dark:text-slate-400 space-y-3">
                                    <Newspaper class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-700" />
                                    <p class="font-medium">Belum ada berita yang ditulis untuk posko ini.</p>
                                    <Link href="/management/news/create">
                                        <Button size="sm" class="bg-sky-500 hover:bg-sky-600 text-white font-bold cursor-pointer">
                                            Tulis Berita Pertama Posko
                                        </Button>
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CTA Detail Modal Popup -->
            <div v-if="isCtaModalOpen && selectedCtaArticle" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4 z-[9999]">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-5 animate-in fade-in zoom-in duration-200">
                    <div class="flex items-start justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-sky-500">Statistik Bagikan / CTA</span>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white line-clamp-1 mt-0.5">
                                {{ selectedCtaArticle.title }}
                            </h3>
                        </div>
                        <button @click="isCtaModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white font-bold p-1 cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-2xl p-4 space-y-1">
                            <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-300 flex items-center gap-1">💬 WhatsApp</span>
                            <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ selectedCtaArticle.cta_wa_count || 0 }}</p>
                            <span class="text-[11px] text-slate-500">kali dibagikan</span>
                        </div>

                        <div class="bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 rounded-2xl p-4 space-y-1">
                            <span class="text-xs font-semibold text-blue-700 dark:text-blue-300 flex items-center gap-1">🌐 Facebook</span>
                            <p class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ selectedCtaArticle.cta_fb_count || 0 }}</p>
                            <span class="text-[11px] text-slate-500">kali dibagikan</span>
                        </div>

                        <div class="bg-pink-50 dark:bg-pink-950/40 border border-pink-200 dark:border-pink-800 rounded-2xl p-4 space-y-1">
                            <span class="text-xs font-semibold text-pink-700 dark:text-pink-300 flex items-center gap-1">📸 Instagram</span>
                            <p class="text-2xl font-black text-pink-600 dark:text-pink-400">{{ selectedCtaArticle.cta_ig_count || 0 }}</p>
                            <span class="text-[11px] text-slate-500">kali disalin link IG</span>
                        </div>

                        <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-2xl p-4 space-y-1">
                            <span class="text-xs font-semibold text-amber-700 dark:text-amber-300 flex items-center gap-1">🔗 Salin Link</span>
                            <p class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ selectedCtaArticle.cta_copy_count || 0 }}</p>
                            <span class="text-[11px] text-slate-500">kali disalin langsung</span>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-3 flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Total Akumulasi Interaksi</span>
                        <span class="font-bold text-slate-900 dark:text-white text-sm">{{ selectedCtaArticle.total_cta_count || 0 }} Total CTA</span>
                    </div>

                    <div class="flex justify-end">
                        <Button @click="isCtaModalOpen = false" class="bg-slate-900 text-white hover:bg-slate-800 font-bold px-5 rounded-xl cursor-pointer">
                            Tutup
                        </Button>
                    </div>
                </div>
            </div>

        </div>
</template>
