<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import ComingSoon from '@/components/ComingSoon.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Voting & Aspirasi',
                href: '/voting',
            },
        ],
    },
});
</script>

<template>
    <Head title="Voting & Aspirasi" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <ComingSoon 
            title="Sistem Voting & Aspirasi Kelompok" 
            description="Lakukan pemungutan suara (polling) cepat secara demokratis untuk pengambilan keputusan internal tim, serta tampung aspirasi anggota posko." 
        />
    </div>
</template>
