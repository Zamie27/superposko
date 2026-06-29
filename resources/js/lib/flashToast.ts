import { router } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';
import type { FlashToast } from '@/types/ui';

export function initializeFlashToast(): void {
    const toast = useToast();

    router.on('flash', (event) => {
        const flash = (event as CustomEvent).detail?.flash;
        const data = flash?.toast as FlashToast | undefined;

        if (!data) {
            return;
        }

        toast[data.type](data.message);
    });
}
