<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { CreditCard, CheckCircle2, AlertTriangle, XCircle, ShieldCheck, Sparkles, Check, ArrowRight } from '@lucide/vue';
import { onMounted, ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { dashboard } from '@/routes';
import { useToast } from '@/composables/useToast';

const props = defineProps<{
    midtransClientKey: string;
    isProduction: boolean;
    packagePrice: number;
    packageStrikePrice: number;
}>();

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

// Load Midtrans Snap JS dynamically
onMounted(() => {
    const snapUrl = props.isProduction
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';

    if (!document.querySelector(`script[src="${snapUrl}"]`)) {
        const script = document.createElement('script');
        script.src = snapUrl;
        script.setAttribute('data-client-key', props.midtransClientKey);
        script.async = true;
        document.head.appendChild(script);
    }
});

const getCookie = (name: string): string => {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);

    if (parts.length === 2) {
        return decodeURIComponent(parts.pop()?.split(';').shift() || '');
    }

    return '';
};

const handlePayment = async () => {
    isLoading.value = true;
    transactionStatus.value = 'idle';
    resultData.value = null;

    try {
        const response = await fetch('/payment/token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
        });

        if (!response.ok) {
            const errData = await response.json();
            throw new Error(errData.message || 'Gagal menghubungi server.');
        }

        const data = await response.json();

        if (data.success && data.token) {
            // @ts-expect-error - window.snap is injected by Midtrans Snap.js
            if (window.snap) {
                // @ts-expect-error - window.snap is injected by Midtrans Snap.js
                window.snap.pay(data.token, {
                    onSuccess: async function (result: any) {
                        transactionStatus.value = 'pending';
                        resultData.value = result;
                        await verifyPayment(result.order_id);
                    },
                    onPending: function (result: any) {
                        transactionStatus.value = 'pending';
                        resultData.value = result;
                    },
                    onError: function (result: any) {
                        transactionStatus.value = 'error';
                        resultData.value = result;
                    },
                    onClose: function () {
                        transactionStatus.value = 'closed';
                    }
                });
            } else {
                toast.error('Midtrans Snap SDK gagal dimuat. Coba refresh halaman.');
            }
        } else {
            toast.error(data.message || 'Gagal mendapatkan token transaksi.');
        }
    } catch (error: any) {
        toast.error(error.message || 'Terjadi kesalahan sistem.');
    } finally {
        isLoading.value = false;
    }
};

const verifyPayment = async (orderId: string) => {
    try {
        const response = await fetch('/payment/success', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ order_id: orderId }),
        });

        const data = await response.json();
        if (response.ok && data.success) {
            transactionStatus.value = 'success';
            successMessage.value = data.message;
            
            // Start countdown to redirect
            const interval = setInterval(() => {
                countdown.value--;
                if (countdown.value <= 0) {
                    clearInterval(interval);
                    router.visit('/dashboard');
                }
            }, 1000);
        } else {
            transactionStatus.value = 'error';
        }
    } catch (error) {
        transactionStatus.value = 'error';
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
                    Kembangkan transparansi program kerja, administrasi keuangan, dan dokumentasi posko KKN dengan sekali transaksi praktis menggunakan pembayaran digital Midtrans.
                </p>
            </div>

            <!-- Main Layout Split Grid -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                
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
                    <div class="bg-white rounded-2xl border border-slate-200/85 p-6 shadow-sm relative overflow-hidden flex flex-col">
                        <!-- Premium gradient corner accent -->
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-sky-400/20 to-transparent pointer-events-none rounded-bl-full"></div>

                        <div class="space-y-1 pb-4 border-b border-slate-100">
                            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider">Premium Access</h3>
                            <h4 class="text-xl font-extrabold text-slate-900">Paket Langganan Posko</h4>
                            <p class="text-xs text-slate-400">1x Aktivasi untuk 1 Kelompok KKN</p>
                        </div>

                        <!-- Pricing Display -->
                        <div class="py-6 space-y-2">
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
                                :disabled="isLoading || transactionStatus === 'success'"
                                class="w-full bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white font-bold py-4 rounded-xl transition duration-200 flex items-center justify-center gap-2.5 shadow-sm text-sm"
                            >
                                <Spinner v-if="isLoading" class="size-4" />
                                <CreditCard v-else class="size-4" />
                                Bayar Sekarang
                            </Button>
                            
                            <div class="flex items-center justify-center gap-1.5 text-[10px] text-slate-400">
                                <ShieldCheck class="size-3.5 text-slate-400" />
                                Transaksi aman & terenkripsi oleh Midtrans
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Feedback States -->
                    <div v-if="transactionStatus !== 'idle'" class="rounded-xl border p-4 space-y-3.5 shadow-sm animate-fade-in" :class="{
                        'border-green-200 bg-green-50/50 text-green-950': transactionStatus === 'success',
                        'border-amber-200 bg-amber-50/50 text-amber-950': transactionStatus === 'pending',
                        'border-red-200 bg-red-50/50 text-red-950': transactionStatus === 'error',
                        'border-slate-200 bg-slate-50/50 text-slate-950': transactionStatus === 'closed'
                    }">
                        <div class="flex items-center gap-2.5 font-bold text-sm">
                            <CheckCircle2 v-if="transactionStatus === 'success'" class="size-5 text-green-600" />
                            <AlertTriangle v-else-if="transactionStatus === 'pending'" class="size-5 text-amber-600" />
                            <XCircle v-else-if="transactionStatus === 'error'" class="size-5 text-red-600" />
                            <AlertTriangle v-else class="size-5 text-slate-600" />

                            <span v-if="transactionStatus === 'success'">Pembayaran Berhasil!</span>
                            <span v-else-if="transactionStatus === 'pending'">Menunggu Pembayaran</span>
                            <span v-else-if="transactionStatus === 'error'">Pembayaran Gagal</span>
                            <span v-else>Checkout Ditutup</span>
                        </div>

                        <p class="text-xs leading-relaxed text-slate-600">
                            <span v-if="transactionStatus === 'success'">
                                {{ successMessage || 'Terima kasih! Pembayaran Anda telah terkonfirmasi secara aman.' }} 
                                <br>
                                <span class="font-bold text-sky-700 flex items-center gap-1 mt-2">
                                    Mengarahkan Anda ke Dashboard dalam {{ countdown }} detik... 
                                    <ArrowRight class="size-3.5 animate-bounce-horizontal" />
                                </span>
                            </span>
                            <span v-else-if="transactionStatus === 'pending'">
                                Selesaikan transaksi Anda melalui portal pembayaran Midtrans yang telah terbuka.
                            </span>
                            <span v-else-if="transactionStatus === 'error'">
                                Terjadi kendala saat memproses atau memverifikasi transaksi. Silakan coba klik Bayar Sekarang kembali.
                            </span>
                            <span v-else>
                                Anda telah menutup jendela checkout sebelum menyelesaikan pembayaran. Klik 'Bayar Sekarang' untuk mencoba kembali.
                            </span>
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
