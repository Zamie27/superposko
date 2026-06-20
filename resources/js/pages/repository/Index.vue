<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

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
        <div class="p-6 bg-card border rounded-lg">
            Halaman Repository Proker (Kosong)
        </div>
    </div>
</template>
