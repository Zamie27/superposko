<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps<{
    immichUrl: string;
    immichApiKey: string;
}>();

const form = useForm({
    immich_url: props.immichUrl || '',
    immich_api_key: props.immichApiKey || '',
});

const submit = () => {
    form.put('/settings/api', {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: Show success toast if you have one
        },
    });
};
</script>

<template>
    <Head title="API Integrations" />

        <div class="space-y-6">
            <Heading
                title="Integrasi Immich"
                description="Konfigurasikan URL dan API Key untuk menghubungkan Dokumentasi dengan server Immich."
            />

            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid gap-2">
                    <Label for="immich_url">Immich Server URL</Label>
                    <Input
                        id="immich_url"
                        v-model="form.immich_url"
                        type="url"
                        placeholder="https://immich.kuukok.my.id"
                    />
                    <InputError :message="form.errors.immich_url" />
                </div>

                <div class="grid gap-2">
                    <Label for="immich_api_key">Immich API Key</Label>
                    <Input
                        id="immich_api_key"
                        v-model="form.immich_api_key"
                        type="password"
                        placeholder="O9SweCkseLkCHvtOoDgPOJny5SsWa4fHk2hZGrvE5o"
                    />
                    <InputError :message="form.errors.immich_api_key" />
                </div>

                <div class="flex items-center gap-4">
                    <Button :disabled="form.processing">Simpan Perubahan</Button>

                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p
                            v-if="form.recentlySuccessful"
                            class="text-sm text-muted-foreground"
                        >
                            Tersimpan.
                        </p>
                    </Transition>
                </div>
            </form>
        </div>
</template>
