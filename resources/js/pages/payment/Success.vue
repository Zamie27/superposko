<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle2, AlertTriangle, XCircle, ArrowRight, ShieldCheck, Sparkles, Receipt } from '@lucide/vue';
import { computed } from 'vue';

const props = defineProps<{
    status: 'success' | 'pending' | 'failed';
    reference: string;
    amount: number;
    paymentMethod: string;
    errorMessage?: string;
}>();

const formattedAmount = computed(() => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(props.amount);
});
</script>

<template>
    <Head title="Status Pembayaran - SuperPosko" />

    <div class="min-h-screen bg-slate-50/50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 font-sans">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl border border-slate-200/80 shadow-lg relative overflow-hidden">
            <!-- Decorative premium gradient corner -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-sky-400/20 to-transparent pointer-events-none rounded-bl-full"></div>

            <div class="text-center space-y-6">
                <!-- Status Icon Container with animations -->
                <div class="flex justify-center">
                    <div v-if="status === 'success'" class="p-4 rounded-full bg-green-50 border border-green-100 text-green-500 animate-bounce">
                        <CheckCircle2 class="size-16" />
                    </div>
                    <div v-else-if="status === 'pending'" class="p-4 rounded-full bg-amber-50 border border-amber-100 text-amber-500 animate-pulse">
                        <AlertTriangle class="size-16" />
                    </div>
                    <div v-else class="p-4 rounded-full bg-red-50 border border-red-100 text-red-500 animate-shake">
                        <XCircle class="size-16" />
                    </div>
                </div>

                <!-- Status Titles -->
                <div class="space-y-2">
                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                        <span v-if="status === 'success'">Pembayaran Berhasil!</span>
                        <span v-else-if="status === 'pending'">Pembayaran Tertunda</span>
                        <span v-else>Pembayaran Gagal</span>
                    </h1>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        <span v-if="status === 'success'">Transaksi Anda telah selesai diproses. Akun Anda sekarang aktif sebagai Host premium.</span>
                        <span v-else-if="status === 'pending'">Sistem kami sedang menunggu konfirmasi pembayaran Anda dari Tripay.</span>
                        <span v-else>{{ errorMessage || 'Transaksi Anda gagal atau dibatalkan. Silakan coba kembali beberapa saat lagi.' }}</span>
                    </p>
                </div>

                <!-- Transaction Details Card -->
                <div class="bg-slate-50 rounded-xl p-5 border border-slate-100 text-left space-y-3.5">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5 pb-2 border-b border-slate-200/60">
                        <Receipt class="size-3.5" /> Detail Transaksi
                    </h3>
                    
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400 font-medium">Nomor Referensi</span>
                        <span class="font-mono font-bold text-slate-700 bg-white border px-2 py-0.5 rounded shadow-sm text-[10px]">{{ reference }}</span>
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400 font-medium">Metode Pembayaran</span>
                        <span class="font-bold text-slate-700">{{ paymentMethod }}</span>
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400 font-medium">Total Tagihan</span>
                        <span class="font-extrabold text-slate-900 text-sm">{{ formattedAmount }}</span>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-2">
                    <Link
                        v-if="status === 'success'"
                        href="/dashboard"
                        class="w-full bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white font-bold py-3.5 px-4 rounded-xl transition duration-200 flex items-center justify-center gap-2 shadow-sm text-sm"
                    >
                        Masuk ke Dashboard
                        <ArrowRight class="size-4" />
                    </Link>
                    <Link
                        v-else-if="status === 'pending'"
                        href="/dashboard"
                        class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-3.5 px-4 rounded-xl transition duration-200 flex items-center justify-center gap-2 shadow-sm text-sm"
                    >
                        Ke Dashboard Sementara
                        <ArrowRight class="size-4" />
                    </Link>
                    <Link
                        v-else
                        href="/preorder"
                        class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3.5 px-4 rounded-xl transition duration-200 flex items-center justify-center gap-2 shadow-sm text-sm"
                    >
                        Coba Lagi
                        <ArrowRight class="size-4" />
                    </Link>
                </div>

                <div class="flex items-center justify-center gap-1.5 text-[10px] text-slate-400">
                    <ShieldCheck class="size-3.5 text-slate-400" />
                    Transaksi aman & terenkripsi oleh Tripay
                </div>
            </div>
        </div>
    </div>
</template>
