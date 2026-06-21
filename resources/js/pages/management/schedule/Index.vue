<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import ComingSoon from '@/components/ComingSoon.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Manajemen',
                href: '#',
            },
            {
                title: 'Piket & Agenda',
                href: '/management/schedule',
            },
        ],
    },
});
</script>

<template>
    <Head title="Piket & Agenda" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <ComingSoon 
            title="Jadwal Piket & Agenda Kegiatan" 
            description="Atur kalender piket harian posko, agenda rapat kelompok, kunjungan DPL, serta jadwal sosialisasi program kerja desa." 
        />
    </div>
</template>
