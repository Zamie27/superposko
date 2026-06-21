<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import ToastContainer from '@/components/ToastContainer.vue';
import ConfirmationModal from '@/components/ConfirmationModal.vue';
import ReportBubble from '@/components/ReportBubble.vue';
import type { BreadcrumbItem } from '@/types';
import { usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Lock } from '@lucide/vue';
import { Button } from '@/components/ui/button';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();

const isLocked = computed(() => {
    const user = page.props.auth?.user as any;
    if (!user) return false;

    // Admin is never locked
    if (user.role === 'admin') return false;

    // Non-subscribed users get locked out of host pages
    const isSubscribed = user.is_subscribed;
    if (isSubscribed) return false;

    const urlPath = page.url;
    if (urlPath.startsWith('/preorder')) return false;

    const hostPaths = [
        '/dashboard',
        '/finance',
        '/logbook',
        '/management',
        '/contacts',
        '/repository',
        '/voting',
        '/documentation'
    ];

    return hostPaths.some(path => urlPath.startsWith(path));
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <div class="relative flex-1 flex flex-col">
                <slot />
                <!-- Locked blur overlay for non-subscribed users viewing host pages -->
                <div v-if="isLocked" class="absolute inset-0 bg-slate-900/10 backdrop-blur-[6px] flex flex-col items-center justify-center z-50 pointer-events-auto cursor-not-allowed">
                     <div class="bg-white/95 border border-slate-200/80 shadow-2xl rounded-3xl p-8 max-w-sm text-center flex flex-col items-center gap-5 animate-in fade-in zoom-in duration-200">
                          <div class="p-4 bg-sky-50 text-sky-500 rounded-full border border-sky-100 shadow-inner">
                               <Lock class="size-9" />
                          </div>
                          <div>
                               <h3 class="text-xl font-extrabold text-slate-900">Layanan ini tersedia di Langganan</h3>
                               <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                                    Silakan lakukan Preorder terlebih dahulu untuk mengaktifkan akses Host Posko KKN Anda.
                                </p>
                          </div>
                          <Link href="/user/preorder" class="w-full">
                               <Button class="w-full bg-sky-500 hover:bg-sky-600 text-white font-semibold py-6 rounded-xl text-sm transition shadow-md shadow-sky-100">
                                    Preorder Sekarang
                               </Button>
                          </Link>
                     </div>
                </div>
            </div>
        </AppContent>
        <ToastContainer />
        <ConfirmationModal />
        <ReportBubble />
    </AppShell>
</template>
