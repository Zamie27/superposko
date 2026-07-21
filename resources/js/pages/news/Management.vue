<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Plus, Edit, Trash2, Eye, ExternalLink, Newspaper, Calendar, CheckCircle2, Clock } from '@lucide/vue';
import { useToast } from '@/composables/useToast';

const props = defineProps<{
    articles: any[];
}>();

const toast = useToast();

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

    <AppLayout>
        <div class="relative flex flex-col gap-6 rounded-xl p-4 sm:p-6 min-h-[400px] w-full max-w-full">
            
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
                    <Button class="bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-600 hover:to-indigo-700 text-white font-bold shadow-md cursor-pointer">
                        <Plus class="w-4 h-4 mr-2" />
                        <span>Tulis Artikel Baru</span>
                    </Button>
                </Link>
            </div>

            <!-- Articles Data Table Container -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 text-slate-600 dark:text-slate-400 font-semibold text-xs uppercase tracking-wider">
                                <th class="p-4">Artikel</th>
                                <th class="p-4">Kategori & Tags</th>
                                <th class="p-4">Penulis (Anggota)</th>
                                <th class="p-4">Pembaca</th>
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
                                <td colspan="6" class="p-12 text-center text-slate-500 dark:text-slate-400 space-y-3">
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

        </div>
    </AppLayout>
</template>
