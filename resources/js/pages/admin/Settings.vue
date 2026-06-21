<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Settings, ArrowLeft, CheckCircle2 } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { ref } from 'vue';

const props = defineProps<{
    settings: {
        footerAbout: string;
        footerEmail: string;
        footerPhone: string;
        footerCopyright: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard Admin', href: '/admin/dashboard' },
            { title: 'Pengaturan Website', href: '/admin/settings' },
        ],
    },
});

const cleanInitialPhone = (phone: string) => {
    if (!phone) return '';
    let clean = phone.replace(/[^0-9]/g, '');
    if (clean.startsWith('62')) {
        clean = clean.substring(2);
    } else if (clean.startsWith('0')) {
        clean = clean.substring(1);
    }
    return clean;
};

const displayPhone = ref(cleanInitialPhone(props.settings.footerPhone));

const form = useForm({
    footerAbout: props.settings.footerAbout,
    footerEmail: props.settings.footerEmail,
    footerPhone: '62' + displayPhone.value,
    footerCopyright: props.settings.footerCopyright,
});

const updatePhoneValue = () => {
    // Keep only digits
    let cleaned = displayPhone.value.replace(/[^0-9]/g, '');
    
    // If they typed leading 62 or 0, strip it
    if (cleaned.startsWith('62')) {
        cleaned = cleaned.substring(2);
    } else if (cleaned.startsWith('0')) {
        cleaned = cleaned.substring(1);
    }
    
    displayPhone.value = cleaned;
    form.footerPhone = '62' + cleaned;
};

const submitForm = () => {
    form.put('/admin/settings', {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Pengaturan Website - Admin" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-4xl mx-auto font-sans">
        <div class="flex items-center gap-3">
            <Link href="/admin/dashboard" class="rounded-lg p-2 hover:bg-slate-100 text-slate-500 transition-colors">
                <ArrowLeft class="size-5" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pengaturan Umum Website</h1>
                <p class="text-sm text-slate-500">Ubah informasi teks footer, email bantuan, nomor kontak telepon, dan copyright.</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form @submit.prevent="submitForm" class="space-y-6">
                <div class="space-y-4">
                    <h3 class="text-base font-bold text-slate-900 border-b pb-2 flex items-center gap-2">
                        <Settings class="size-5 text-slate-600" /> Konten Footer & Kontak Informasi
                    </h3>
                    
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Teks Deskripsi Tentang Website (Tentang SuperPosko)</label>
                        <textarea
                            v-model="form.footerAbout"
                            rows="4"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                            required
                        ></textarea>
                        <p v-if="form.errors.footerAbout" class="text-xs text-red-500">{{ form.errors.footerAbout }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">Email Hubungi Kami</label>
                            <input
                                v-model="form.footerEmail"
                                type="email"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                                required
                            />
                            <p v-if="form.errors.footerEmail" class="text-xs text-red-500">{{ form.errors.footerEmail }}</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">Telepon Hubungi Kami (WhatsApp)</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-sm font-semibold text-slate-500">+62</span>
                                <input
                                    v-model="displayPhone"
                                    @input="updatePhoneValue"
                                    type="text"
                                    placeholder="851XXXXXXXXX"
                                    class="w-full rounded-xl border border-slate-200 pl-12 pr-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                                    required
                                />
                            </div>
                            <p v-if="form.errors.footerPhone" class="text-xs text-red-500">{{ form.errors.footerPhone }}</p>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Nama Teks Copyright / Perusahaan Pengembang</label>
                        <input
                            v-model="form.footerCopyright"
                            type="text"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                            required
                        />
                        <p v-if="form.errors.footerCopyright" class="text-xs text-red-500">{{ form.errors.footerCopyright }}</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t flex justify-end">
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-6 py-3 rounded-xl transition duration-200 flex items-center justify-center gap-2"
                    >
                        <Spinner v-if="form.processing" />
                        <CheckCircle2 class="size-4" /> Simpan Pengaturan
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
