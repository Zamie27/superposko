<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, Calendar, Award, ShieldAlert, ArrowLeft, Check, AlertCircle } from '@lucide/vue';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { useConfirm } from '@/composables/useConfirm';
import { useToast } from '@/composables/useToast';

const props = defineProps<{
    subscriptions: {
        data: Array<{
            id: number;
            name: string;
            email: string;
            subscription_expires_at: string | null;
            university: string | null;
            group_number: string | null;
        }>;
        links: Array<any>;
        current_page: number;
        last_page: number;
    };
    filters: {
        search?: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard Admin', href: '/admin/dashboard' },
            { title: 'Manajemen Langganan', href: '/admin/subscriptions' },
        ],
    },
});

const searchQuery = ref(props.filters.search || '');
const isDurationModalOpen = ref(false);
const selectedUser = ref<any>(null);
const expiresAtInput = ref('');
const isProcessing = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const { confirm } = useConfirm();
const toast = useToast();

watch(searchQuery, (value) => {
    router.get('/admin/subscriptions', { search: value }, {
        preserveState: true,
        replace: true,
    });
});

const openDurationModal = (user: any) => {
    selectedUser.value = user;
    if (user.subscription_expires_at) {
        expiresAtInput.value = user.subscription_expires_at.substring(0, 10);
    } else {
        const defaultDate = new Date();
        defaultDate.setDate(defaultDate.getDate() + 40);
        expiresAtInput.value = defaultDate.toISOString().substring(0, 10);
    }
    successMessage.value = '';
    errorMessage.value = '';
    isDurationModalOpen.value = true;
};

const handleUpdateDuration = async () => {
    if (!expiresAtInput.value) {
        errorMessage.value = 'Silakan pilih tanggal kadaluarsa.';
        return;
    }

    isProcessing.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    try {
        const response = await fetch(`/admin/subscriptions/${selectedUser.value.id}/duration`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                expires_at: expiresAtInput.value,
            }),
        });

        const data = await response.json();
        if (data.success) {
            successMessage.value = data.message;
            router.reload({ only: ['subscriptions'] });
            setTimeout(() => {
                isDurationModalOpen.value = false;
            }, 1500);
        } else {
            errorMessage.value = data.message || 'Terjadi kesalahan.';
        }
    } catch (e) {
        errorMessage.value = 'Gagal menghubungi server.';
    } finally {
        isProcessing.value = false;
    }
};

const handleBypassPayment = async (user: any) => {
    const isConfirmed = await confirm({
        title: 'Bypass Pembayaran?',
        message: `Berikan hak akses bypass pembayaran (aktif 40 hari) ke user ${user.name}?`,
        confirmText: 'Ya, Bypass',
        cancelText: 'Batal',
    });

    if (!isConfirmed) {
        return;
    }

    try {
        const response = await fetch(`/admin/subscriptions/${user.id}/bypass`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
        });

        const data = await response.json();
        toast.success(data.message || 'Bypass pembayaran berhasil.');
        router.reload({ only: ['subscriptions'] });
    } catch (e) {
        toast.error('Gagal memberikan bypass pembayaran.');
    }
};

const handleRevokeSubscription = async (user: any) => {
    const isConfirmed = await confirm({
        title: 'Cabut Langganan?',
        message: `Cabut hak akses langganan posko user ${user.name}? User akan di-set expired.`,
        confirmText: 'Ya, Cabut',
        cancelText: 'Batal',
        variant: 'destructive',
    });

    if (!isConfirmed) {
        return;
    }

    try {
        const response = await fetch(`/admin/subscriptions/${user.id}/revoke`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
        });

        const data = await response.json();
        toast.success(data.message || 'Langganan berhasil dicabut.');
        router.reload({ only: ['subscriptions'] });
    } catch (e) {
        toast.error('Gagal mencabut langganan.');
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

const isExpired = (dateString: string | null) => {
    if (!dateString) return false;
    const safeString = String(dateString).replace(' ', 'T');
    return new Date(safeString) < new Date();
};

const formatDate = (dateString: string | null) => {
    if (!dateString) return '-';
    const safeString = String(dateString).replace(' ', 'T');
    const date = new Date(safeString);
    if (isNaN(date.getTime())) return dateString;
    return date.toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
};
</script>

<template>
    <Head title="Manajemen Langganan - Admin" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-6xl mx-auto font-sans">
        <div class="flex items-center gap-3">
            <Link href="/admin/dashboard" class="rounded-lg p-2 hover:bg-slate-100 text-slate-500 transition-colors">
                <ArrowLeft class="size-5" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Langganan Posko KKN</h1>
                <p class="text-sm text-slate-500">Pantau posko yang aktif berlangganan, edit durasi akses backup, cabut akses, atau bypass bayar.</p>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="relative max-w-md">
            <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari nama, email, atau posko..."
                class="w-full rounded-xl border border-slate-200 pl-9 pr-4 py-2.5 text-sm focus:border-sky-500 focus:outline-none shadow-sm"
            />
        </div>

        <!-- Subscriptions Table Card -->
        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-500">
                    <thead class="text-xs uppercase bg-slate-50 text-slate-700 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 font-semibold">User Pendaftar</th>
                            <th class="px-6 py-4 font-semibold">Kampus & Kelompok</th>
                            <th class="px-6 py-4 font-semibold">Masa Berlaku</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="user in subscriptions.data" :key="user.id" class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 flex items-center gap-2">
                                    {{ user.name }}
                                    <span class="inline-flex items-center rounded-full bg-slate-50 px-2 py-0.5 text-[10px] font-semibold text-slate-600 border">
                                        {{ user.role.toUpperCase() }}
                                    </span>
                                </div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ user.email }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                <div>{{ user.university || '-' }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ user.group_number || '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-medium">
                                <span v-if="user.role === 'user'">
                                    Belum Berlangganan
                                </span>
                                <span v-else-if="user.subscription_expires_at">
                                    {{ formatDate(user.subscription_expires_at) }}
                                </span>
                                <span v-else class="text-emerald-600">
                                    Selamanya (Manual)
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold" :class="{
                                    'bg-green-50 text-green-700': user.role === 'host' && !isExpired(user.subscription_expires_at),
                                    'bg-red-50 text-red-700': user.role === 'host' && isExpired(user.subscription_expires_at),
                                    'bg-slate-100 text-slate-600': user.role === 'user',
                                }">
                                    {{ user.role === 'user' ? 'TIDAK AKTIF' : (isExpired(user.subscription_expires_at) ? 'EXPIRED' : 'AKTIF') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2.5">
                                <template v-if="user.role === 'host'">
                                    <button @click="openDurationModal(user)" class="text-xs font-semibold text-sky-600 hover:text-sky-700 underline">
                                        Atur Durasi
                                    </button>
                                    <button @click="handleRevokeSubscription(user)" class="text-xs font-semibold text-red-500 hover:text-red-700 underline">
                                        Cabut Akses
                                    </button>
                                </template>
                                <template v-else>
                                    <button @click="handleBypassPayment(user)" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 underline">
                                        Bypass Bayar (40d)
                                    </button>
                                </template>
                            </td>
                        </tr>
                        <tr v-if="subscriptions.data.length === 0">
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                                Tidak ada akun langganan Host posko yang ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between" v-if="subscriptions.last_page > 1">
                <span class="text-xs text-slate-400">Halaman {{ subscriptions.current_page }} dari {{ subscriptions.last_page }}</span>
                <div class="flex items-center gap-2">
                    <Link
                        v-for="link in subscriptions.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        v-html="link.label"
                        class="px-3 py-1.5 rounded-lg border text-xs font-medium transition"
                        :class="{
                            'bg-sky-500 text-white border-sky-500': link.active,
                            'hover:bg-slate-50 text-slate-600 border-slate-200': !link.active,
                            'text-slate-300 pointer-events-none': !link.url
                        }"
                    />
                </div>
            </div>
        </div>

        <!-- Duration Adjustment Modal -->
        <Dialog v-model:open="isDurationModalOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <Calendar class="size-5 text-sky-500" /> Perpanjang Masa Aktif Langganan
                    </DialogTitle>
                </DialogHeader>
                <div class="space-y-4 py-3">
                    <p class="text-xs text-slate-500">
                        Atur tanggal kadaluarsa baru untuk posko user <strong>{{ selectedUser?.name }}</strong>.
                    </p>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Tanggal Berakhir</label>
                        <input
                            v-model="expiresAtInput"
                            type="date"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                        />
                    </div>

                    <!-- Alerts -->
                    <div v-if="successMessage" class="p-3 bg-green-50 text-green-700 text-xs rounded-xl flex items-center gap-2">
                        <Check class="size-4 shrink-0" /> {{ successMessage }}
                    </div>
                    <div v-if="errorMessage" class="p-3 bg-red-50 text-red-700 text-xs rounded-xl flex items-center gap-2">
                        <AlertCircle class="size-4 shrink-0" /> {{ errorMessage }}
                    </div>
                </div>
                <DialogFooter>
                    <Button :disabled="isProcessing" @click="handleUpdateDuration" class="bg-sky-500 hover:bg-sky-600 text-white font-semibold">
                        <Spinner v-if="isProcessing" /> Simpan Masa Aktif
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
