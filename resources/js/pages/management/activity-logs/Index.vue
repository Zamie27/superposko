<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { 
    Clock, Search, Calendar, HardDrive, Smartphone, Monitor
} from '@lucide/vue';
import { ref, watch } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
}

interface LogEntry {
    id: number;
    user_id: number | null;
    user_name: string | null;
    user_email: string | null;
    role: string | null;
    category: string;
    action: string;
    description: string;
    ip_address: string | null;
    user_agent: string | null;
    created_at: string;
    user?: User | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedLogs {
    data: LogEntry[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    prev_page_url: string | null;
    next_page_url: string | null;
    links: PaginationLink[];
}

const props = defineProps<{
    logs: PaginatedLogs;
    members: User[];
    categories: string[];
    filters: {
        search?: string;
        member_id?: string;
        category?: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Manajemen',
                href: '#',
            },
            {
                title: 'Log Aktifitas',
                href: '/management/activity-logs',
            },
        ],
    },
});

const search = ref(props.filters.search || '');
const memberId = ref(props.filters.member_id || '');
const category = ref(props.filters.category || '');

// Throttle/Watch filters to trigger server reload
watch([search, memberId, category], () => {
    router.get('/management/activity-logs', {
        search: search.value,
        member_id: memberId.value,
        category: category.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});

const formatTime = (timeStr: string) => {
    const d = new Date(timeStr);

    return d.toLocaleString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getCategoryBadgeClass = (cat: string) => {
    switch (cat.toLowerCase()) {
        case 'auth':
            return 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20';
        case 'finance':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20';
        case 'member':
            return 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20';
        case 'schedule':
            return 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20';
        default:
            return 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-800/40 dark:text-slate-400 dark:border-slate-850';
    }
};

const getDeviceIcon = (userAgent: string | null) => {
    if (!userAgent) {
return HardDrive;
}

    const ua = userAgent.toLowerCase();

    if (ua.includes('mobile') || ua.includes('android') || ua.includes('iphone')) {
return Smartphone;
}

    return Monitor;
};
</script>

<template>
    <Head title="Log Aktifitas Anggota - SuperPosko" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-7xl mx-auto font-sans">
        
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                <Clock class="size-6 text-sky-500 shrink-0" />
                Tracking Log Aktifitas Anggota
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pantau riwayat tindakan, perubahan data, dan log masuk/keluar dari seluruh anggota posko KKN Anda.</p>
        </div>

        <!-- Filter Controls -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-white dark:bg-slate-900 p-4 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs">
            <!-- Search Text -->
            <div class="relative">
                <Search class="absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari deskripsi log, aksi, email..."
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-800 pl-10 pr-4 py-2 text-sm focus:border-sky-500 focus:outline-none dark:bg-slate-950 dark:text-white"
                />
            </div>

            <!-- Member Dropdown -->
            <div>
                <select
                    v-model="memberId"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3.5 py-2 text-sm focus:border-sky-500 focus:outline-none bg-white dark:bg-slate-950 dark:text-white"
                >
                    <option value="">Semua Anggota</option>
                    <option v-for="m in members" :key="m.id" :value="m.id">
                        {{ m.name }} ({{ m.role }})
                    </option>
                </select>
            </div>

            <!-- Category Dropdown -->
            <div>
                <select
                    v-model="category"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3.5 py-2 text-sm focus:border-sky-500 focus:outline-none bg-white dark:bg-slate-950 dark:text-white"
                >
                    <option value="">Semua Kategori</option>
                    <option v-for="cat in categories" :key="cat" :value="cat">
                        {{ cat }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Logs Table -->
        <div v-if="logs.data.length > 0" class="flex flex-col gap-4">
            <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-250 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider">
                            <th class="p-4 pl-6">Waktu</th>
                            <th class="p-4">Anggota</th>
                            <th class="p-4">Kategori</th>
                            <th class="p-4">Aksi</th>
                            <th class="p-4">Deskripsi</th>
                            <th class="p-4 text-center">Perangkat / IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-150 dark:divide-slate-850 text-sm">
                        <tr 
                            v-for="log in logs.data" 
                            :key="log.id" 
                            class="hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition duration-150"
                        >
                            <!-- Time -->
                            <td class="p-4 pl-6 whitespace-nowrap text-xs text-slate-500 dark:text-slate-400">
                                <span class="flex items-center gap-1.5">
                                    <Calendar class="size-3.5 text-slate-400" />
                                    {{ formatTime(log.created_at) }}
                                </span>
                            </td>
                            <!-- User -->
                            <td class="p-4 whitespace-nowrap">
                                <div class="font-semibold text-slate-800 dark:text-white">
                                    {{ log.user_name || log.user?.name || 'User Terhapus' }}
                                </div>
                                <div class="text-[10px] text-slate-400">{{ log.role || log.user?.role || 'anggota' }}</div>
                            </td>
                            <!-- Category -->
                            <td class="p-4 whitespace-nowrap">
                                <span :class="['px-2.5 py-0.5 rounded-full border text-[10px] font-semibold uppercase tracking-wider', getCategoryBadgeClass(log.category)]">
                                    {{ log.category }}
                                </span>
                            </td>
                            <!-- Action -->
                            <td class="p-4 whitespace-nowrap font-bold text-slate-800 dark:text-slate-200">
                                {{ log.action }}
                            </td>
                            <!-- Description -->
                            <td class="p-4 text-xs text-slate-600 dark:text-slate-350 max-w-sm break-words">
                                {{ log.description }}
                            </td>
                            <!-- IP/UA -->
                            <td class="p-4 text-center whitespace-nowrap text-xs text-slate-500 dark:text-slate-400">
                                <div class="flex items-center justify-center gap-2">
                                    <component :is="getDeviceIcon(log.user_agent)" class="size-4 text-slate-400 shrink-0" :title="log.user_agent || 'Unknown'" />
                                    <span class="font-mono bg-slate-50 dark:bg-slate-950 px-2 py-0.5 border border-slate-150 dark:border-slate-850 rounded-md text-[10px] font-semibold">
                                        {{ log.ip_address || '127.0.0.1' }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="logs.last_page > 1" class="flex items-center justify-between border-t border-slate-200 dark:border-slate-800 pt-4">
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Menampilkan {{ logs.data.length }} dari {{ logs.total }} log aktifitas.
                </p>
                <div class="flex items-center gap-1.5">
                    <template v-for="link in logs.links" :key="link.label">
                        <button
                            v-if="link.url"
                            @click="router.get(link.url!, { search, member_id: memberId, category }, { preserveState: true })"
                            :class="[
                                'px-3 py-1.5 text-xs rounded-xl font-bold border transition duration-200 cursor-pointer',
                                link.active 
                                    ? 'bg-sky-500 text-white border-sky-500' 
                                    : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'
                            ]"
                            v-html="link.label"
                        />
                        <span 
                            v-else 
                            class="px-3 py-1.5 text-xs rounded-xl font-bold border border-slate-100 dark:border-slate-850 text-slate-400 select-none bg-slate-50/50 dark:bg-slate-950/20"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-else class="flex flex-col items-center justify-center py-16 px-4 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-slate-900 text-center">
            <div class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 flex items-center justify-center text-slate-400 mb-4">
                <Clock class="size-6" />
            </div>
            <h3 class="font-bold text-slate-800 dark:text-slate-200 text-base mb-1">Log Aktifitas Kosong</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm">Tidak ditemukan rekaman log aktifitas anggota posko yang cocok dengan filter pencarian Anda.</p>
        </div>

    </div>
</template>
