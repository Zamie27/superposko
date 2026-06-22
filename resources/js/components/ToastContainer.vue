<script setup lang="ts">
import { CheckCircle2, AlertTriangle, AlertCircle, Info, X } from '@lucide/vue';
import { useToast } from '@/composables/useToast';

const { toasts, removeToast } = useToast();
</script>

<template>
    <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 w-full max-w-sm pointer-events-none font-sans">
        <TransitionGroup
            name="toast-list"
            tag="div"
            class="flex flex-col gap-3 w-full"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-lg p-4 flex gap-3 overflow-hidden pointer-events-auto transition duration-300"
                :class="{
                    'border-l-4 border-l-green-500': toast.type === 'success',
                    'border-l-4 border-l-red-500': toast.type === 'error',
                    'border-l-4 border-l-amber-500': toast.type === 'warning',
                    'border-l-4 border-l-sky-500': toast.type === 'info',
                }"
            >
                <!-- Icon -->
                <div class="shrink-0 mt-0.5">
                    <CheckCircle2 v-if="toast.type === 'success'" class="size-5 text-green-500" />
                    <AlertCircle v-else-if="toast.type === 'error'" class="size-5 text-red-500" />
                    <AlertTriangle v-else-if="toast.type === 'warning'" class="size-5 text-amber-500" />
                    <Info v-else class="size-5 text-sky-500" />
                </div>

                <!-- Content -->
                <div class="flex-grow text-sm text-slate-800 dark:text-slate-200 font-medium pr-4 leading-relaxed">
                    {{ toast.message }}
                </div>

                <!-- Dismiss Button -->
                <button
                    @click="removeToast(toast.id)"
                    class="shrink-0 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors p-0.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 self-start cursor-pointer"
                >
                    <X class="size-4" />
                </button>

                <!-- Progress Bar at bottom -->
                <div class="absolute bottom-0 left-0 h-[3px] bg-slate-100 dark:bg-slate-800 w-full">
                    <div
                        class="h-full toast-progress"
                        :style="{
                            animationDuration: `${toast.duration}ms`,
                        }"
                        :class="{
                            'bg-green-500': toast.type === 'success',
                            'bg-red-500': toast.type === 'error',
                            'bg-amber-500': toast.type === 'warning',
                            'bg-sky-500': toast.type === 'info',
                        }"
                    ></div>
                </div>
            </div>
        </TransitionGroup>
    </div>
</template>

<style scoped>
/* Keyframes for the progress bar */
@keyframes shrink-progress {
    from {
        width: 100%;
    }
    to {
        width: 0%;
    }
}

.toast-progress {
    animation-name: shrink-progress;
    animation-timing-function: linear;
    animation-fill-mode: forwards;
}

/* Transition Group Animations */
.toast-list-enter-from {
    opacity: 0;
    transform: translateX(100px) translateY(0);
}
.toast-list-enter-to {
    opacity: 1;
    transform: translateX(0) translateY(0);
}
.toast-list-leave-from {
    opacity: 1;
    transform: scale(1);
}
.toast-list-leave-to {
    opacity: 0;
    transform: scale(0.9);
}
.toast-list-leave-active {
    position: absolute;
    width: 100%;
}
</style>
