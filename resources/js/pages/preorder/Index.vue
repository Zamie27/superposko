<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { CheckCircle, AlertCircle, Upload, Phone, Mail, User, ShieldCheck, Sparkles, Check } from '@lucide/vue';
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { dashboard } from '@/routes';

const props = defineProps<{
    preorderPrice: number;
    preorderStrikePrice: number;
    activeTripayUrl: string | null;
    activeTripayRef: string | null;
    checkoutPaymentMethod: string;
    staticQrisUrl: string | null;
    existingPreorder: {
        status: string;
        created_at: string;
    } | null;
    tripayChannels?: Array<{
        code: string;
        name: string;
        icon_url: string;
        group: string;
    }>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Preorder Langganan',
                href: '/preorder',
            },
        ],
    },
});

const fileInput = ref<HTMLInputElement | null>(null);
const previewUrl = ref<string | null>(null);

const form = useForm({
    name: '',
    email: '',
    whatsapp: '',
    payment_proof: null as File | null,
});

const selectedMethod = ref<string>('');
const isLoading = ref(false);
const toast = ref<any>(null); // Fallback for alerts or toast

// Import toast hook
import { useToast } from '@/composables/useToast';
const mainToast = useToast();

const formattedPrice = computed(() => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(props.preorderPrice);
});

const formattedStrikePrice = computed(() => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(props.preorderStrikePrice);
});

const initForm = () => {
     
    const user = (window as any).Inertia?.page?.props?.auth?.user || {};
    form.name = user.name || '';
    form.email = user.email || '';
};

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
        initForm();
        selectedMethod.value = 'qris_static';
    } else {
        // Auto-select first active Tripay channel
        if (props.tripayChannels && props.tripayChannels.length > 0) {
            const hasQris = props.tripayChannels.find(c => c.code === 'QRIS');
            if (hasQris) {
                selectedMethod.value = 'QRIS';
            } else {
                selectedMethod.value = props.tripayChannels[0].code;
            }
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
        form.payment_proof = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const triggerFileSelect = () => {
    fileInput.value?.click();
};

const submitQrisPreorder = () => {
    if (!form.name || !form.email || !form.whatsapp) {
        mainToast.error('Silakan lengkapi formulir pendaftaran terlebih dahulu.');
        return;
    }
    if (!form.payment_proof) {
        mainToast.error('Silakan unggah bukti transfer terlebih dahulu.');
        return;
    }

    form.post('/preorder', {
        forceFormData: true,
        onSuccess: () => {
            form.reset('whatsapp', 'payment_proof');
            previewUrl.value = null;
            mainToast.success('Form Preorder berhasil dikirim! Silakan tunggu konfirmasi Admin.');
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
        mainToast.error('Silakan pilih metode pembayaran terlebih dahulu.');
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
                type: 'preorder',
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
            window.location.reload();
        } else {
            if (paymentWindow) {
                paymentWindow.close();
            }
            mainToast.error(res.message || 'Gagal mendapatkan tautan pembayaran Tripay.');
        }
    } catch (error: any) {
        if (paymentWindow) {
            paymentWindow.close();
        }
        mainToast.error(error.message || 'Terjadi kesalahan sistem.');
    } finally {
        isLoading.value = false;
    }
};

const handleSubmit = () => {
    if (props.checkoutPaymentMethod === 'qris_static') {
        submitQrisPreorder();
    } else {
        handlePayment();
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
            body: JSON.stringify({
                type: 'preorder'
            })
        });

        if (!response.ok) {
            throw new Error('Gagal membatalkan transaksi.');
        }

        mainToast.success('Transaksi sebelumnya dibatalkan.');
        // Clear cached state for fast UI feedback
        window.location.reload();
    } catch (error: any) {
        mainToast.error(error.message || 'Terjadi kesalahan sistem.');
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
    <Head title="Preorder Langganan - SuperPosko" />

    <div class="min-h-[calc(100vh-64px)] bg-slate-50/50 py-12 px-4 sm:px-6 lg:px-8 font-sans">
        <div class="max-w-4xl mx-auto space-y-8">
            <!-- Header Section -->
            <div class="text-center space-y-3">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-3.5 py-1 text-xs font-semibold text-sky-600 border border-sky-100 animate-pulse">
                    <Sparkles class="size-3.5 text-sky-500" /> Promosi Preorder SuperPosko
                </span>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                    Amankan Akses Posko KKN Lebih Awal
                </h1>
                <p class="text-base text-slate-600 max-w-2xl mx-auto">
                    Dapatkan potongan harga khusus preorder sebelum website resmi diluncurkan. Layanan siap aktif untuk seluruh anggota kelompok Anda.
                </p>
            </div>

            <!-- Existing Preorder Status Box -->
            <div v-if="existingPreorder" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4 max-w-2xl mx-auto">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl" :class="{
                        'bg-amber-50 text-amber-600 border border-amber-100': existingPreorder.status === 'pending',
                        'bg-green-50 text-green-600 border border-green-100': existingPreorder.status === 'approved',
                        'bg-red-50 text-red-600 border border-red-100': existingPreorder.status === 'rejected',
                    }">
                        <AlertCircle v-if="existingPreorder.status === 'pending'" class="size-6 animate-pulse" />
                        <CheckCircle v-else-if="existingPreorder.status === 'approved'" class="size-6" />
                        <XCircle v-else class="size-6" />
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900">Status Pendaftaran Preorder Anda</h3>
                        <p class="text-xs text-slate-500">Diajukan pada {{ new Date(existingPreorder.created_at).toLocaleDateString('id-ID', { dateStyle: 'long' }) }}</p>
                    </div>
                </div>

                <div class="p-4 rounded-xl border text-sm leading-relaxed" :class="{
                    'border-amber-200 bg-amber-50/30 text-amber-900': existingPreorder.status === 'pending',
                    'border-green-200 bg-green-50/30 text-green-900': existingPreorder.status === 'approved',
                    'border-red-200 bg-red-50/30 text-red-900': existingPreorder.status === 'rejected',
                }">
                    <p v-if="existingPreorder.status === 'pending'">
                        <strong>Tinjauan Pembayaran:</strong> Bukti transfer Anda sedang dalam verifikasi admin. Akun Anda akan otomatis diaktifkan menjadi Host segera setelah verifikasi disetujui (biasanya kurang dari 24 jam).
                    </p>
                    <p v-else-if="existingPreorder.status === 'approved'">
                        <strong>Preorder Disetujui!</strong> Akun Anda berhasil dipromosikan menjadi Host. Silakan kembali ke dashboard untuk menikmati semua fitur premium.
                    </p>
                    <div v-else class="space-y-2">
                        <p class="font-bold text-red-900">Verifikasi Gagal!</p>
                        <p class="text-xs text-red-700 leading-relaxed">
                            Bukti transfer Anda tidak dapat divalidasi oleh admin. Beberapa faktor umum penolakan meliputi:
                        </p>
                        <ul class="list-disc list-inside text-xs text-red-700 space-y-1 pl-1.5">
                            <li>Gambar bukti transfer buram, terpotong, atau tidak terbaca dengan jelas.</li>
                            <li>Nominal transfer tidak sesuai dengan harga preorder promo ({{ formattedPrice }}).</li>
                            <li>Tanggal/waktu transaksi transfer tidak valid atau sudah kedaluwarsa.</li>
                            <li>Tujuan transfer rekening/e-wallet tidak cocok dengan QRIS resmi kami.</li>
                        </ul>
                        <p class="text-xs text-red-700 mt-2 font-semibold">
                            Silakan lakukan pengecekan ulang dan kirim kembali formulir pendaftaran di bawah dengan menyertakan bukti transfer yang valid.
                        </p>
                    </div>
                </div>

                <div class="pt-2 flex justify-end" v-if="existingPreorder.status === 'approved'">
                    <Link :href="dashboard()" class="rounded-xl bg-[#38BDF8] text-white font-bold px-6 py-2.5 hover:bg-[#38BDF8]/90 transition duration-200">
                        Masuk ke Dashboard
                    </Link>
                </div>
            </div>

            <!-- Form Preorder Layout Split Grid -->
            <div v-if="!existingPreorder || existingPreorder.status === 'rejected'" class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                
                <!-- Left Side: Unlocked Features (7 cols) -->
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
                                <span class="font-bold text-slate-700">Akses Tanpa Batas:</span> 
                                Sekali bayar untuk seluruh kelompok posko Anda selama program kerja KKN berlangsung (40 hari penuh).
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Pendaftaran & Pembayaran Tripay Form (5 cols) -->
                <div class="md:col-span-5 space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-200/85 p-6 shadow-sm relative overflow-hidden flex flex-col space-y-6">
                        <!-- Premium gradient corner accent -->
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-sky-400/20 to-transparent pointer-events-none rounded-bl-full"></div>

                        <div class="space-y-1 pb-4 border-b border-slate-100">
                            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider">Preorder Promo</h3>
                            <h4 class="text-xl font-extrabold text-slate-900">Daftar & Bayar</h4>
                            <p class="text-xs text-slate-400">Isi formulir pendaftaran dan selesaikan pembayaran aman via Tripay</p>
                        </div>

                        <!-- Pending Payment Alert -->
                        <div v-if="activeTripayUrl" class="bg-amber-50/70 border border-amber-200 rounded-xl p-4 text-xs text-amber-900 leading-relaxed flex gap-2.5">
                            <AlertCircle class="size-4 shrink-0 text-amber-600 mt-0.5" />
                            <div>
                                <span class="font-bold">Tagihan Belum Dibayar:</span> Anda memiliki transaksi Tripay yang sedang aktif (Ref: <code class="font-mono bg-amber-100 px-1 py-0.5 rounded text-[10px]">{{ activeTripayRef }}</code>). Silakan selesaikan pembayaran Anda.
                            </div>
                        </div>

                        <!-- Pricing Display -->
                        <div class="space-y-2">
                            <div class="flex flex-col gap-1">
                                <span class="text-sm text-slate-400 line-through font-medium">{{ formattedStrikePrice }}</span>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-4xl font-extrabold text-slate-900 tracking-tight">{{ formattedPrice }}</span>
                                    <span class="text-xs text-slate-400 font-medium">/ 40 hari</span>
                                </div>
                            </div>
                        </div>

                        <!-- Submission Fields -->
                        <form @submit.prevent="handleSubmit" class="space-y-4 pt-2">
                            <!-- Static QRIS Manual Flow (only if checkoutPaymentMethod is qris_static) -->
                            <template v-if="checkoutPaymentMethod === 'qris_static'">
                                <!-- QRIS Code Container -->
                                <div class="rounded-xl border p-2 bg-slate-50 flex flex-col items-center justify-center space-y-2">
                                    <img :src="staticQrisUrl || '/images/qris.jpg'" alt="QRIS SuperPosko" class="max-w-[180px] h-auto rounded-lg shadow-sm border bg-white" />
                                </div>

                                <!-- Name -->
                                <div class="space-y-1.5" :class="{ 'opacity-65 pointer-events-none': activeTripayUrl }">
                                    <label class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                        <User class="size-3.5 text-slate-400" /> Nama Lengkap
                                    </label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        placeholder="Nama Lengkap"
                                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                        required
                                        :disabled="!!activeTripayUrl"
                                    />
                                    <p v-if="form.errors.name" class="text-[11px] text-red-500">{{ form.errors.name }}</p>
                                </div>

                                <!-- Email -->
                                <div class="space-y-1.5" :class="{ 'opacity-65 pointer-events-none': activeTripayUrl }">
                                    <label class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                        <Mail class="size-3.5 text-slate-400" /> Alamat Email
                                    </label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        placeholder="nama@email.com"
                                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                        required
                                        :disabled="!!activeTripayUrl"
                                    />
                                    <p v-if="form.errors.email" class="text-[11px] text-red-500">{{ form.errors.email }}</p>
                                </div>

                                <!-- WhatsApp -->
                                <div class="space-y-1.5" :class="{ 'opacity-65 pointer-events-none': activeTripayUrl }">
                                    <label class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                        <Phone class="size-3.5 text-slate-400" /> Nomor WhatsApp
                                    </label>
                                    <input
                                        v-model="form.whatsapp"
                                        type="text"
                                        placeholder="Contoh: 08123456789"
                                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                        required
                                        :disabled="!!activeTripayUrl"
                                    />
                                    <p v-if="form.errors.whatsapp" class="text-[11px] text-red-500">{{ form.errors.whatsapp }}</p>
                                </div>

                                <!-- Payment Proof -->
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                        <Upload class="size-3.5 text-slate-400" /> Bukti Transfer (Screenshot)
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
                                            <Upload class="size-6 text-slate-400" />
                                            <span class="text-xs font-medium text-slate-600">Klik untuk unggah gambar</span>
                                            <span class="text-[9px] text-slate-400">PNG, JPG, JPEG (Maks. 2MB)</span>
                                        </template>
                                        <template v-else>
                                            <img :src="previewUrl" alt="Bukti Transfer" class="max-h-24 object-contain rounded-lg border shadow-sm" />
                                            <span class="text-[10px] text-sky-600 font-semibold underline">Ganti Gambar</span>
                                        </template>
                                    </div>
                                    <p v-if="form.errors.payment_proof" class="text-[11px] text-red-500">{{ form.errors.payment_proof }}</p>
                                </div>
                            </template>

                            <!-- Tripay Flow (only if checkoutPaymentMethod is tripay) -->
                            <template v-else>
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
                            </template>

                            <!-- Submit Button -->
                            <div class="pt-2 space-y-3">
                                <Button
                                    type="submit"
                                    :disabled="isLoading || form.processing || (!selectedMethod && !activeTripayUrl)"
                                    class="w-full bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white font-bold py-3.5 rounded-xl transition duration-200 flex items-center justify-center gap-2 shadow-sm text-sm"
                                >
                                    <Spinner v-if="isLoading || form.processing" class="size-4" />
                                    <ShieldCheck v-else class="size-4" />
                                    {{ activeTripayUrl ? 'Lanjutkan Pembayaran' : (checkoutPaymentMethod === 'qris_static' ? 'Kirim Pengajuan Preorder' : 'Bayar Sekarang') }}
                                </Button>

                                <button
                                    v-if="activeTripayUrl"
                                    @click="handleCancelPayment"
                                    :disabled="isLoading"
                                    type="button"
                                    class="w-full text-center text-xs font-semibold text-red-500 hover:text-red-600 transition py-1 hover:underline mt-1"
                                >
                                    Batalkan & Ganti Metode Pembayaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
