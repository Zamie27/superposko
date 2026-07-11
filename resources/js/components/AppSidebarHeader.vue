<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem } from '@/types';
import { usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const dplData = computed(() => page.props.dpl as any);

const switchPosko = (event: Event) => {
    const target = event.target as HTMLSelectElement;
    const hostId = target.value;
    if (hostId) {
        router.post('/dpl/switch-posko', { host_id: hostId }, {
            preserveState: false,
        });
    }
};
</script>

<template>
    <header
        class="sticky top-0 z-40 bg-white/95 backdrop-blur-md dark:bg-neutral-950/95 flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
        <div v-if="dplData" class="ml-auto flex items-center gap-2">
            <span class="text-xs font-semibold text-slate-500">Posko:</span>
            <select
                :value="dplData.active_host_id || ''"
                @change="switchPosko"
                class="text-xs h-9 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-3 text-slate-800 dark:text-slate-100 focus:outline-none focus:border-indigo-550 max-w-[200px]"
            >
                <option value="" disabled>-- Pilih Posko --</option>
                <option
                    v-for="posko in dplData.poskos"
                    :key="posko.id"
                    :value="posko.id"
                >
                    {{ posko.name }} - Kel. {{ posko.group_number }}
                </option>
            </select>
        </div>
        <img
            src="/logo_superposko.png"
            alt="SuperPosko"
            class="h-8 w-auto object-contain ml-auto"
            :class="[dplData ? 'hidden md:block' : 'block md:hidden']"
        />
    </header>
</template>
