<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

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
        <div class="p-6 bg-card border rounded-lg">
            Halaman Kas & Keuangan (Kosong)
        </div>
    </div>
</template>
