<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import ComingSoon from '@/components/ComingSoon.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Repository Proker',
                href: '/repository',
            },
        ],
    },
});
</script>

<template>
    <Head title="Repository Proker" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <ComingSoon 
            title="Repository Proposal & Dokumen" 
            description="Wadah penyimpanan sentral untuk berkas proposal kegiatan, surat menyurat, berkas perizinan desa, dan notulensi rapat agar tidak tercecer." 
        />
    </div>
</template>
