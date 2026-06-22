import { ref } from 'vue';

interface ConfirmOptions {
    title: string;
    message: string;
    confirmText?: string;
    cancelText?: string;
    variant?: 'destructive' | 'default';
}

const isOpen = ref(false);
const title = ref('');
const message = ref('');
const confirmText = ref('Konfirmasi');
const cancelText = ref('Batal');
const variant = ref<'destructive' | 'default'>('default');
const resolvePromise = ref<((value: boolean) => void) | null>(null);

export function useConfirm() {
    const confirm = (options: ConfirmOptions) => {
        title.value = options.title;
        message.value = options.message;
        confirmText.value = options.confirmText || 'Konfirmasi';
        cancelText.value = options.cancelText || 'Batal';
        variant.value = options.variant || 'default';
        isOpen.value = true;

        return new Promise<boolean>((resolve) => {
            resolvePromise.value = resolve;
        });
    };

    const handleConfirm = () => {
        isOpen.value = false;

        if (resolvePromise.value) {
            resolvePromise.value(true);
        }
    };

    const handleCancel = () => {
        isOpen.value = false;

        if (resolvePromise.value) {
            resolvePromise.value(false);
        }
    };

    return {
        isOpen,
        title,
        message,
        confirmText,
        cancelText,
        variant,
        confirm,
        handleConfirm,
        handleCancel,
    };
}
