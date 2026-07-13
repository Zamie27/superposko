<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem } from '@/types';
import { usePage, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { LogOut, ArrowLeft, RefreshCw, ChevronDown } from '@lucide/vue';

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

const isPoskoModalOpen = ref(false);
const selectedUniversity = ref('');
const selectedHostId = ref('');

const universities = computed(() => {
    if (!dplData.value?.poskos) return [];
    const unis = dplData.value.poskos.map((p: any) => p.university || 'Tanpa Universitas');
    return [...new Set(unis)].sort();
});

const filteredPoskos = computed(() => {
    if (!dplData.value?.poskos) return [];
    let poskos = dplData.value.poskos;
    if (selectedUniversity.value) {
        poskos = poskos.filter((p: any) => (p.university || 'Tanpa Universitas') === selectedUniversity.value);
    }
    return poskos.sort((a: any, b: any) => (a.group_number || 0) - (b.group_number || 0));
});

watch(selectedUniversity, () => {
    selectedHostId.value = '';
});

const switchPosko = () => {
    if (selectedHostId.value) {
        router.post('/dpl/switch-posko', { host_id: selectedHostId.value }, {
            preserveState: false,
            onSuccess: () => {
                isPoskoModalOpen.value = false;
            }
        });
    }
};

const exitPosko = () => {
    router.post('/dpl/switch-posko', { host_id: null }, {
        preserveState: false,
    });
};

const activePoskoName = computed(() => {
    if (!dplData.value?.active_host_id) return '';
    const posko = dplData.value.poskos.find((p: any) => p.id == dplData.value.active_host_id);
    return posko ? `${posko.university || 'Tanpa Universitas'} - Kel. ${posko.group_number || '-'}` : '';
});
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
        
        <div v-if="dplData" class="ml-auto flex items-center gap-3">
            <template v-if="!dplData.active_host_id">
                <Button @click="isPoskoModalOpen = true" class="bg-indigo-600 hover:bg-indigo-700 text-white h-8 text-xs rounded-lg px-3">
                    <LogOut class="mr-1.5 size-3.5" /> Masuk Posko
                </Button>
            </template>
            <template v-else>
                <Button @click="isPoskoModalOpen = true" variant="outline" class="h-8 text-xs rounded-lg px-3 shadow-sm border-indigo-200 text-indigo-700 hover:bg-indigo-50" title="Ganti Posko">
                    <RefreshCw class="mr-1.5 size-3.5 sm:hidden" />
                    <span class="hidden sm:inline font-bold">{{ activePoskoName }}</span>
                    <ChevronDown class="ml-1.5 size-3.5" />
                </Button>
                <Button @click="exitPosko" variant="destructive" class="h-8 text-xs rounded-lg px-3 shadow-sm" title="Keluar dari mode pemantauan">
                    <LogOut class="mr-1.5 size-3.5" /> <span class="hidden sm:inline">Keluar</span>
                </Button>
            </template>
        </div>
        
        <img
            v-if="!dplData"
            src="/logo_superposko.png"
            alt="SuperPosko"
            class="h-8 w-auto object-contain ml-auto block md:hidden"
        />

        <!-- Masuk Posko Modal -->
        <Dialog v-model:open="isPoskoModalOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <LogOut class="size-5 text-indigo-600" /> Masuk ke Posko
                    </DialogTitle>
                </DialogHeader>
                <div class="space-y-4 py-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Pilih Universitas</label>
                        <select v-model="selectedUniversity" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none bg-white">
                            <option value="">-- Semua Universitas --</option>
                            <option v-for="uni in universities" :key="uni" :value="uni">{{ uni }}</option>
                        </select>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Pilih Kelompok (Posko)</label>
                        <select v-model="selectedHostId" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none bg-white">
                            <option value="">-- Wajib Pilih Kelompok --</option>
                            <option v-for="posko in filteredPoskos" :key="posko.id" :value="posko.id">
                                Kelompok {{ posko.group_number || '-' }} - {{ posko.name }}
                            </option>
                        </select>
                        <p v-if="filteredPoskos.length === 0" class="text-xs text-red-500 mt-1">Tidak ada posko di universitas ini.</p>
                    </div>
                </div>
                <DialogFooter>
                    <Button :disabled="!selectedHostId" @click="switchPosko" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold">
                        Masuk Posko
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </header>
</template>
