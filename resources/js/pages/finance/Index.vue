<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import ComingSoon from '@/components/ComingSoon.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Kas & Keuangan',
                href: '/finance',
            },
        ],
    },
});
</script>

<template>
    <Head title="Kas & Keuangan" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <ComingSoon 
            title="E-Bendahara (Kas & Keuangan)" 
            description="Kelola iuran kas, catat pengeluaran posko secara transparan lengkap dengan bukti nota belanja digital, dan hindari selisih dana kelompok." 
        />
    </div>
</template>
