<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, Calendar, ArrowLeft, Check, AlertCircle, Clock } from '@lucide/vue';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { useConfirm } from '@/composables/useConfirm';
import { useToast } from '@/composables/useToast';

const props = defineProps<{
    trials: {
        data: Array<{
            id: number;
            name: string;
            email: string;
            role: string;
            trial_ends_at: string | null;
            created_at: string;
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
            { title: 'Manajemen Trial', href: '/admin/trials' },
        ],
    },
});

const searchQuery = ref(props.filters.search || '');
const isTrialModalOpen = ref(false);
const selectedUser = ref<any>(null);
const trialDaysInput = ref(5);
const isProcessing = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const { confirm } = useConfirm();
const toast = useToast();

watch(searchQuery, (value) => {
    router.get('/admin/trials', { search: value }, {
        preserveState: true,
        replace: true,
    });
});

const openTrialModal = (user: any) => {
    selectedUser.value = user;
    trialDaysInput.value = 5; // Default to 5 days
    successMessage.value = '';
    errorMessage.value = '';
    isTrialModalOpen.value = true;
};

const handleUpdateTrial = async () => {
    if (!trialDaysInput.value || trialDaysInput.value < 1) {
        errorMessage.value = 'Silakan masukkan jumlah hari yang valid (minimal 1 hari).';
        return;
    }

    isProcessing.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    try {
        const response = await fetch(`/admin/trials/${selectedUser.value.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                trial_days: trialDaysInput.value,
            }),
        });

        const data = await response.json();
        if (data.success) {
            successMessage.value = data.message;
            toast.success(data.message || 'Trial berhasil diperbarui.');
            router.reload({ only: ['trials'] });
            setTimeout(() => {
                isTrialModalOpen.value = false;
            }, 1500);
        } else {
            errorMessage.value = data.message || 'Terjadi kesalahan.';
        }
    } catch {
        errorMessage.value = 'Gagal menghubungi server.';
    } finally {
        isProcessing.value = false;
    }
};

const handleRevokeTrial = async (user: any) => {
    const isConfirmed = await confirm({
        title: 'Cabut Akses Trial?',
        message: `Cabut akses trial untuk user ${user.name}? Pengguna akan di-set menjadi role 'user' dan trial berakhir.`,
        confirmText: 'Ya, Cabut',
        cancelText: 'Batal',
        variant: 'destructive',
    });

    if (!isConfirmed) {
        return;
    }

    try {
        const response = await fetch(`/admin/trials/${user.id}/revoke`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
        });

        const data = await response.json();
        toast.success(data.message || 'Trial berhasil dicabut.');
        router.reload({ only: ['trials'] });
    } catch {
        toast.error('Gagal mencabut trial.');
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

const getRemainingDays = (user: any): number => {
    if (user.role !== 'trial') return 0;
    const endsAt = user.trial_ends_at 
        ? new Date(user.trial_ends_at.replace(' ', 'T')) 
        : new Date(new Date(user.created_at).getTime() + 5 * 24 * 60 * 60 * 1000);
    const diffTime = endsAt.getTime() - new Date().getTime();
    return Math.max(0, Math.ceil(diffTime / (1000 * 60 * 60 * 24)));
};

const isExpired = (user: any): boolean => {
    if (user.role !== 'trial') return true;
    const endsAt = user.trial_ends_at 
        ? new Date(user.trial_ends_at.replace(' ', 'T')) 
        : new Date(new Date(user.created_at).getTime() + 5 * 24 * 60 * 60 * 1000);
    return endsAt < new Date();
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
    <Head title="Manajemen Trial - Admin" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-6xl mx-auto font-sans">
        <div class="flex items-center gap-3">
            <Link href="/admin/dashboard" class="rounded-lg p-2 hover:bg-slate-100 text-slate-500 transition-colors">
                <ArrowLeft class="size-5" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Akses Trial</h1>
                <p class="text-sm text-slate-500">Cek durasi sisa trial akun pendaftar, beri durasi tambahan/trial baru, atau cabut akses trial.</p>
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

        <!-- Trials Table Card -->
        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-500">
                    <thead class="text-xs uppercase bg-slate-50 text-slate-700 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 font-semibold">User Pendaftar</th>
                            <th class="px-6 py-4 font-semibold">Kampus & Kelompok</th>
                            <th class="px-6 py-4 font-semibold">Tanggal Berakhir</th>
                            <th class="px-6 py-4 font-semibold">Sisa Waktu</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="user in trials.data" :key="user.id" class="hover:bg-slate-50/50 transition">
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
                                <span v-if="user.role !== 'trial' && !user.trial_ends_at">
                                    Bukan Akun Trial
                                </span>
                                <span v-else>
                                    {{ formatDate(user.trial_ends_at || new Date(new Date(user.created_at).getTime() + 5*24*60*60*1000).toISOString()) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span v-if="user.role === 'trial'" class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold" :class="{
                                    'bg-amber-50 text-amber-700': !isExpired(user),
                                    'bg-red-50 text-red-700': isExpired(user),
                                }">
                                    {{ isExpired(user) ? 'EXPIRED' : `${getRemainingDays(user)} HARI LAGI` }}
                                </span>
                                <span v-else class="text-xs text-slate-400">
                                    -
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2.5">
                                <button @click="openTrialModal(user)" class="text-xs font-semibold text-sky-600 hover:text-sky-700 underline">
                                    Beri/Atur Trial
                                </button>
                                <button v-if="user.role === 'trial'" @click="handleRevokeTrial(user)" class="text-xs font-semibold text-red-500 hover:text-red-700 underline">
                                    Cabut Trial
                                </button>
                            </td>
                        </tr>
                        <tr v-if="trials.data.length === 0">
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                                Tidak ada akun trial yang ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between" v-if="trials.last_page > 1">
                <span class="text-xs text-slate-400">Halaman {{ trials.current_page }} dari {{ trials.last_page }}</span>
                <div class="flex items-center gap-2">
                    <Link
                        v-for="link in trials.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        class="px-3 py-1.5 rounded-lg border text-xs font-medium transition"
                        :class="{
                            'bg-sky-500 text-white border-sky-500': link.active,
                            'hover:bg-slate-50 text-slate-600 border-slate-200': !link.active,
                            'text-slate-300 pointer-events-none': !link.url
                        }"
                    >
                        <span v-html="link.label"></span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Trial Duration Adjustment Modal -->
        <Dialog v-model:open="isTrialModalOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <Clock class="size-5 text-sky-500" /> Atur Masa Trial Akun
                    </DialogTitle>
                </DialogHeader>
                <div class="space-y-4 py-3">
                    <p class="text-xs text-slate-500">
                        Beri akses trial atau ubah sisa masa aktif untuk user <strong>{{ selectedUser?.name }}</strong>.
                    </p>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Durasi Trial (Hari)</label>
                        <input
                            v-model.number="trialDaysInput"
                            type="number"
                            min="1"
                            placeholder="5"
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
                    <Button :disabled="isProcessing" @click="handleUpdateTrial" class="bg-sky-500 hover:bg-sky-600 text-white font-semibold">
                        <Spinner v-if="isProcessing" /> Simpan Masa Trial
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
