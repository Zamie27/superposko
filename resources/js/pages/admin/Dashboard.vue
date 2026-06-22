<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Users, CreditCard, ShoppingBag, Settings, CheckCircle2, Shield, Clock, Bell, Bug, Trophy, Medal } from '@lucide/vue';

defineProps<{
    stats: {
        totalUsers: number;
        totalHosts: number;
        activeSubscriptions: number;
        totalPreorders: number;
        pendingPreorders: number;
        approvedPreorders: number;
        totalTrials: number;
        totalBugReports: number;
        pendingBugReports: number;
        resolvedBugReports: number;
    };
    topBugReporters: Array<{
        reporter_name: string;
        contact_info: string | null;
        total_reports: number;
    }>;
    topAcceptedReporters: Array<{
        reporter_name: string;
        contact_info: string | null;
        accepted_reports: number;
    }>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard Admin',
                href: '/admin/dashboard',
            },
        ],
    },
});

const getRankStyle = (index: number) => {
    if (index === 0) {
return 'bg-amber-400 text-white';
}

    if (index === 1) {
return 'bg-slate-400 text-white';
}

    if (index === 2) {
return 'bg-amber-600 text-white';
}

    return 'bg-slate-100 text-slate-500';
};
</script>

<template>
    <Head title="Admin Dashboard - SuperPosko" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-6xl mx-auto font-sans">
        <div class="space-y-1">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard Owner</h1>
            <p class="text-sm text-slate-500">Selamat datang, Administrator. Monitor performa platform KKN secara real-time.</p>
        </div>

        <!-- Statistics Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <!-- Total Users Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Akun</span>
                    <h3 class="text-2xl font-extrabold text-slate-900">{{ stats.totalUsers }}</h3>
                    <p class="text-[10px] text-slate-400">Pengguna terdaftar</p>
                </div>
                <div class="p-3 bg-sky-50 text-sky-500 rounded-2xl">
                    <Users class="size-5" />
                </div>
            </div>

            <!-- Total Active Hosts Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Host Aktif</span>
                    <h3 class="text-2xl font-extrabold text-slate-900">{{ stats.activeSubscriptions }}</h3>
                    <p class="text-[10px] text-slate-400">Langganan aktif</p>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-500 rounded-2xl">
                    <CheckCircle2 class="size-5" />
                </div>
            </div>

            <!-- Active Trials Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Akun Trial</span>
                    <h3 class="text-2xl font-extrabold text-slate-900">{{ stats.totalTrials }}</h3>
                    <p class="text-[10px] text-slate-400">Trial sedang berjalan</p>
                </div>
                <div class="p-3 bg-amber-50 text-amber-500 rounded-2xl">
                    <Shield class="size-5" />
                </div>
            </div>

            <!-- Total Preorders Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Preorder Pending</span>
                    <h3 class="text-2xl font-extrabold text-slate-900">{{ stats.pendingPreorders }}</h3>
                    <p class="text-[10px] text-slate-400">Menunggu verifikasi</p>
                </div>
                <div class="p-3 bg-indigo-50 text-indigo-500 rounded-2xl">
                    <ShoppingBag class="size-5" />
                </div>
            </div>
        </div>

        <!-- Bug Report Stats + Leaderboards -->
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-slate-900">Statistik Laporan Bug</h2>

            <!-- Bug Report Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <Link href="/admin/bug-reports" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex items-center justify-between hover:border-sky-500 transition group">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Laporan Bug</span>
                        <h3 class="text-2xl font-extrabold text-slate-900">{{ stats.totalBugReports }}</h3>
                        <p class="text-[10px] text-slate-400">Semua laporan masuk</p>
                    </div>
                    <div class="p-3 bg-sky-50 text-sky-500 rounded-2xl group-hover:bg-sky-100 transition">
                        <Bug class="size-5" />
                    </div>
                </Link>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Bug Pending</span>
                        <h3 class="text-2xl font-extrabold text-red-600">{{ stats.pendingBugReports }}</h3>
                        <p class="text-[10px] text-slate-400">Belum ditangani</p>
                    </div>
                    <div class="p-3 bg-red-50 text-red-500 rounded-2xl">
                        <Bug class="size-5" />
                    </div>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex items-center justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Bug Diterima</span>
                        <h3 class="text-2xl font-extrabold text-green-600">{{ stats.resolvedBugReports }}</h3>
                        <p class="text-[10px] text-slate-400">Sudah diverifikasi</p>
                    </div>
                    <div class="p-3 bg-green-50 text-green-500 rounded-2xl">
                        <CheckCircle2 class="size-5" />
                    </div>
                </div>
            </div>

            <!-- Two-column Leaderboards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Top Bug Reporters -->
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2.5">
                        <div class="p-2 bg-amber-50 text-amber-500 rounded-xl">
                            <Trophy class="size-4" />
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">Peringkat Pelapor Bug Terbanyak</h3>
                            <p class="text-[10px] text-slate-400">Berdasarkan jumlah laporan bug yang dikirim</p>
                        </div>
                    </div>
                    <div v-if="topBugReporters.length > 0" class="divide-y divide-slate-50">
                        <div v-for="(reporter, index) in topBugReporters" :key="index" class="px-5 py-3 flex items-center gap-3 hover:bg-slate-50/50 transition">
                            <span class="flex items-center justify-center size-7 rounded-full text-xs font-bold shrink-0" :class="getRankStyle(index)">
                                {{ index + 1 }}
                            </span>
                            <div class="flex-grow min-w-0">
                                <div class="font-semibold text-sm text-slate-800 truncate">{{ reporter.reporter_name }}</div>
                                <div class="text-[10px] text-slate-400 truncate">{{ reporter.contact_info || 'Tidak ada kontak' }}</div>
                            </div>
                            <span class="text-sm font-bold text-sky-600 bg-sky-50 px-2.5 py-1 rounded-lg shrink-0">
                                {{ reporter.total_reports }}
                            </span>
                        </div>
                    </div>
                    <div v-else class="px-5 py-8 text-center text-sm text-slate-400">
                        Belum ada laporan bug.
                    </div>
                </div>

                <!-- Top Accepted/Resolved Reporters -->
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2.5">
                        <div class="p-2 bg-green-50 text-green-500 rounded-xl">
                            <Medal class="size-4" />
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">Peringkat Temuan Bug Diterima</h3>
                            <p class="text-[10px] text-slate-400">Bug yang sudah diverifikasi admin sebagai temuan valid</p>
                        </div>
                    </div>
                    <div v-if="topAcceptedReporters.length > 0" class="divide-y divide-slate-50">
                        <div v-for="(reporter, index) in topAcceptedReporters" :key="index" class="px-5 py-3 flex items-center gap-3 hover:bg-slate-50/50 transition">
                            <span class="flex items-center justify-center size-7 rounded-full text-xs font-bold shrink-0" :class="getRankStyle(index)">
                                {{ index + 1 }}
                            </span>
                            <div class="flex-grow min-w-0">
                                <div class="font-semibold text-sm text-slate-800 truncate">{{ reporter.reporter_name }}</div>
                                <div class="text-[10px] text-slate-400 truncate">{{ reporter.contact_info || 'Tidak ada kontak' }}</div>
                            </div>
                            <span class="text-sm font-bold text-green-600 bg-green-50 px-2.5 py-1 rounded-lg shrink-0">
                                {{ reporter.accepted_reports }}
                            </span>
                        </div>
                    </div>
                    <div v-else class="px-5 py-8 text-center text-sm text-slate-400">
                        Belum ada bug yang diterima.
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Administration Panels -->
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-slate-900">Modul Pengelolaan Admin</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <!-- User Management -->
                <Link href="/admin/users" class="group rounded-2xl border border-slate-200 bg-white p-5 hover:border-sky-500 hover:shadow-sm transition duration-200 flex flex-col justify-between h-40">
                    <div class="space-y-2">
                        <div class="p-2 bg-sky-50 text-sky-500 rounded-xl w-10 h-10 flex items-center justify-center">
                            <Users class="size-5" />
                        </div>
                        <h4 class="font-bold text-slate-900 group-hover:text-sky-600 transition">Manajemen User</h4>
                        <p class="text-xs text-slate-500">Kelola akun terdaftar, set/ubah password, reset, dan ganti role akun.</p>
                    </div>
                </Link>

                <!-- Subscription Management -->
                <Link href="/admin/subscriptions" class="group rounded-2xl border border-slate-200 bg-white p-5 hover:border-sky-500 hover:shadow-sm transition duration-200 flex flex-col justify-between h-40">
                    <div class="space-y-2">
                        <div class="p-2 bg-emerald-50 text-emerald-500 rounded-xl w-10 h-10 flex items-center justify-center">
                            <CheckCircle2 class="size-5" />
                        </div>
                        <h4 class="font-bold text-slate-900 group-hover:text-emerald-600 transition">Manajemen Langganan</h4>
                        <p class="text-xs text-slate-500">Manajemen akses langganan posko, perpanjang waktu aktif, dan bypass bayar.</p>
                    </div>
                </Link>

                <!-- Preorders Management -->
                <Link href="/admin/preorders" class="group rounded-2xl border border-slate-200 bg-white p-5 hover:border-sky-500 hover:shadow-sm transition duration-200 flex flex-col justify-between h-40">
                    <div class="space-y-2">
                        <div class="p-2 bg-amber-50 text-amber-500 rounded-xl w-10 h-10 flex items-center justify-center">
                            <ShoppingBag class="size-5" />
                        </div>
                        <h4 class="font-bold text-slate-900 group-hover:text-amber-600 transition">Manajemen Preorder</h4>
                        <p class="text-xs text-slate-500">Cek berkas preorder, unduh bukti bayar, dan aktivasi akun promosi.</p>
                    </div>
                </Link>

                <!-- Trial Management -->
                <Link href="/admin/trials" class="group rounded-2xl border border-slate-200 bg-white p-5 hover:border-sky-500 hover:shadow-sm transition duration-200 flex flex-col justify-between h-40">
                    <div class="space-y-2">
                        <div class="p-2 bg-amber-50 text-amber-500 rounded-xl w-10 h-10 flex items-center justify-center">
                            <Clock class="size-5" />
                        </div>
                        <h4 class="font-bold text-slate-900 group-hover:text-amber-600 transition">Manajemen Trial</h4>
                        <p class="text-xs text-slate-500">Cek sisa durasi trial, beri masa aktif trial tambahan, atau cabut akses trial user.</p>
                    </div>
                </Link>

                <!-- Prices Config -->
                <Link href="/admin/prices" class="group rounded-2xl border border-slate-200 bg-white p-5 hover:border-sky-500 hover:shadow-sm transition duration-200 flex flex-col justify-between h-40">
                    <div class="space-y-2">
                        <div class="p-2 bg-purple-50 text-purple-500 rounded-xl w-10 h-10 flex items-center justify-center">
                            <CreditCard class="size-5" />
                        </div>
                        <h4 class="font-bold text-slate-900 group-hover:text-purple-600 transition">Manajemen Harga</h4>
                        <p class="text-xs text-slate-500">Ubah harga normal & diskon langganan coret, diskon preorder, dan deskripsi.</p>
                    </div>
                </Link>

                <!-- Notifications Config -->
                <Link href="/admin/notifications" class="group rounded-2xl border border-slate-200 bg-white p-5 hover:border-sky-500 hover:shadow-sm transition duration-200 flex flex-col justify-between h-40">
                    <div class="space-y-2">
                        <div class="p-2 bg-amber-50 text-amber-500 rounded-xl w-10 h-10 flex items-center justify-center">
                            <Bell class="size-5" />
                        </div>
                        <h4 class="font-bold text-slate-900 group-hover:text-amber-600 transition">Pusat Notifikasi</h4>
                        <p class="text-xs text-slate-500">Kirim web push notification instan atau email pengumuman massal.</p>
                    </div>
                </Link>

                <!-- Site Config -->
                <Link href="/admin/settings" class="group rounded-2xl border border-slate-200 bg-white p-5 hover:border-sky-500 hover:shadow-sm transition duration-200 flex flex-col justify-between h-40">
                    <div class="space-y-2">
                        <div class="p-2 bg-slate-100 text-slate-600 rounded-xl w-10 h-10 flex items-center justify-center">
                            <Settings class="size-5" />
                        </div>
                        <h4 class="font-bold text-slate-900 group-hover:text-slate-700 transition">Pengaturan Website</h4>
                        <p class="text-xs text-slate-500">Sesuaikan deskripsi footer, alamat kontak email/whatsapp posko general.</p>
                    </div>
                </Link>
            </div>
        </div>
    </div>
</template>

