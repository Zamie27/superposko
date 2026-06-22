<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Settings, ArrowLeft, CheckCircle2, Server, Globe } from '@lucide/vue';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';

const props = defineProps<{
    settings: {
        footerAbout: string;
        footerEmail: string;
        footerPhone: string;
        footerCopyright: string;
        midtransMerchantId: string;
        midtransClientKey: string;
        midtransServerKey: string;
        midtransIsProduction: boolean;
        immichUrl: string;
        
        eventTitle?: string;
        eventPrize?: string;
        eventStartDate?: string;
        eventEndDate?: string;
        eventYoutubeEmbedUrl?: string;
        eventDescription?: string;
        eventRules?: string;
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

const activeTab = ref<'general' | 'api' | 'event'>('general');

const cleanInitialPhone = (phone: string) => {
    if (!phone) {
return '';
}

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
    midtransMerchantId: props.settings.midtransMerchantId,
    midtransClientKey: props.settings.midtransClientKey,
    midtransServerKey: props.settings.midtransServerKey,
    midtransIsProduction: props.settings.midtransIsProduction,
    immichUrl: props.settings.immichUrl,
    
    // Event Form fields
    eventTitle: props.settings.eventTitle || '',
    eventPrize: props.settings.eventPrize || '',
    eventStartDate: props.settings.eventStartDate || '',
    eventEndDate: props.settings.eventEndDate || '',
    eventYoutubeEmbedUrl: props.settings.eventYoutubeEmbedUrl || '',
    eventDescription: props.settings.eventDescription || '',
    eventRules: props.settings.eventRules || '',
});

const updatePhoneValue = () => {
    let cleaned = displayPhone.value.replace(/[^0-9]/g, '');

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
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pengaturan Website</h1>
                <p class="text-sm text-slate-500">Ubah konfigurasi umum platform, serta integrasi API pihak ketiga secara dinamis.</p>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex border-b border-slate-200 gap-4">
            <button
                type="button"
                @click="activeTab = 'general'"
                :class="[
                    'pb-3 text-sm font-semibold border-b-2 px-1 transition-all duration-200',
                    activeTab === 'general' ? 'border-sky-500 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-700'
                ]"
            >
                <span class="flex items-center gap-2">
                    <Globe class="size-4" /> Pengaturan Umum
                </span>
            </button>
            <button
                type="button"
                @click="activeTab = 'api'"
                :class="[
                    'pb-3 text-sm font-semibold border-b-2 px-1 transition-all duration-200',
                    activeTab === 'api' ? 'border-sky-500 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-700'
                ]"
            >
                <span class="flex items-center gap-2">
                    <Server class="size-4" /> Integrasi API
                </span>
            </button>
            <button
                type="button"
                @click="activeTab = 'event'"
                :class="[
                    'pb-3 text-sm font-semibold border-b-2 px-1 transition-all duration-200',
                    activeTab === 'event' ? 'border-sky-500 text-sky-600' : 'border-transparent text-slate-500 hover:text-slate-700'
                ]"
            >
                <span class="flex items-center gap-2">
                    <Settings class="size-4" /> Pengaturan Event
                </span>
            </button>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- 1. GENERAL SETTINGS TAB -->
                <div v-if="activeTab === 'general'" class="space-y-6">
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
                </div>

                <!-- 2. API INTEGRATIONS TAB -->
                <div v-if="activeTab === 'api'" class="space-y-6">
                    <!-- Midtrans Integration -->
                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-slate-900 border-b pb-2 flex items-center gap-2">
                            <Settings class="size-5 text-sky-500" /> Midtrans Snap Payment Gateway
                        </h3>

                        <!-- Midtrans Instruction Callout -->
                        <div class="p-4 rounded-xl bg-sky-50 border border-sky-100 text-xs text-sky-800 space-y-2">
                            <p class="font-bold">Panduan Konfigurasi Midtrans Dashboard:</p>
                            <p>Silakan masuk ke Dashboard Midtrans Anda dan atur Endpoint URLs berikut:</p>
                            <ul class="list-disc pl-4 space-y-1 font-mono text-[11px]">
                                <li><strong>Finish Redirect URL:</strong> https://superposko.web.id/payment</li>
                                <li><strong>Error Redirect URL:</strong> https://superposko.web.id/payment</li>
                                <li><strong>Notification Webhook URL:</strong> https://superposko.web.id/payment/notification</li>
                            </ul>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-slate-700">Merchant ID</label>
                                <input
                                    v-model="form.midtransMerchantId"
                                    type="text"
                                    placeholder="GXXXXXXXXX"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                                />
                                <p v-if="form.errors.midtransMerchantId" class="text-xs text-red-500">{{ form.errors.midtransMerchantId }}</p>
                            </div>

                            <div class="space-y-1.5 flex flex-col justify-end pb-1.5">
                                <label class="text-xs font-semibold text-slate-700 mb-2">Environment Mode (Production)</label>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="form.midtransIsProduction" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-500"></div>
                                    <span class="ml-3 text-sm font-medium text-slate-600">{{ form.midtransIsProduction ? 'Production Mode (Real Payment)' : 'Sandbox Mode (Testing)' }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-slate-700">Client Key</label>
                                <input
                                    v-model="form.midtransClientKey"
                                    type="text"
                                    placeholder="Mid-client-..."
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                                />
                                <p v-if="form.errors.midtransClientKey" class="text-xs text-red-500">{{ form.errors.midtransClientKey }}</p>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-slate-700">Server Key</label>
                                <input
                                    v-model="form.midtransServerKey"
                                    type="password"
                                    placeholder="Mid-server-..."
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                                />
                                <p v-if="form.errors.midtransServerKey" class="text-xs text-red-500">{{ form.errors.midtransServerKey }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Immich Integration -->
                    <div class="space-y-4 pt-4 border-t">
                        <h3 class="text-base font-bold text-slate-900 border-b pb-2 flex items-center gap-2">
                            <Settings class="size-5 text-emerald-500" /> Immich Storage Server
                        </h3>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">URL Server Immich Utama</label>
                            <input
                                v-model="form.immichUrl"
                                type="url"
                                placeholder="https://immich.yourdomain.com"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                            />
                            <p v-if="form.errors.immichUrl" class="text-xs text-red-500">{{ form.errors.immichUrl }}</p>
                            <p class="text-[11px] text-slate-400">Masukkan alamat URL utama instance Immich Anda. Kredensial spesifik per host KKN akan diatur pada menu "Manajemen Dokumentasi".</p>
                        </div>
                    </div>
                </div>

                <!-- 3. EVENT SETTINGS TAB -->
                <div v-if="activeTab === 'event'" class="space-y-6 animate-in fade-in duration-200">
                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-slate-900 border-b pb-2 flex items-center gap-2">
                            <Settings class="size-5 text-amber-500" /> Konten Halaman Event & Syarat Ketentuan
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-slate-700">Judul Event</label>
                                <input
                                    v-model="form.eventTitle"
                                    type="text"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                                    required
                                />
                                <p v-if="form.errors.eventTitle" class="text-xs text-red-500">{{ form.errors.eventTitle }}</p>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-slate-700">Total Hadiah (Prize Pool)</label>
                                <input
                                    v-model="form.eventPrize"
                                    type="text"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                                    required
                                />
                                <p v-if="form.errors.eventPrize" class="text-xs text-red-500">{{ form.errors.eventPrize }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-slate-700">Tanggal Mulai</label>
                                <input
                                    v-model="form.eventStartDate"
                                    type="date"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                                    required
                                />
                                <p v-if="form.errors.eventStartDate" class="text-xs text-red-500">{{ form.errors.eventStartDate }}</p>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-slate-700">Tanggal Berakhir</label>
                                <input
                                    v-model="form.eventEndDate"
                                    type="date"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                                    required
                                />
                                <p v-if="form.errors.eventEndDate" class="text-xs text-red-500">{{ form.errors.eventEndDate }}</p>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">URL Embed YouTube Tutorial (Iframe Src)</label>
                            <input
                                v-model="form.eventYoutubeEmbedUrl"
                                type="text"
                                placeholder="https://www.youtube.com/embed/dQw4w9WgXcQ"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                            />
                            <p v-if="form.errors.eventYoutubeEmbedUrl" class="text-xs text-red-500">{{ form.errors.eventYoutubeEmbedUrl }}</p>
                            <p class="text-[11px] text-slate-400">Gunakan format embed URL (contoh: https://www.youtube.com/embed/...)</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">Deskripsi / Penjelasan Event</label>
                            <textarea
                                v-model="form.eventDescription"
                                rows="4"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                                required
                            ></textarea>
                            <p v-if="form.errors.eventDescription" class="text-xs text-red-500">{{ form.errors.eventDescription }}</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">Syarat & Ketentuan (Satu baris per aturan)</label>
                            <textarea
                                v-model="form.eventRules"
                                rows="6"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none font-mono text-xs"
                                required
                            ></textarea>
                            <p v-if="form.errors.eventRules" class="text-xs text-red-500">{{ form.errors.eventRules }}</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t flex justify-end">
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-6 py-3 rounded-xl transition duration-200 flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <Spinner v-if="form.processing" />
                        <CheckCircle2 class="size-4" /> Simpan Pengaturan
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
