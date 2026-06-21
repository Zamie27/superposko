<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import ComingSoon from '@/components/ComingSoon.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Buku Kontak',
                href: '/contacts',
            },
        ],
    },
});
</script>

<template>
    <Head title="Buku Kontak" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <ComingSoon 
            title="Buku Kontak Desa & Mitra" 
            description="Simpan database kontak RT/RW, Kepala Desa, Bhabinkamtibmas, tokoh masyarakat, dan mitra eksternal program kerja posko." 
        />
    </div>
</template>
