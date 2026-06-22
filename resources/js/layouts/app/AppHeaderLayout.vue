<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
import ToastContainer from '@/components/ToastContainer.vue';
import ConfirmationModal from '@/components/ConfirmationModal.vue';
import ReportBubble from '@/components/ReportBubble.vue';
import BugReportBubble from '@/components/BugReportBubble.vue';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="header">
        <AppHeader :breadcrumbs="breadcrumbs" />
        <AppContent variant="header">
            <slot />
        </AppContent>
        <ToastContainer />
        <ConfirmationModal />
        <ReportBubble />
        <BugReportBubble />
    </AppShell>
</template>
