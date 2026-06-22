<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import ToastContainer from '@/components/ToastContainer.vue';
import ConfirmationModal from '@/components/ConfirmationModal.vue';
import ReportBubble from '@/components/ReportBubble.vue';
import BugReportBubble from '@/components/BugReportBubble.vue';
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
        <AppContent variant="sidebar" class="h-svh overflow-y-auto">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <div class="relative flex-1 flex flex-col min-h-[400px]">
                <!-- Secure Slot Wrapper: Only render content if NOT locked -->
                <template v-if="!isLocked">
                    <slot />
                </template>
                <!-- Locked blur overlay for non-subscribed users viewing host pages -->
                <div v-else class="absolute inset-0 bg-slate-50/60 dark:bg-slate-900/60 backdrop-blur-md flex flex-col items-center justify-center z-50 pointer-events-auto cursor-not-allowed p-6">
                    <div class="max-w-md p-8 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-950/95 shadow-xl flex flex-col items-center text-center">
                        <div class="h-16 w-16 rounded-full bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center text-amber-500 mb-6 animate-pulse">
                            <Lock class="w-8 h-8" />
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Fitur Premium Terkunci</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-3 leading-relaxed">
                            Layanan ini hanya tersedia setelah berlangganan. Silakan aktifkan paket posko untuk mengakses fitur ini tanpa batas.
                        </p>
                        <div class="mt-6 flex flex-col gap-3 w-full">
                            <Link 
                                :href="page.props.preorder_promo_active ? '/preorder' : '/payment'" 
                                class="flex-1 rounded-xl bg-[#38BDF8] hover:bg-[#38BDF8]/90 py-3 text-sm font-bold text-white transition duration-200 text-center"
                            >
                                {{ page.props.preorder_promo_active ? 'Preorder Sekarang' : 'Beli Langganan' }}
                            </Link>
                            <Link 
                                href="/dashboard" 
                                class="flex-1 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 transition duration-200 text-center"
                            >
                                Kembali ke Dashboard
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </AppContent>
        <ToastContainer />
        <ConfirmationModal />
        <ReportBubble />
        <BugReportBubble />
    </AppShell>
</template>
