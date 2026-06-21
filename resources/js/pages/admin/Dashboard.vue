<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Users, CreditCard, ShoppingBag, Settings, CheckCircle2 } from '@lucide/vue';

defineProps<{
    stats: {
        totalUsers: number;
        totalHosts: number;
        activeSubscriptions: number;
        totalPreorders: number;
        pendingPreorders: number;
        approvedPreorders: number;
    };
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
</script>

<template>
    <Head title="Admin Dashboard - SuperPosko" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-6xl mx-auto font-sans">
        <div class="space-y-1">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard Owner</h1>
            <p class="text-sm text-slate-500">Selamat datang, Administrator. Monitor performa platform KKN secara real-time.</p>
        </div>

        <!-- Statistics Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Users Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Akun Terdaftar</span>
                    <h3 class="text-3xl font-extrabold text-slate-900">{{ stats.totalUsers }}</h3>
                    <p class="text-xs text-slate-400">Pengguna terdaftar di database</p>
                </div>
                <div class="p-3 bg-sky-50 text-sky-500 rounded-2xl">
                    <Users class="size-6" />
                </div>
            </div>

            <!-- Total Active Hosts Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Langganan Aktif (Host)</span>
                    <h3 class="text-3xl font-extrabold text-slate-900">{{ stats.activeSubscriptions }}</h3>
                    <p class="text-xs text-slate-400">Total Host posko aktif</p>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-500 rounded-2xl">
                    <CheckCircle2 class="size-6" />
                </div>
            </div>

            <!-- Total Preorders Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Preorder Menunggu</span>
                    <h3 class="text-3xl font-extrabold text-slate-900">{{ stats.pendingPreorders }}</h3>
                    <p class="text-xs text-slate-400">Total preorder status pending</p>
                </div>
                <div class="p-3 bg-amber-50 text-amber-500 rounded-2xl">
                    <ShoppingBag class="size-6" />
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
