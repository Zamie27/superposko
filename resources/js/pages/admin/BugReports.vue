<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Search, Bug, CheckCircle2, Info, Image as ImageIcon } from '@lucide/vue';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { useConfirm } from '@/composables/useConfirm';

const props = defineProps<{
    bugReports: {
        data: Array<{
            id: number;
            user_id: number | null;
            title: string;
            description: string;
            reporter_name: string;
            contact_info: string | null;
            screenshots: string[] | null;
            status: 'pending' | 'resolved';
            created_at: string;
            user?: {
                id: number;
                name: string;
                email: string;
            } | null;
        }>;
        links: Array<any>;
        current_page: number;
        last_page: number;
    };
    filters: {
        search?: string;
        status?: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard Admin', href: '/admin/dashboard' },
            { title: 'Laporan Bug', href: '/admin/bug-reports' },
        ],
    },
});

const searchQuery = ref(props.filters.search || '');
const statusQuery = ref(props.filters.status || '');
const previewImage = ref<string | null>(null);

const openPreview = (url: string) => {
    previewImage.value = url;
};

const updateFilters = () => {
    router.get('/admin/bug-reports', {
        search: searchQuery.value || null,
        status: statusQuery.value || null,
    }, {
        preserveState: true,
        replace: true,
    });
};

watch([searchQuery, statusQuery], () => {
    updateFilters();
});

const { confirm } = useConfirm();

const handleResolve = async (report: any) => {
    const isConfirmed = await confirm({
        title: 'Tandai Bug Selesai?',
        message: `Apakah Anda yakin ingin menandai laporan bug "${report.title}" dari ${report.reporter_name} sebagai Selesai (Resolved)?`,
        confirmText: 'Ya, Selesai',
        cancelText: 'Batal',
    });

    if (isConfirmed) {
        router.put(`/admin/bug-reports/${report.id}/resolve`, {}, {
            preserveScroll: true,
        });
    }
};

const formatDate = (dateStr: string) => {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head title="Laporan Bug - Admin" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-6xl mx-auto font-sans">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <Link href="/admin/dashboard" class="rounded-lg p-2 hover:bg-slate-100 text-slate-500 transition-colors">
                <ArrowLeft class="size-5" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Laporan Bug</h1>
                <p class="text-sm text-slate-500">Kelola laporan bug yang dikirim pengguna melalui tombol Lapor Bug.</p>
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <!-- Search -->
            <div class="relative col-span-1 md:col-span-2">
                <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari judul, rincian, atau nama pelapor..."
                    class="w-full rounded-xl border border-slate-200 pl-9 pr-4 py-2 text-sm focus:border-sky-500 focus:outline-none bg-slate-50/50"
                />
            </div>

            <!-- Status -->
            <div>
                <select
                    v-model="statusQuery"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none bg-slate-50/50 text-slate-700"
                >
                    <option value="">Semua Status</option>
                    <option value="pending">Tertunda (Pending)</option>
                    <option value="resolved">Selesai (Resolved)</option>
                </select>
            </div>
        </div>

        <!-- Bug Reports List -->
        <div class="space-y-4">
            <div
                v-for="report in bugReports.data"
                :key="report.id"
                class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6 transition hover:border-slate-300"
            >
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <!-- Title & Metadata -->
                    <div class="space-y-2 flex-grow">
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Type Badge -->
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-100">
                                <Bug class="size-3" />
                                Bug Report
                            </span>

                            <!-- Status Badge -->
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold" :class="{
                                'bg-red-50 text-red-700 border border-red-100': report.status === 'pending',
                                'bg-green-50 text-green-700 border border-green-100': report.status === 'resolved',
                            }">
                                {{ report.status === 'pending' ? 'Pending' : 'Selesai' }}
                            </span>

                            <span class="text-xs text-slate-400 font-medium">
                                {{ formatDate(report.created_at) }}
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-slate-900">{{ report.title }}</h3>

                        <!-- Reporter Info -->
                        <div class="text-xs text-slate-500 flex flex-wrap items-center gap-1">
                            <span>Pelapor:</span>
                            <span class="font-semibold text-slate-700">{{ report.reporter_name }}</span>
                            <span v-if="report.contact_info" class="text-slate-400">
                                ({{ report.contact_info }})
                            </span>
                            <span v-if="report.user" class="text-slate-400">
                                — Akun: {{ report.user.name }} (ID: {{ report.user.id }})
                            </span>
                            <span v-else class="text-slate-400">— Tamu/Anonim</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div v-if="report.status === 'pending'" class="shrink-0 self-end sm:self-start">
                        <Button
                            @click="handleResolve(report)"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold cursor-pointer flex items-center gap-1 text-xs px-4 py-2"
                        >
                            <CheckCircle2 class="size-4" /> Tandai Selesai
                        </Button>
                    </div>
                </div>

                <hr class="my-4 border-slate-100" />

                <!-- Details Description -->
                <div class="bg-slate-50 p-4 rounded-xl text-sm text-slate-700 whitespace-pre-line leading-relaxed">
                    <div>{{ report.description }}</div>

                    <!-- Screenshots -->
                    <div v-if="report.screenshots && report.screenshots.length > 0" class="mt-4 pt-4 border-t border-slate-200/50">
                        <span class="text-xs font-semibold text-slate-400 block mb-2">
                            <ImageIcon class="size-3 inline -mt-0.5" /> Lampiran Screenshot ({{ report.screenshots.length }}):
                        </span>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="(screenshot, index) in report.screenshots"
                                :key="index"
                                @click="openPreview('/storage/' + screenshot)"
                                type="button"
                                class="inline-block group relative rounded-lg overflow-hidden border border-slate-200 w-24 h-24 hover:shadow-md transition cursor-pointer text-left focus:outline-none"
                            >
                                <img :src="'/storage/' + screenshot" alt="Screenshot Bug" class="w-full h-full object-cover" />
                                <div class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-[10px] font-bold transition">
                                    Lihat
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="bugReports.data.length === 0" class="text-center py-12 bg-white rounded-2xl border border-slate-200 shadow-sm text-slate-400">
                <Info class="size-8 mx-auto text-slate-300 mb-2" />
                Tidak ada laporan bug ditemukan.
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 flex items-center justify-between" v-if="bugReports.last_page > 1">
                <span class="text-xs text-slate-400">Halaman {{ bugReports.current_page }} dari {{ bugReports.last_page }}</span>
                <div class="flex items-center gap-2">
                    <Link
                        v-for="link in bugReports.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        class="px-3 py-1.5 rounded-lg border text-xs font-medium transition"
                        :class="{
                            'bg-sky-500 text-white border-sky-500': link.active,
                            'hover:bg-slate-50 text-slate-600 border-slate-200': !link.active,
                            'text-slate-300 pointer-events-none': !link.url
                        }"
                    >
                        <span v-html="link.label"></span>
                    </Link>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <Dialog :open="!!previewImage" @update:open="previewImage = null">
        <DialogContent class="sm:max-w-4xl p-2 bg-slate-950 border-slate-800 text-white overflow-hidden flex flex-col items-center">
            <DialogHeader class="sr-only">
                <DialogTitle>Preview Screenshot Bug</DialogTitle>
                <DialogDescription>Menampilkan detail screenshot laporan bug</DialogDescription>
            </DialogHeader>
            <div class="relative w-full max-h-[75vh] flex items-center justify-center overflow-auto p-4">
                <img :src="previewImage!" alt="Preview Screenshot Bug" class="max-w-full max-h-[70vh] object-contain rounded-md" />
            </div>
            <div class="w-full flex justify-end p-2 bg-slate-900/50 border-t border-slate-800 gap-2">
                <a :href="previewImage!" target="_blank" download class="inline-flex items-center gap-1 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs px-3 py-1.5 rounded-lg transition">
                    Unduh Gambar
                </a>
                <button @click="previewImage = null" class="bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs px-3 py-1.5 rounded-lg transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </DialogContent>
    </Dialog>
</template>
