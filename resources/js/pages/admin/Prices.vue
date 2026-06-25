<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { CreditCard, ArrowLeft, CheckCircle2, AlertTriangle, QrCode, Upload, Trash2 } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';

const props = defineProps<{
    pricing: {
        packageName: string;
        packagePrice: number;
        packageStrikePrice: number;
        packageDescription: string;
        preorderPrice: number;
        preorderStrikePrice: number;
        preorderPromoActive: boolean;
        checkoutPaymentMethod: string;
        staticQrisPath: string | null;
        staticQrisUrl: string | null;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard Admin', href: '/admin/dashboard' },
            { title: 'Manajemen Harga', href: '/admin/prices' },
        ],
    },
});

const form = useForm({
    packageName: props.pricing.packageName,
    packagePrice: props.pricing.packagePrice,
    packageStrikePrice: props.pricing.packageStrikePrice,
    packageDescription: props.pricing.packageDescription,
    preorderPrice: props.pricing.preorderPrice,
    preorderStrikePrice: props.pricing.preorderStrikePrice,
    preorderPromoActive: props.pricing.preorderPromoActive,
    checkoutPaymentMethod: props.pricing.checkoutPaymentMethod,
});

const submitForm = () => {
    form.put('/admin/prices', {
        preserveScroll: true,
    });
};

const qrisForm = useForm({
    qris_image: null as File | null,
});

const qrisInput = ref<HTMLInputElement | null>(null);
const qrisPreview = ref<string | null>(props.pricing.staticQrisUrl || null);

const handleQrisChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        qrisForm.qris_image = file;
        const reader = new FileReader();
        reader.onload = (event) => {
            qrisPreview.value = event.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const uploadQris = () => {
    qrisForm.post('/admin/prices/qris', {
        preserveScroll: true,
        onSuccess: () => {
            qrisForm.reset();
            if (qrisInput.value) {
                qrisInput.value.value = '';
            }
        },
    });
};

const deleteQris = () => {
    if (confirm('Apakah Anda yakin ingin menghapus QRIS statis ini?')) {
        qrisForm.delete('/admin/prices/qris', {
            preserveScroll: true,
            onSuccess: () => {
                qrisPreview.value = null;
                qrisForm.reset();
                if (qrisInput.value) {
                    qrisInput.value.value = '';
                }
            },
        });
    }
};
</script>

<template>
    <Head title="Manajemen Harga - Admin" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-4xl mx-auto font-sans">
        <div class="flex items-center gap-3">
            <Link href="/admin/dashboard" class="rounded-lg p-2 hover:bg-slate-100 text-slate-500 transition-colors">
                <ArrowLeft class="size-5" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Konfigurasi Harga Platform</h1>
                <p class="text-sm text-slate-500">Atur nominal langganan (SaaS Utama & Preorder). Nominal di sini akan merubah nominal pembayaran Midtrans secara langsung.</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Section 1: Paket Posko Utama -->
                <div class="space-y-4">
                    <h3 class="text-base font-bold text-slate-900 border-b pb-2 flex items-center gap-2">
                        <CreditCard class="size-5 text-sky-500" /> Paket Langganan Utama (SaaS KKN)
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">Nama Paket</label>
                            <input
                                v-model="form.packageName"
                                type="text"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                                required
                            />
                            <p v-if="form.errors.packageName" class="text-xs text-red-500">{{ form.errors.packageName }}</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">Harga Jual Paket (Rp)</label>
                            <input
                                v-model.number="form.packagePrice"
                                type="number"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                                required
                            />
                            <p v-if="form.errors.packagePrice" class="text-xs text-red-500">{{ form.errors.packagePrice }}</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">Harga Coret / Asli (Rp)</label>
                            <input
                                v-model.number="form.packageStrikePrice"
                                type="number"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                                required
                            />
                            <p v-if="form.errors.packageStrikePrice" class="text-xs text-red-500">{{ form.errors.packageStrikePrice }}</p>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Deskripsi Singkat Paket</label>
                        <textarea
                            v-model="form.packageDescription"
                            rows="3"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                            required
                        ></textarea>
                        <p v-if="form.errors.packageDescription" class="text-xs text-red-500">{{ form.errors.packageDescription }}</p>
                    </div>

                    <div class="space-y-3 pt-2">
                        <label class="text-xs font-semibold text-slate-700 block">Metode Pembayaran Checkout Utama</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label 
                                class="flex items-center gap-3 p-3.5 border rounded-xl cursor-pointer transition-all duration-200"
                                :class="form.checkoutPaymentMethod === 'midtrans' ? 'border-sky-500 bg-sky-50/30' : 'border-slate-200 hover:bg-slate-50'"
                            >
                                <input 
                                    type="radio" 
                                    value="midtrans" 
                                    v-model="form.checkoutPaymentMethod"
                                    class="text-sky-500 focus:ring-sky-500"
                                />
                                <div>
                                    <div class="text-sm font-bold text-slate-900">Midtrans Gateway</div>
                                    <div class="text-[11px] text-slate-500">Otomatis via QRIS/E-Wallet/VA Midtrans</div>
                                </div>
                            </label>
                            <label 
                                class="flex items-center gap-3 p-3.5 border rounded-xl cursor-pointer transition-all duration-200"
                                :class="form.checkoutPaymentMethod === 'qris_static' ? 'border-sky-500 bg-sky-50/30' : 'border-slate-200 hover:bg-slate-50'"
                            >
                                <input 
                                    type="radio" 
                                    value="qris_static" 
                                    v-model="form.checkoutPaymentMethod"
                                    class="text-sky-500 focus:ring-sky-500"
                                />
                                <div>
                                    <div class="text-sm font-bold text-slate-900">QRIS Statis (Manual)</div>
                                    <div class="text-[11px] text-slate-500">User upload bukti, admin validasi manual</div>
                                </div>
                            </label>
                        </div>
                        <p v-if="form.errors.checkoutPaymentMethod" class="text-xs text-red-500">{{ form.errors.checkoutPaymentMethod }}</p>
                    </div>
                </div>

                <!-- Section 2: Preorder Pricing -->
                <div class="space-y-4 pt-4 border-t">
                    <h3 class="text-base font-bold text-slate-900 border-b pb-2 flex items-center gap-2">
                        <CreditCard class="size-5 text-purple-500" /> Paket Promosi Preorder
                    </h3>

                    <div class="flex items-center justify-between p-4 bg-purple-50/50 border border-purple-100 rounded-xl mb-4">
                        <div class="space-y-0.5">
                            <label class="text-sm font-semibold text-slate-900">Status Promo Preorder</label>
                            <p class="text-xs text-slate-500">Aktifkan untuk menampilkan form preorder bagi pengguna non-aktif. Nonaktifkan untuk mengarahkan pengguna langsung ke pembayaran utama.</p>
                        </div>
                        <button
                            type="button"
                            @click="form.preorderPromoActive = !form.preorderPromoActive"
                            :class="form.preorderPromoActive ? 'bg-purple-600' : 'bg-slate-200'"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                        >
                            <span
                                :class="form.preorderPromoActive ? 'translate-x-5' : 'translate-x-0'"
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                            ></span>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">Harga Jual Preorder (Rp)</label>
                            <input
                                v-model.number="form.preorderPrice"
                                type="number"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                                required
                            />
                            <p v-if="form.errors.preorderPrice" class="text-xs text-red-500">{{ form.errors.preorderPrice }}</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">Harga Coret Preorder (Rp)</label>
                            <input
                                v-model.number="form.preorderStrikePrice"
                                type="number"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                                required
                            />
                            <p v-if="form.errors.preorderStrikePrice" class="text-xs text-red-500">{{ form.errors.preorderStrikePrice }}</p>
                        </div>
                    </div>
                </div>

                <!-- Alerts info -->
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex gap-3 text-amber-900 text-xs leading-relaxed">
                    <AlertTriangle class="size-5 shrink-0 text-amber-600" />
                    <div>
                        <strong>Catatan Penting:</strong> Mengubah harga di atas akan langsung mengubah nominal checkout pembayaran digital Midtrans (API transaksi Sandbox & Production) serta tampilan informasi harga diskon pada Landing Page / Halaman Depan.
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
                        <CheckCircle2 class="size-4" /> Simpan Konfigurasi Harga
                    </Button>
                </div>
            </form>
        </div>

        <!-- Section 3: QRIS Statis Pembayaran -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 border-b pb-2 flex items-center gap-2 mb-4">
                <QrCode class="size-5 text-emerald-500" /> QRIS Statis Pembayaran (Alternatif)
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                <!-- Preview QRIS -->
                <div class="flex flex-col items-center justify-center p-4 border border-dashed rounded-xl bg-slate-50 min-h-[250px]">
                    <div v-if="qrisPreview" class="flex flex-col items-center gap-3">
                        <img :src="qrisPreview" alt="QRIS Statis" class="max-h-64 object-contain rounded-lg border shadow-sm" />
                        <Button
                            type="button"
                            variant="destructive"
                            size="sm"
                            @click="deleteQris"
                            class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white"
                        >
                            <Trash2 class="size-4" /> Hapus QRIS
                        </Button>
                    </div>
                    <div v-else class="text-center p-6 text-slate-400 flex flex-col items-center gap-2">
                        <QrCode class="size-12 text-slate-300" />
                        <p class="text-sm font-medium">Belum ada QRIS statis diunggah</p>
                        <p class="text-xs">QRIS ini akan ditampilkan sebagai alternatif pembayaran manual bagi pengguna.</p>
                    </div>
                </div>

                <!-- Form Upload -->
                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Pilih Berkas QRIS</label>
                        <input
                            ref="qrisInput"
                            type="file"
                            accept="image/jpeg,image/png,image/jpg"
                            @change="handleQrisChange"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100"
                        />
                        <p class="text-[10px] text-slate-400">Mendukung format JPG, JPEG, PNG. Ukuran maksimal 2MB.</p>
                        <p v-if="qrisForm.errors.qris_image" class="text-xs text-red-500">{{ qrisForm.errors.qris_image }}</p>
                    </div>

                    <Button
                        type="button"
                        :disabled="!qrisForm.qris_image || qrisForm.processing"
                        @click="uploadQris"
                        class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold w-full rounded-xl transition duration-200 flex items-center justify-center gap-2"
                    >
                        <Spinner v-if="qrisForm.processing" />
                        <Upload class="size-4" /> Unggah QRIS Baru
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
