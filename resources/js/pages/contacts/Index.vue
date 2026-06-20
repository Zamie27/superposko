<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

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
        <div class="p-6 bg-card border rounded-lg">
            Halaman Buku Kontak (Kosong)
        </div>
    </div>
</template>
