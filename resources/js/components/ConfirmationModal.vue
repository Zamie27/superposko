<script setup lang="ts">
import { useConfirm } from '@/composables/useConfirm';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { AlertTriangle, HelpCircle } from '@lucide/vue';

const { isOpen, title, message, confirmText, cancelText, variant, handleConfirm, handleCancel } = useConfirm();
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogContent class="sm:max-w-md font-sans">
            <DialogHeader class="flex flex-row items-center gap-3">
                <div
                    class="p-2 rounded-full"
                    :class="{
                        'bg-red-50 text-red-500': variant === 'destructive',
                        'bg-sky-50 text-sky-500': variant === 'default',
                    }"
                >
                    <AlertTriangle v-slot="icon" v-if="variant === 'destructive'" class="size-5 shrink-0" />
                    <HelpCircle v-else class="size-5 shrink-0" />
                </div>
                <div class="flex flex-col gap-1">
                    <DialogTitle class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ title }}</DialogTitle>
                </div>
            </DialogHeader>
            <div class="py-3">
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    {{ message }}
                </p>
            </div>
            <DialogFooter class="flex gap-2 sm:justify-end">
                <Button
                    variant="outline"
                    @click="handleCancel"
                    class="cursor-pointer"
                >
                    {{ cancelText }}
                </Button>
                <Button
                    :variant="variant === 'destructive' ? 'destructive' : 'default'"
                    @click="handleConfirm"
                    class="cursor-pointer"
                    :class="{
                        'bg-sky-500 hover:bg-sky-600 text-white': variant === 'default'
                    }"
                >
                    {{ confirmText }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
