<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Check, X, Eye, Phone, Mail, ArrowLeft } from '@lucide/vue';
import { ref } from 'vue';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import { useConfirm } from '@/composables/useConfirm';
import { useToast } from '@/composables/useToast';

defineProps<{
    preorders: Array<{
        id: number;
        name: string;
        email: string;
        whatsapp: string;
        payment_proof: string;
        status: string;
        created_at: string;
        user_id: number;
    }>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard Admin', href: '/admin/dashboard' },
            { title: 'Manajemen Preorder', href: '/admin/preorders' },
        ],
    },
});

const isProofOpen = ref(false);
const activeProofUrl = ref<string | null>(null);
const { confirm } = useConfirm();
const toast = useToast();

const viewProof = (url: string) => {
    activeProofUrl.value = url;
    isProofOpen.value = true;
};

const handleApprove = async (id: number, name: string) => {
    const isConfirmed = await confirm({
        title: 'Setujui Preorder?',
        message: `Setujui preorder dari ${name}? Pengguna ini akan langsung aktif sebagai Host.`,
        confirmText: 'Ya, Setujui',
        cancelText: 'Batal',
    });

    if (!isConfirmed) {
        return;
    }

    try {
        const response = await fetch(`/admin/preorders/${id}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
        });

        const data = await response.json();
        toast.success(data.message || 'Preorder berhasil disetujui.');
        router.reload({ only: ['preorders'] });
    } catch {
        toast.error('Gagal menyetujui preorder.');
    }
};

const handleReject = async (id: number, name: string) => {
    const isConfirmed = await confirm({
        title: 'Tolak Preorder?',
        message: `Tolak pengajuan preorder dari ${name}?`,
        confirmText: 'Ya, Tolak',
        cancelText: 'Batal',
        variant: 'destructive',
    });

    if (!isConfirmed) {
        return;
    }

    try {
        const response = await fetch(`/admin/preorders/${id}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
        });

        const data = await response.json();
        toast.success(data.message || 'Preorder berhasil ditolak.');
        router.reload({ only: ['preorders'] });
    } catch {
        toast.error('Gagal menolak preorder.');
    }
};

const getCookie = (name: string): string => {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) {
        return decodeURIComponent(parts.pop()?.split(';').shift() || '');
    }
    return '';
};
</script>

<template>
    <Head title="Manajemen Preorder - Admin" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-6xl mx-auto font-sans">
        <div class="flex items-center gap-3">
            <Link href="/admin/dashboard" class="rounded-lg p-2 hover:bg-slate-100 text-slate-500 transition-colors">
                <ArrowLeft class="size-5" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Validasi Preorder Promosi</h1>
                <p class="text-sm text-slate-500">Periksa bukti transfer QRIS pendaftaran preorder, hubungi pendaftar, dan setujui akses Host posko.</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-500">
                    <thead class="text-xs uppercase bg-slate-50 text-slate-700 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Nama Pendaftar</th>
                            <th class="px-6 py-4 font-semibold">Kontak</th>
                            <th class="px-6 py-4 font-semibold">Bukti Pembayaran</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold">Diajukan Pada</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="po in preorders" :key="po.id" class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ po.name }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">User ID: {{ po.user_id }}</div>
                            </td>
                            <td class="px-6 py-4 space-y-1">
                                <div class="flex items-center gap-1.5 text-xs text-slate-600">
                                    <Mail class="size-3.5" /> {{ po.email }}
                                </div>
                                <div class="flex items-center gap-1.5 text-xs text-slate-600">
                                    <Phone class="size-3.5" /> 
                                    <a :href="`https://wa.me/${po.whatsapp.replace(/[^0-9]/g, '')}`" target="_blank" class="hover:text-sky-600 underline">
                                        {{ po.whatsapp }}
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <button @click="viewProof(po.payment_proof)" class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-semibold text-sky-600 hover:bg-sky-50 transition border-sky-100">
                                    <Eye class="size-3.5" /> Lihat Bukti
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold" :class="{
                                    'bg-amber-50 text-amber-700': po.status === 'pending',
                                    'bg-green-50 text-green-700': po.status === 'approved',
                                    'bg-red-50 text-red-700': po.status === 'rejected',
                                }">
                                    {{ po.status.toUpperCase() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ new Date(po.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button
                                    v-if="po.status === 'pending'"
                                    @click="handleApprove(po.id, po.name)"
                                    class="inline-flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition"
                                >
                                    <Check class="size-3.5" /> Setujui
                                </button>
                                <button
                                    v-if="po.status === 'pending'"
                                    @click="handleReject(po.id, po.name)"
                                    class="inline-flex items-center gap-1 bg-red-500 hover:bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition"
                                >
                                    <X class="size-3.5" /> Tolak
                                </button>
                                <span v-else class="text-xs text-slate-400 italic">Selesai</span>
                            </td>
                        </tr>
                        <tr v-if="preorders.length === 0">
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                Tidak ada pengajuan preorder masuk.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Proof Image Modal (Lightbox) -->
        <Dialog v-model:open="isProofOpen">
            <DialogContent class="sm:max-w-2xl bg-black border-none p-0 overflow-hidden flex items-center justify-center min-h-[400px]">
                <div class="relative w-full h-full p-2 flex items-center justify-center">
                    <img v-if="activeProofUrl" :src="activeProofUrl" alt="Bukti Pembayaran QRIS" class="max-w-full max-h-[80vh] object-contain rounded-lg" />
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>
