<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Sparkles } from '@lucide/vue';
import { Button } from '@/components/ui/button';

defineProps<{
    title: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex flex-col items-center justify-center min-h-[60vh] p-8 text-center font-sans">
        <div class="relative mb-6">
            <!-- Decorative animated rings -->
            <div class="absolute inset-0 rounded-full bg-sky-500/10 blur-xl scale-150 animate-pulse"></div>
            <div class="relative flex items-center justify-center size-20 rounded-2xl bg-gradient-to-tr from-sky-400 to-blue-500 text-white shadow-lg shadow-sky-500/20">
                <Sparkles class="size-10 animate-bounce" />
            </div>
        </div>

        <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 dark:bg-sky-950/40 px-3.5 py-1.5 text-xs font-bold text-sky-600 dark:text-sky-400 mb-3 border border-sky-100 dark:border-sky-900/50 uppercase tracking-widest">
            Segera Hadir
        </span>

        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100 sm:text-3xl max-w-md">
            {{ title }}
        </h1>

        <p class="mt-3 text-sm sm:text-base text-slate-500 dark:text-slate-400 max-w-md leading-relaxed">
            {{ description || 'Fitur ini sedang dalam tahap pengembangan aktif untuk menunjang kegiatan KKN posko Anda secara digital. Harap tunggu info selanjutnya!' }}
        </p>

        <div class="mt-8 flex flex-col sm:flex-row items-center gap-3">
            <Link href="/host/dashboard">
                <Button class="bg-sky-500 hover:bg-sky-600 text-white font-bold cursor-pointer px-6 rounded-xl shadow-md transition duration-200">
                    Kembali ke Dashboard
                </Button>
            </Link>
        </div>
    </div>
</template>
