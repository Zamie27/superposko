<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Search, ArrowLeft } from '@lucide/vue';
import { Button } from '@/components/ui/button';

interface Log {
    id: number;
    user_name: string | null;
    user_email: string | null;
    role: string | null;
    category: string;
    action: string;
    description: string;
    ip_address: string | null;
    user_agent: string | null;
    created_at: string;
}

const props = defineProps<{
    logs: {
        data: Log[];
        current_page: number;
        last_page: number;
        prev_page_url: string | null;
        next_page_url: string | null;
        links: any[];
    };
    filters: {
        search?: string;
        category?: string;
        per_page?: string | number;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard Admin', href: '/admin/dashboard' },
            { title: 'Log Aktifitas', href: '/admin/activity-logs' },
        ],
    },
});

const search = ref(props.filters.search || '');
const category = ref(props.filters.category || '');
const perPage = ref(props.filters.per_page?.toString() || '30');

const handleFilter = () => {
    router.get(
        '/admin/activity-logs',
        {
            search: search.value,
            category: category.value,
            per_page: perPage.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

const clearFilters = () => {
    search.value = '';
    category.value = '';
    perPage.value = '30';
    handleFilter();
};

watch([category, perPage], () => {
    handleFilter();
});

const getCategoryBadgeClass = (cat: string) => {
    switch (cat) {
        case 'auth':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'payment':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'preorder':
            return 'bg-violet-50 text-violet-700 border-violet-200';
        case 'settings':
            return 'bg-sky-50 text-sky-700 border-sky-200';
        case 'member':
            return 'bg-rose-50 text-rose-700 border-rose-200';
        default:
            return 'bg-slate-50 text-slate-700 border-slate-200';
    }
};

const formatDate = (dateStr: string) => {
    return new Date(dateStr).toLocaleString('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    });
};
</script>

<template>
    <Head title="Log Aktifitas Sistem - Admin" />

    <div class="flex min-h-full flex-1 flex-col gap-6 p-6 max-w-7xl mx-auto font-sans">
        <div class="flex items-center gap-3">
            <Link href="/admin/dashboard" class="rounded-lg p-2 hover:bg-slate-100 text-slate-500 transition-colors">
                <ArrowLeft class="size-5" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Log Aktifitas Sistem</h1>
                <p class="text-sm text-slate-500">Pantau seluruh aktifitas sistem, login/logout, pembayaran, preorder, dan perubahan data.</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-1 flex-col sm:flex-row gap-3 w-full">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                    <input
                        v-model="search"
                        @keyup.enter="handleFilter"
                        type="text"
                        placeholder="Cari user, email, atau aksi..."
                        class="w-full rounded-xl border border-slate-200 pl-10 pr-4 py-2 text-sm focus:border-sky-500 focus:outline-none"
                    />
                </div>

                <!-- Category Select -->
                <select
                    v-model="category"
                    class="rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-sky-500 focus:outline-none bg-white min-w-[150px]"
                >
                    <option value="">Semua Kategori</option>
                    <option value="auth">Autentikasi</option>
                    <option value="payment">Pembayaran</option>
                    <option value="preorder">Preorder</option>
                    <option value="settings">Pengaturan</option>
                    <option value="member">Anggota</option>
                </select>

                <!-- Per Page Select -->
                <select
                    v-model="perPage"
                    class="rounded-xl border border-slate-200 px-4 py-2 text-sm focus:border-sky-500 focus:outline-none bg-white min-w-[120px]"
                >
                    <option value="10">10 Baris</option>
                    <option value="20">20 Baris</option>
                    <option value="30">30 Baris</option>
                    <option value="50">50 Baris</option>
                    <option value="100">100 Baris</option>
                    <option value="200">200 Baris</option>
                    <option value="500">500 Baris</option>
                    <option value="all">Semua</option>
                </select>
            </div>

            <div class="flex gap-2 w-full sm:w-auto justify-end">
                <Button @click="handleFilter" class="bg-sky-500 hover:bg-sky-600 text-white rounded-xl px-4 py-2 text-sm font-bold cursor-pointer">
                    Filter
                </Button>
                <Button @click="clearFilters" variant="outline" class="rounded-xl px-4 py-2 text-sm cursor-pointer">
                    Reset
                </Button>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                            <th class="p-4 w-[180px]">Waktu</th>
                            <th class="p-4">User</th>
                            <th class="p-4 w-[110px]">Role</th>
                            <th class="p-4 w-[120px]">Kategori</th>
                            <th class="p-4 w-[150px]">Aksi</th>
                            <th class="p-4">Keterangan</th>
                            <th class="p-4 w-[140px]">IP & User Agent</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <tr v-if="logs.data.length === 0">
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                Tidak ada log aktifitas yang ditemukan.
                            </td>
                        </tr>
                        <tr v-for="log in logs.data" :key="log.id" class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 text-xs text-slate-500 whitespace-nowrap">
                                {{ formatDate(log.created_at) }}
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-slate-900">{{ log.user_name || 'Guest/System' }}</div>
                                <div class="text-xs text-slate-400">{{ log.user_email || '-' }}</div>
                            </td>
                            <td class="p-4 text-xs whitespace-nowrap capitalize">
                                <span :class="[
                                    'px-2 py-0.5 rounded-full border text-[10px] font-semibold',
                                    log.role === 'admin' ? 'bg-red-50 text-red-700 border-red-200' :
                                    log.role === 'host' ? 'bg-sky-50 text-sky-700 border-sky-200' :
                                    'bg-slate-50 text-slate-600 border-slate-200'
                                ]">
                                    {{ log.role || 'Guest' }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span :class="['px-2 py-0.5 rounded-full border text-[10px] font-semibold capitalize', getCategoryBadgeClass(log.category)]">
                                    {{ log.category }}
                                </span>
                            </td>
                            <td class="p-4 font-mono text-xs text-slate-600 whitespace-nowrap">
                                {{ log.action }}
                            </td>
                            <td class="p-4 text-slate-600 max-w-[250px] truncate" :title="log.description">
                                {{ log.description }}
                            </td>
                            <td class="p-4 text-xs text-slate-400">
                                <div class="font-mono">{{ log.ip_address || '-' }}</div>
                                <div class="truncate max-w-[120px]" :title="log.user_agent || ''">{{ log.user_agent || '-' }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div v-if="logs.last_page > 1" class="flex items-center justify-between border-t border-slate-200 bg-white px-4 py-3 sm:px-6">
                <div class="flex flex-1 justify-between sm:hidden">
                    <Link
                        v-if="logs.prev_page_url"
                        :href="logs.prev_page_url"
                        class="relative inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    >
                        Sebelumnya
                    </Link>
                    <Link
                        v-if="logs.next_page_url"
                        :href="logs.next_page_url"
                        class="relative ml-3 inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    >
                        Selanjutnya
                    </Link>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs text-slate-500">
                            Halaman <span class="font-semibold">{{ logs.current_page }}</span> dari <span class="font-semibold">{{ logs.last_page }}</span>
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-xs" aria-label="Pagination">
                            <Link
                                v-for="(link, idx) in logs.links"
                                :key="idx"
                                :href="link.url"
                                :disabled="!link.url"
                                :class="[
                                    'relative inline-flex items-center px-3 py-1.5 text-xs font-semibold focus:z-20',
                                    link.active ? 'z-10 bg-sky-500 text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500' : 'text-slate-900 ring-1 ring-inset ring-slate-200 hover:bg-slate-50',
                                    idx === 0 ? 'rounded-l-md' : '',
                                    idx === logs.links.length - 1 ? 'rounded-r-md' : '',
                                    !link.url ? 'opacity-40 pointer-events-none' : ''
                                ]"
                            >
                                <span v-html="link.label"></span>
                            </Link>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
