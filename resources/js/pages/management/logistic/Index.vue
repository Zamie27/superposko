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
        <div class="p-6 bg-card border rounded-lg">
            Halaman Logistik (Kosong)
        </div>
    </div>
</template>
