<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import ComingSoon from '@/components/ComingSoon.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Logbook & Proker',
                href: '/logbook',
            },
        ],
    },
});
</script>

<template>
    <Head title="Logbook & Proker" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <ComingSoon 
            title="Digital Logbook & Program Kerja" 
            description="Catat laporan kegiatan harian (logbook) anggota kelompok, kelola status progres program kerja KKN dengan papan Kanban interaktif." 
        />
    </div>
</template>
