import { ref } from 'vue';

export interface ToastItem {
    id: number;
    type: 'success' | 'info' | 'warning' | 'error';
    message: string;
    duration: number;
}

const toasts = ref<ToastItem[]>([]);
let nextId = 1;

export function useToast() {
    const addToast = (message: string, type: ToastItem['type'] = 'info', duration = 4000) => {
        const id = nextId++;
        const item: ToastItem = { id, type, message, duration };
        toasts.value.push(item);

        setTimeout(() => {
            removeToast(id);
        }, duration);

        return id;
    };

    const removeToast = (id: number) => {
        const index = toasts.value.findIndex((t) => t.id === id);
        if (index !== -1) {
            toasts.value.splice(index, 1);
        }
    };

    const success = (message: string, duration = 4000) => addToast(message, 'success', duration);
    const error = (message: string, duration = 4000) => addToast(message, 'error', duration);
    const info = (message: string, duration = 4000) => addToast(message, 'info', duration);
    const warning = (message: string, duration = 4000) => addToast(message, 'warning', duration);

    return {
        toasts,
        addToast,
        removeToast,
        success,
        error,
        info,
        warning,
    };
}
