<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { CreditCard, CheckCircle as CheckCircle2, AlertTriangle, XCircle, Info } from '@lucide/vue';
import { onMounted, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { dashboard } from '@/routes';

const props = defineProps<{
    midtransClientKey: string;
    isProduction: boolean;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Test Payment',
                href: '/host/payment/test',
            },
        ],
    },
});

const isLoading = ref(false);
const transactionStatus = ref<'idle' | 'success' | 'pending' | 'error' | 'closed'>('idle');
const resultData = ref<any>(null);

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
        const response = await fetch('/host/payment/test/token', {
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
                    onSuccess: function (result: any) {
                        transactionStatus.value = 'success';
                        resultData.value = result;
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
                alert('Midtrans Snap SDK gagal dimuat. Coba refresh halaman.');
            }
        } else {
            alert(data.message || 'Gagal mendapatkan token transaksi.');
        }
    } catch (error: any) {
        alert(error.message || 'Terjadi kesalahan sistem.');
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <Head title="Test Payment Midtrans" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-2xl mx-auto font-sans">
        <div class="space-y-2">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Uji Coba Pembayaran Midtrans</h1>
            <p class="text-sm text-slate-500">
                Halaman simulasi pembayaran paket langganan SuperPosko menggunakan Midtrans Snap.
            </p>
        </div>

        <!-- Main Card -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-2 py-1 text-xs font-semibold text-sky-600">
                        <Info class="size-3.5" /> Sandbox Mode
                    </span>
                    <h3 class="mt-2.5 text-lg font-bold text-slate-900">Paket Langganan Posko KKN</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Akses penuh 40 hari / 1 siklus posko KKN</p>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-extrabold text-slate-900">Rp 100.000</span>
                    <p class="text-xs text-slate-500 mt-0.5">Sekali bayar</p>
                </div>
            </div>

            <div class="py-6 space-y-3.5 text-sm text-slate-600">
                <p>Fitur yang akan diaktifkan setelah pembayaran sukses:</p>
                <ul class="space-y-2.5">
                    <li class="flex items-center gap-2.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                        E-Bendahara (Kas & Keuangan)
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                        Digital Logbook & Kanban Board Proker
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                        Buku Kontak Desa & Manajemen Piket
                    </li>
                </ul>
            </div>

            <div class="border-t border-slate-100 pt-6">
                <Button
                    @click="handlePayment"
                    :disabled="isLoading"
                    class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3.5 rounded-xl transition duration-200 flex items-center justify-center gap-2 shadow-sm"
                >
                    <Spinner v-if="isLoading" />
                    <CreditCard v-else class="size-4" />
                    Bayar Sekarang
                </Button>
            </div>
        </div>

        <!-- Transaction Status Info -->
        <div v-if="transactionStatus !== 'idle'" class="rounded-xl border p-4 space-y-3 shadow-sm animate-fade-in" :class="{
            'border-green-200 bg-green-50/50 text-green-800': transactionStatus === 'success',
            'border-amber-200 bg-amber-50/50 text-amber-800': transactionStatus === 'pending',
            'border-red-200 bg-red-50/50 text-red-800': transactionStatus === 'error',
            'border-slate-200 bg-slate-50/50 text-slate-800': transactionStatus === 'closed'
        }">
            <div class="flex items-center gap-2.5 font-semibold text-sm">
                <CheckCircle2 v-if="transactionStatus === 'success'" class="size-5 text-green-600" />
                <AlertTriangle v-else-if="transactionStatus === 'pending'" class="size-5 text-amber-600" />
                <XCircle v-else-if="transactionStatus === 'error'" class="size-5 text-red-600" />
                <Info v-else class="size-5 text-slate-600" />

                <span v-if="transactionStatus === 'success'">Pembayaran Berhasil!</span>
                <span v-else-if="transactionStatus === 'pending'">Pembayaran Menunggu Penyelesaian</span>
                <span v-else-if="transactionStatus === 'error'">Pembayaran Gagal</span>
                <span v-else>Popup Pembayaran Ditutup</span>
            </div>

            <p class="text-xs leading-relaxed text-slate-600">
                <span v-if="transactionStatus === 'success'">
                    Terima kasih! Pembayaran Anda dengan ID Order <strong>{{ resultData?.order_id }}</strong> sebesar Rp 100.000 telah kami terima.
                </span>
                <span v-else-if="transactionStatus === 'pending'">
                    Silakan selesaikan pembayaran Anda melalui metode yang Anda pilih. ID Order: <strong>{{ resultData?.order_id }}</strong>.
                </span>
                <span v-else-if="transactionStatus === 'error'">
                    Terjadi kendala dalam memproses transaksi Anda. Silakan coba kembali.
                </span>
                <span v-else>
                    Anda telah menutup pop-up Snap sebelum menyelesaikan pembayaran. Silakan klik 'Bayar Sekarang' untuk mencoba kembali.
                </span>
            </p>
        </div>
    </div>
</template>
