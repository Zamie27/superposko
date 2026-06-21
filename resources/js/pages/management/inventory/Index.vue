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
                title: 'Inventaris',
                href: '/management/inventory',
            },
        ],
    },
});
</script>

<template>
    <Head title="Inventaris" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <ComingSoon 
            title="Katalog Inventaris Posko" 
            description="Catat barang inventaris posko, perlengkapan KKN yang dipinjam dari warga/desa, dan kelola checklist pengembalian otomatis." 
        />
    </div>
</template>
