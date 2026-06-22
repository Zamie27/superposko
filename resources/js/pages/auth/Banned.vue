<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ShieldAlert, LogOut, MessageSquare } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { logout } from '@/routes';

defineOptions({
    layout: {
        title: 'Akun Ditangguhkan',
        description: 'Mohon maaf, akun Anda telah dinonaktifkan atau di-banned oleh administrator.',
    },
});

const handleLogout = () => {
    router.flushAll();
};
</script>

<template>
    <Head title="Akun Di-banned" />

    <div class="flex flex-col items-center justify-center gap-6 text-center">
        <!-- Banner/Icon Visual -->
        <div class="relative flex h-20 w-20 items-center justify-center rounded-full bg-red-50 text-red-500 shadow-inner">
            <ShieldAlert class="h-10 w-10 animate-bounce" />
            <div class="absolute -right-1 -top-1 flex h-4 w-4 animate-ping rounded-full bg-red-400 opacity-75"></div>
            <div class="absolute -right-1 -top-1 flex h-4 w-4 rounded-full bg-red-500"></div>
        </div>

        <!-- Detail Message Card -->
        <div class="w-full rounded-2xl border border-red-100 bg-red-50/30 p-5 text-left text-sm text-slate-700 shadow-sm backdrop-blur-md">
            <h3 class="font-bold text-red-900 mb-2">Informasi Penting:</h3>
            <p class="leading-relaxed text-slate-600 mb-4">
                Akses Anda ke platform SuperPosko telah dibatasi karena melanggar ketentuan penggunaan, penyalahgunaan sistem, atau masa sewa posko telah dihentikan secara sepihak oleh admin.
            </p>
            <div class="border-t border-red-100/50 pt-3 flex flex-col gap-2">
                <span class="text-xs text-slate-500 font-medium">Butuh Bantuan? Hubungi pengembang & support di:</span>
                <div class="flex flex-col gap-1 text-xs">
                    <span class="font-semibold text-slate-800">Email: <a href="mailto:kuukok.id@gmail.com" class="text-sky-600 hover:underline">kuukok.id@gmail.com</a></span>
                    <span class="font-semibold text-slate-800">WhatsApp: <a href="https://wa.me/6285171739232" target="_blank" rel="noopener noreferrer" class="text-sky-600 hover:underline">+62 851-7173-9232</a></span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col w-full gap-3">
            <a
                href="https://wa.me/6285171739232"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-center justify-center gap-2 w-full rounded-md bg-emerald-600 text-white px-4 py-2.5 text-sm font-semibold hover:bg-emerald-700 shadow-sm transition-colors duration-200"
            >
                <MessageSquare class="h-4 w-4" />
                Hubungi Support (WhatsApp)
            </a>

            <Link
                class="flex items-center justify-center gap-2 w-full rounded-md border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors duration-200 cursor-pointer"
                :href="logout()"
                @click="handleLogout"
                as="button"
                method="post"
            >
                <LogOut class="h-4 w-4" />
                Keluar / Ganti Akun
            </Link>
        </div>
    </div>
</template>
