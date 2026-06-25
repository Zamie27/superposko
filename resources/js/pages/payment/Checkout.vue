<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { CreditCard, CheckCircle2, AlertTriangle, XCircle, ShieldCheck, Sparkles, Check, ArrowRight } from '@lucide/vue';
import { onMounted, ref, computed, onBeforeUnmount, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { useToast } from '@/composables/useToast';
import { dashboard } from '@/routes';

const props = defineProps<{
    packagePrice: number;
    packageStrikePrice: number;
    checkoutPaymentMethod: string;
    staticQrisUrl: string | null;
    existingRequest: {
        status: string;
        rejection_reason: string | null;
        created_at: string;
    } | null;
    tripayChannels?: Array<{
        code: string;
        name: string;
        icon_url: string;
        group: string;
        active: boolean;
    }>;
    activeTripayUrl: string | null;
    activeTripayRef: string | null;
}>();

const selectedMethod = ref<string>('');

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Beli Langganan',
                href: '/payment',
            },
        ],
    },
});

const isLoading = ref(false);
const transactionStatus = ref<'idle' | 'success' | 'pending' | 'error' | 'closed'>('idle');
const resultData = ref<any>(null);
const successMessage = ref('');
const countdown = ref(5);
const toast = useToast();

const fileInput = ref<HTMLInputElement | null>(null);
const previewUrl = ref<string | null>(null);

const qrisForm = useForm({
    name: '',
    email: '',
    whatsapp: '',
    payment_proof: null as File | null,
});

const formattedPrice = computed(() => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(props.packagePrice);
});

const formattedStrikePrice = computed(() => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(props.packageStrikePrice);
});

let pollingInterval: any = null;

const startPolling = () => {
    if (pollingInterval) clearInterval(pollingInterval);
    pollingInterval = setInterval(async () => {
        try {
            const response = await fetch('/payment/tripay/status');
            if (response.ok) {
                const res = await response.json();
                if (res.success && res.status === 'PAID') {
                    clearInterval(pollingInterval);
                    window.location.href = '/payment/tripay/return';
                }
            }
        } catch (e) {
            console.error('Error polling status:', e);
        }
    }, 4000);
};

watch(() => props.activeTripayUrl, (newVal) => {
    if (newVal) {
        startPolling();
    } else {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    }
}, { immediate: true });

onMounted(() => {
    if (props.checkoutPaymentMethod === 'qris_static') {
        // Init form with user data for QRIS
        const user = (window as any).Inertia?.page?.props?.auth?.user || {};
        qrisForm.name = user.name || '';
        qrisForm.email = user.email || '';
    }

    // Auto-select first active Tripay channel
    if (props.tripayChannels && props.tripayChannels.length > 0) {
        const hasQris = props.tripayChannels.find(c => c.code === 'QRIS');
        if (hasQris) {
            selectedMethod.value = 'QRIS';
        } else {
            selectedMethod.value = props.tripayChannels[0].code;
        }
    }
});

onBeforeUnmount(() => {
    if (pollingInterval) clearInterval(pollingInterval);
});

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;

    if (target.files && target.files.length > 0) {
        const file = target.files[0];
        qrisForm.payment_proof = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const triggerFileSelect = () => {
    fileInput.value?.click();
};

const submitQrisPayment = () => {
    qrisForm.post('/payment/qris', {
        forceFormData: true,
        onSuccess: () => {
            qrisForm.reset('whatsapp', 'payment_proof');
            previewUrl.value = null;
        },
    });
};



const getCookie = (name: string): string => {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);

    if (parts.length === 2) {
        return decodeURIComponent(parts.pop()?.split(';').shift() || '');
    }

    return '';
};

const handlePayment = async () => {
    if (props.activeTripayUrl) {
        window.open(props.activeTripayUrl, '_blank');
        return;
    }

    if (!selectedMethod.value) {
        toast.error('Silakan pilih metode pembayaran terlebih dahulu.');
        return;
    }

    // Pre-open blank tab to bypass browser popup blockers during async operations
    const paymentWindow = window.open('', '_blank');
    if (paymentWindow) {
        paymentWindow.document.write('<p style="font-family: sans-serif; text-align: center; margin-top: 20%; color: #64748b;">Membuka halaman pembayaran Tripay, mohon tunggu...</p>');
    }

    isLoading.value = true;

    try {
        const response = await fetch('/payment/tripay/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                method: selectedMethod.value
            })
        });

        if (!response.ok) {
            const errData = await response.json();
            throw new Error(errData.message || 'Gagal menghubungi server.');
        }

        const res = await response.json();

        if (res.success && res.data && res.data.checkout_url) {
            if (paymentWindow) {
                paymentWindow.location.href = res.data.checkout_url;
            } else {
                window.open(res.data.checkout_url, '_blank');
            }
            router.reload();
        } else {
            if (paymentWindow) {
                paymentWindow.close();
            }
            toast.error(res.message || 'Gagal mendapatkan tautan pembayaran Tripay.');
        }
    } catch (error: any) {
        if (paymentWindow) {
            paymentWindow.close();
        }
        toast.error(error.message || 'Terjadi kesalahan sistem.');
    } finally {
        isLoading.value = false;
    }
};

const handleCancelPayment = async () => {
    isLoading.value = true;
    try {
        const response = await fetch('/payment/tripay/cancel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error('Gagal membatalkan transaksi.');
        }

        toast.success('Transaksi sebelumnya dibatalkan.');
        router.reload();
    } catch (error: any) {
        toast.error(error.message || 'Terjadi kesalahan sistem.');
    } finally {
        isLoading.value = false;
    }
};

const features = [
    'E-Bendahara (Kas & Keuangan)',
    'Digital Logbook & Proker',
    'Manajemen Inventaris & Logistik',
    'Buku Kontak Desa',
    'Schedule Management (Piket & Agenda)',
    'Repository Proker (Lost & Found)',
    'Voting & Aspirasi',
    'Manajemen Barang Bawaan Pribadi',
    'Galeri Dokumentasi (Penyimpanan 20 GB)',
    'Menambahkan Anggota Sampai 20 Akun',
    'Support 24/7'
];
</script>

<template>
    <Head title="Beli Langganan - SuperPosko" />

    <div class="min-h-[calc(100vh-64px)] bg-slate-50/50 py-12 px-4 sm:px-6 lg:px-8 font-sans">
        <div class="max-w-4xl mx-auto space-y-8">
            <!-- Header Section -->
            <div class="text-center space-y-3">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-3.5 py-1 text-xs font-semibold text-sky-600 border border-sky-100 animate-pulse">
                    <Sparkles class="size-3.5 text-sky-500" /> Layanan Premium SuperPosko
                </span>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                    Aktifkan Fitur Kolaborasi Posko KKN Anda
                </h1>
                <p class="text-base text-slate-600 max-w-2xl mx-auto">
                    Kembangkan transparansi program kerja, administrasi keuangan, dan dokumentasi posko KKN dengan sekali transaksi praktis.
                </p>
            </div>

            <!-- Existing Subscription Request Status Box (QRIS manual only) -->
            <div v-if="checkoutPaymentMethod === 'qris_static' && existingRequest" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4 max-w-2xl mx-auto">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl" :class="{
                        'bg-amber-50 text-amber-600 border border-amber-100': existingRequest.status === 'pending',
                        'bg-green-50 text-green-600 border border-green-100': existingRequest.status === 'approved',
                        'bg-red-50 text-red-600 border border-red-100': existingRequest.status === 'rejected',
                    }">
                        <AlertTriangle v-if="existingRequest.status === 'pending'" class="size-6 animate-pulse" />
                        <CheckCircle2 v-else-if="existingRequest.status === 'approved'" class="size-6" />
                        <XCircle v-else class="size-6" />
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900">Status Pengajuan Aktivasi Langganan</h3>
                        <p class="text-xs text-slate-500">Diajukan pada {{ new Date(existingRequest.created_at).toLocaleDateString('id-ID', { dateStyle: 'long' }) }}</p>
                    </div>
                </div>

                <div class="p-4 rounded-xl border text-sm leading-relaxed" :class="{
                    'border-amber-200 bg-amber-50/30 text-amber-900': existingRequest.status === 'pending',
                    'border-green-200 bg-green-50/30 text-green-900': existingRequest.status === 'approved',
                    'border-red-200 bg-red-50/30 text-red-900': existingRequest.status === 'rejected',
                }">
                    <p v-if="existingRequest.status === 'pending'">
                        <strong>Tinjauan Pembayaran:</strong> Bukti transfer Anda sedang dalam verifikasi admin. Akun Anda akan otomatis diaktifkan menjadi Host segera setelah verifikasi disetujui (biasanya kurang dari 24 jam).
                    </p>
                    <p v-else-if="existingRequest.status === 'approved'">
                        <strong>Aktivasi Disetujui!</strong> Akun Anda berhasil dipromosikan menjadi Host. Silakan kembali ke dashboard untuk menikmati semua fitur premium.
                    </p>
                    <div v-else class="space-y-2">
                        <p class="font-bold text-red-900">Verifikasi Gagal!</p>
                        <p class="text-xs text-red-700 leading-relaxed">
                            Bukti transfer Anda tidak dapat divalidasi oleh admin. Alasan penolakan:
                        </p>
                        <div class="p-3 bg-red-50 rounded-lg border border-red-100 text-xs font-semibold text-red-950 mt-1">
                            {{ existingRequest.rejection_reason || 'Tidak ada alasan spesifik yang diberikan oleh admin.' }}
                        </div>
                        <p class="text-xs text-red-700 mt-2 font-semibold">
                            Silakan lakukan pengecekan ulang dan kirim kembali formulir pendaftaran di bawah dengan menyertakan bukti transfer yang valid.
                        </p>
                    </div>
                </div>

                <div class="pt-2 flex justify-end" v-if="existingRequest.status === 'approved'">
                    <Link href="/dashboard" class="rounded-xl bg-[#38BDF8] text-white font-bold px-6 py-2.5 hover:bg-[#38BDF8]/90 transition duration-200">
                        Masuk ke Dashboard
                    </Link>
                </div>
            </div>

            <!-- Main Layout Split Grid -->
            <div v-if="checkoutPaymentMethod === 'tripay' || !existingRequest || existingRequest.status === 'rejected'" class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                
                <!-- Left Details / Features list (7 cols on large screen) -->
                <div class="md:col-span-7 space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-6">
                        <h2 class="text-lg font-bold text-slate-950 flex items-center gap-2">
                            <ShieldCheck class="size-5.5 text-sky-500" />
                            Fitur Premium Yang Didapatkan
                        </h2>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div v-for="(feature, idx) in features" :key="idx" class="flex gap-3 items-center">
                                <div class="flex-none rounded-full bg-sky-50 p-1 border border-sky-100">
                                    <Check class="size-3.5 text-sky-600 font-bold" />
                                </div>
                                <span class="text-sm font-medium text-slate-700 leading-relaxed">{{ feature }}</span>
                            </div>
                        </div>

                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 text-xs text-slate-500 leading-relaxed flex gap-3">
                            <Sparkles class="size-5 text-amber-500 flex-shrink-0 mt-0.5" />
                            <div>
                                <span class="font-bold text-slate-700">Masa Aktif 40 Hari:</span> 
                                Cukup untuk mendampingi seluruh siklus dan masa bakti kelompok KKN Anda dari awal penerjunan hingga penarikan.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Card / Pricing & Checkout (5 cols on large screen) -->
                <div class="md:col-span-5 space-y-6">
                    <!-- Tripay Flow -->
                    <div v-if="checkoutPaymentMethod === 'tripay'" class="bg-white rounded-2xl border border-slate-200/85 p-6 shadow-sm relative overflow-hidden flex flex-col space-y-6">
                        <!-- Premium gradient corner accent -->
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-sky-400/20 to-transparent pointer-events-none rounded-bl-full"></div>

                        <div class="space-y-1 pb-4 border-b border-slate-100">
                            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider">Premium Access</h3>
                            <h4 class="text-xl font-extrabold text-slate-900">Paket Langganan Posko</h4>
                            <p class="text-xs text-slate-400">1x Aktivasi untuk 1 Kelompok KKN</p>
                        </div>

                        <!-- Pending Payment Alert -->
                        <div v-if="activeTripayUrl" class="bg-amber-50/70 border border-amber-200 rounded-xl p-4 text-xs text-amber-900 leading-relaxed flex gap-2.5">
                            <AlertTriangle class="size-4 shrink-0 text-amber-600 mt-0.5" />
                            <div>
                                <span class="font-bold">Tagihan Belum Dibayar:</span> Anda memiliki transaksi Tripay yang sedang aktif (Ref: <code class="font-mono bg-amber-100 px-1 py-0.5 rounded text-[10px]">{{ activeTripayRef }}</code>). Silakan selesaikan pembayaran Anda.
                            </div>
                        </div>

                        <!-- Payment Method Selector -->
                        <div class="space-y-3" :class="{ 'opacity-65 pointer-events-none': activeTripayUrl }">
                            <label class="text-xs font-bold text-slate-700 block">Pilih Metode Pembayaran:</label>
                            <div v-if="tripayChannels && tripayChannels.length > 0" class="space-y-2.5">
                                <button
                                    v-for="channel in tripayChannels"
                                    :key="channel.code"
                                    type="button"
                                    @click="selectedMethod = channel.code"
                                    :class="[
                                        'w-full flex items-center justify-between p-3.5 rounded-xl border text-left transition-all duration-200',
                                        selectedMethod === channel.code
                                            ? 'border-sky-500 bg-sky-50/40 ring-1 ring-sky-500 scale-[1.01]'
                                            : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50/50'
                                    ]"
                                >
                                    <div class="flex items-center gap-3">
                                        <img :src="channel.icon_url" :alt="channel.name" class="h-5 w-auto object-contain bg-white rounded p-0.5 border" />
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-slate-900">{{ channel.name }}</span>
                                            <span class="text-[10px] text-slate-400">{{ channel.group }}</span>
                                        </div>
                                    </div>
                                    <div class="size-4.5 rounded-full border flex items-center justify-center" :class="selectedMethod === channel.code ? 'border-sky-500 bg-sky-500 text-white' : 'border-slate-300'">
                                        <Check v-if="selectedMethod === channel.code" class="size-3 font-bold" />
                                    </div>
                                </button>
                            </div>
                            <div v-else class="text-xs text-slate-400 py-2">
                                Tidak ada metode pembayaran Tripay yang aktif.
                            </div>
                        </div>

                        <!-- Pricing Display -->
                        <div class="py-4 border-t border-b border-slate-100 space-y-2">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm text-slate-400 line-through font-medium">{{ formattedStrikePrice }}</span>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-4xl font-extrabold text-slate-900 tracking-tight">{{ formattedPrice }}</span>
                                    <span class="text-xs text-slate-400 font-medium">/ 40 hari</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500">Harga final termasuk PPN. Bebas biaya tersembunyi.</p>
                        </div>

                        <!-- Action Button -->
                        <div class="space-y-3">
                            <Button
                                @click="handlePayment"
                                :disabled="isLoading || transactionStatus === 'success' || (!selectedMethod && !activeTripayUrl)"
                                class="w-full bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white font-bold py-4 rounded-xl transition duration-200 flex items-center justify-center gap-2.5 shadow-sm text-sm"
                            >
                                <Spinner v-if="isLoading" class="size-4" />
                                <CreditCard v-else class="size-4" />
                                {{ activeTripayUrl ? 'Lanjutkan Pembayaran' : 'Bayar Sekarang' }}
                            </Button>
                            
                            <button
                                v-if="activeTripayUrl"
                                @click="handleCancelPayment"
                                :disabled="isLoading"
                                type="button"
                                class="w-full text-center text-xs font-semibold text-red-500 hover:text-red-600 transition py-1 hover:underline mt-2 flex items-center justify-center gap-1.5"
                            >
                                <Spinner v-if="isLoading" class="size-3" />
                                Batalkan & Ganti Metode Pembayaran
                            </button>
                            
                            <div class="flex items-center justify-center gap-1.5 text-[10px] text-slate-400">
                                <ShieldCheck class="size-3.5 text-slate-400" />
                                Transaksi aman & terenkripsi oleh Tripay
                            </div>
                        </div>
                    </div>

                    <!-- Static QRIS Manual Flow -->
                    <div v-else class="bg-white rounded-2xl border border-slate-200/85 p-6 shadow-sm relative overflow-hidden flex flex-col space-y-5">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-sky-400/20 to-transparent pointer-events-none rounded-bl-full"></div>

                        <div class="space-y-1 pb-4 border-b border-slate-100">
                            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider">Manual Activation</h3>
                            <h4 class="text-xl font-extrabold text-slate-900">Scan QRIS & Unggah Bukti</h4>
                            <p class="text-xs text-slate-400">Transfer manual melalui QRIS e-wallet resmi</p>
                        </div>

                        <!-- Pricing Display -->
                        <div class="space-y-2">
                            <div class="flex flex-col gap-0.5">
                                <span class="text-xs text-slate-400 line-through font-medium">{{ formattedStrikePrice }}</span>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ formattedPrice }}</span>
                                    <span class="text-xs text-slate-400 font-medium">/ 40 hari</span>
                                </div>
                            </div>
                        </div>

                        <!-- QRIS Code Container -->
                        <div class="rounded-xl border p-2 bg-slate-50 flex flex-col items-center justify-center space-y-2">
                            <img :src="staticQrisUrl || '/images/qris.jpg'" alt="QRIS SuperPosko" class="max-w-[180px] h-auto rounded-lg shadow-sm border bg-white" />
                        </div>

                        <!-- Submission Fields -->
                        <form @submit.prevent="submitQrisPayment" class="space-y-4">
                            <!-- Name -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                    <CreditCard class="size-3.5 text-slate-400" /> Nama Lengkap
                                </label>
                                <input
                                    v-model="qrisForm.name"
                                    type="text"
                                    placeholder="Nama Lengkap"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                    required
                                />
                                <p v-if="qrisForm.errors.name" class="text-[11px] text-red-500">{{ qrisForm.errors.name }}</p>
                            </div>

                            <!-- Email -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                    <CreditCard class="size-3.5 text-slate-400" /> Alamat Email
                                </label>
                                <input
                                    v-model="qrisForm.email"
                                    type="email"
                                    placeholder="nama@email.com"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                    required
                                />
                                <p v-if="qrisForm.errors.email" class="text-[11px] text-red-500">{{ qrisForm.errors.email }}</p>
                            </div>

                            <!-- WhatsApp -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                    <CreditCard class="size-3.5 text-slate-400" /> Nomor WhatsApp
                                </label>
                                <input
                                    v-model="qrisForm.whatsapp"
                                    type="text"
                                    placeholder="Contoh: 08123456789"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                    required
                                />
                                <p v-if="qrisForm.errors.whatsapp" class="text-[11px] text-red-500">{{ qrisForm.errors.whatsapp }}</p>
                            </div>

                            <!-- Payment Proof -->
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                    <CreditCard class="size-3.5 text-slate-400" /> Bukti Transfer (Screenshot)
                                </label>
                                
                                <div 
                                    @click="triggerFileSelect"
                                    class="border border-dashed border-slate-200 rounded-xl p-4 text-center cursor-pointer hover:bg-slate-50/50 transition flex flex-col items-center justify-center gap-2"
                                >
                                    <input 
                                        ref="fileInput" 
                                        type="file" 
                                        class="hidden" 
                                        accept="image/*"
                                        @change="handleFileChange"
                                    />
                                    
                                    <template v-if="!previewUrl">
                                        <CreditCard class="size-6 text-slate-400" />
                                        <span class="text-xs font-medium text-slate-600">Klik untuk unggah gambar</span>
                                        <span class="text-[9px] text-slate-400">PNG, JPG, JPEG (Maks. 2MB)</span>
                                    </template>
                                    <template v-else>
                                        <img :src="previewUrl" alt="Bukti Transfer" class="max-h-24 object-contain rounded-lg border shadow-sm" />
                                        <span class="text-[10px] text-sky-600 font-semibold underline">Ganti Gambar</span>
                                    </template>
                                </div>
                                <p v-if="qrisForm.errors.payment_proof" class="text-[11px] text-red-500">{{ qrisForm.errors.payment_proof }}</p>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-2">
                                <Button
                                    type="submit"
                                    :disabled="qrisForm.processing"
                                    class="w-full bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white font-bold py-3.5 rounded-xl transition duration-200 flex items-center justify-center gap-2 shadow-sm text-sm"
                                >
                                    <Spinner v-if="qrisForm.processing" class="size-4" />
                                    Kirim Pengajuan Langganan
                                </Button>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>
</template>
