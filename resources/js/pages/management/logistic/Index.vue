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
                title: 'Logistik',
                href: '/management/logistic',
            },
        ],
    },
});
</script>

<template>
    <Head title="Logistik" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <ComingSoon 
            title="Manajemen Logistik Kelompok" 
            description="Kelola distribusi logistik, bantuan sosial, kebutuhan konsumsi, dan penyaluran perlengkapan posko KKN secara terstruktur." 
        />
    </div>
</template>
