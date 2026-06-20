<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

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
        <div class="p-6 bg-card border rounded-lg">
            Halaman Inventaris (Kosong)
        </div>
    </div>
</template>
