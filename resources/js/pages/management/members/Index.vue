<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { Eye, EyeOff } from '@lucide/vue';
import { ref, computed } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useConfirm } from '@/composables/useConfirm';

const props = defineProps<{
    members: any[];
    pendingDpls?: any[];
    activeDpls?: any[];
    isHost: boolean;
    availableRoles: Record<string, { label: string; capacity: number; current: number; available: boolean }>;
    customRoles: any[];
    currentUserRole: string;
}>();

const approveDpl = (id: number) => {
    router.post(`/management/members/dpl-approve/${id}`);
};

const rejectDpl = (id: number) => {
    router.post(`/management/members/dpl-reject/${id}`);
};

const isModalOpen = ref(false);
const isTransferModalOpen = ref(false);
const editingMember = ref<any>(null);
const { confirm } = useConfirm();

const searchQuery = ref('');
const filterRole = ref('all');

const filteredMembers = computed(() => {
    let result = props.members;
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(m => 
            m.name.toLowerCase().includes(query) || 
            m.email.toLowerCase().includes(query)
        );
    }
    
    if (filterRole.value !== 'all') {
        result = result.filter(m => m.role === filterRole.value);
    }
    
    return result;
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'anggota',
    custom_role_id: null as number | null,
});

const transferForm = useForm({
    target_member_id: null as number | null,
    new_self_role: 'anggota',
});

const showPassword = ref(false);

const passwordRules = computed(() => {
    const pwd = form.password || '';

    return {
        minLength: pwd.length >= 8,
        hasUppercase: /[A-Z]/.test(pwd),
        hasLowercase: /[a-z]/.test(pwd),
        hasNumber: /[0-9]/.test(pwd),
        hasSpecial: /[^A-Za-z0-9]/.test(pwd),
    };
});

const openCreateModal = () => {
    editingMember.value = null;
    form.reset();
    form.clearErrors();
    showPassword.value = false;
    isModalOpen.value = true;
};

const openEditModal = (member: any) => {
    editingMember.value = member;
    form.name = member.name;
    form.email = member.email;
    form.password = '';
    form.role = member.role;
    form.custom_role_id = member.custom_role_id;
    form.clearErrors();
    showPassword.value = false;
    isModalOpen.value = true;
};

const submitForm = () => {
    if (form.role === 'ketua') {
        if (!editingMember.value) {
            alert('Untuk menjadikan anggota baru sebagai Ketua, silakan buat anggota tersebut terlebih dahulu dengan role lain, kemudian lakukan Edit & Transfer Ketua.');
            return;
        }
        transferForm.target_member_id = editingMember.value.id;
        transferForm.new_self_role = 'anggota';
        isTransferModalOpen.value = true;
        isModalOpen.value = false;
        return;
    }

    if (editingMember.value) {
        form.put(`/management/members/${editingMember.value.id}`, {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.post('/management/members', {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

const submitTransfer = () => {
    transferForm.post('/management/members/transfer-ketua', {
        onSuccess: () => {
            isTransferModalOpen.value = false;
            transferForm.reset();
        },
    });
};

const isBatchModalOpen = ref(false);
const batchTab = ref('manual');

const handleCsvUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        const text = e.target?.result as string;
        if (!text) return;

        const lines = text.split('\n');
        if (lines.length <= 1) {
            alert('File CSV kosong.');
            return;
        }

        const parsedMembers: Array<{ name: string; email: string; role: string }> = [];

        const header = lines[0].split(',').map(h => h.trim().toLowerCase());
        const nameIdx = header.findIndex(h => h === 'nama' || h === 'name');
        const emailIdx = header.indexOf('email');
        const roleIdx = header.indexOf('role');

        if (nameIdx === -1 || emailIdx === -1 || roleIdx === -1) {
            alert('Format CSV tidak sesuai template. Pastikan baris pertama memiliki header: nama, email, role.');
            return;
        }

        for (let i = 1; i < lines.length; i++) {
            const line = lines[i].trim();
            if (!line) continue;

            // Simple CSV line parser
            const cols = line.split(',').map(c => c.trim());
            if (cols.length >= 3) {
                parsedMembers.push({
                    name: cols[nameIdx] || '',
                    email: cols[emailIdx] || '',
                    role: cols[roleIdx] ? cols[roleIdx].toLowerCase() : 'anggota',
                });
            }
        }

        if (parsedMembers.length > 0) {
            batchForm.members = parsedMembers;
            batchTab.value = 'manual';
            alert(`Berhasil membaca ${parsedMembers.length} data dari CSV. Silakan tinjau dan klik Simpan Batch.`);
        } else {
            alert('Tidak ada data anggota valid yang ditemukan di file CSV.');
        }
    };
    reader.readAsText(file);
};

const batchForm = useForm({
    members: [
        { name: '', email: '', role: 'anggota' }
    ]
});

const openBatchModal = () => {
    batchForm.reset();
    batchTab.value = 'manual';
    batchForm.members = [{ name: '', email: '', role: 'anggota' }];
    batchForm.clearErrors();
    isBatchModalOpen.value = true;
};

const addBatchRow = () => {
    batchForm.members.push({ name: '', email: '', role: 'anggota' });
};

const removeBatchRow = (index: number) => {
    if (batchForm.members.length > 1) {
        batchForm.members.splice(index, 1);
    }
};

const submitBatchForm = () => {
    batchForm.post('/management/members/batch', {
        onSuccess: () => {
            isBatchModalOpen.value = false;
            batchForm.reset();
        }
    });
};

const deleteMember = async (id: number) => {
    const isConfirmed = await confirm({
        title: 'Hapus Anggota?',
        message: 'Apakah Anda yakin ingin menghapus anggota ini? Tindakan ini tidak dapat dibatalkan.',
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        variant: 'destructive',
    });

    if (isConfirmed) {
        router.delete(`/management/members/${id}`);
    }
};

const isCustomRoleModalOpen = ref(false);
const customRoleForm = useForm({
    id: null as number | null,
    name: '',
    permissions: {
        can_manage_members: false,
        can_write_finance: false,
        can_write_logistic: false,
        can_write_inventory: false,
        can_write_contact: false,
        can_write_proker: false,
        can_manage_immich: false,
        can_write_schedule: false,
        can_write_group_logbook: false,
    }
});

const openCustomRoleModal = () => {
    customRoleForm.reset();
    isCustomRoleModalOpen.value = true;
};

const editCustomRole = (cr: any) => {
    customRoleForm.id = cr.id;
    customRoleForm.name = cr.name;
    customRoleForm.permissions = cr.permissions || {};
};

const submitCustomRole = () => {
    if (customRoleForm.id) {
        customRoleForm.put(`/management/custom-roles/${customRoleForm.id}`, {
            onSuccess: () => {
                customRoleForm.reset();
                customRoleForm.id = null;
            }
        });
    } else {
        customRoleForm.post('/management/custom-roles', {
            onSuccess: () => {
                customRoleForm.reset();
                customRoleForm.id = null;
            }
        });
    }
};

const deleteCustomRole = async (id: number) => {
    const isConfirmed = await confirm({
        title: 'Hapus Role Kustom?',
        message: 'Apakah Anda yakin ingin menghapus role kustom ini? Pastikan tidak ada anggota yang sedang menggunakannya.',
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        variant: 'destructive',
    });

    if (isConfirmed) {
        router.delete(`/management/custom-roles/${id}`);
    }
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Manajemen',
                href: '#',
            },
            {
                title: 'Anggota',
                href: '/management/members',
            },
        ],
    },
});
</script>

<template>
    <Head title="Manajemen Anggota" />

    <div class="relative flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6 min-h-[400px]">
        <!-- Trial/Unsubscribed Lock Overlay -->
        <div v-if="$page.props.auth.user.role === 'trial' || !$page.props.auth.user.is_subscribed" class="absolute inset-0 z-50 flex flex-col items-center justify-center bg-slate-50/60 dark:bg-slate-900/60 backdrop-blur-md p-6 text-center rounded-xl">
            <div class="max-w-md p-8 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-950/95 shadow-xl flex flex-col items-center">
                <div class="h-16 w-16 rounded-full bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center text-amber-500 mb-6 animate-pulse">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Fitur Premium Terkunci</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-3 leading-relaxed">
                    {{ $page.props.auth.user.role === 'trial' ? 'Fitur ini tidak tersedia selama masa trial. Hubungi Owner untuk melakukan aktivasi pembayaran flat agar dapat mengakses fitur ini tanpa batas.' : 'Layanan ini hanya tersedia setelah berlangganan. Silakan aktifkan paket posko untuk mengakses fitur ini tanpa batas.' }}
                </p>
                <div class="mt-6 flex flex-col gap-3 w-full">
                    <a 
                        :href="$page.props.preorder_promo_active ? '/preorder' : '/payment'" 
                        class="flex-1 rounded-xl bg-[#38BDF8] hover:bg-[#38BDF8]/90 py-3 text-sm font-bold text-white transition duration-200 text-center"
                    >
                        {{ $page.props.preorder_promo_active ? 'Preorder Sekarang' : 'Beli Langganan' }}
                    </a>
                    <a 
                        href="/dashboard" 
                        class="flex-1 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 transition duration-200 text-center"
                    >
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Secure Content Wrap -->
        <template v-if="$page.props.auth.user.role !== 'trial' && $page.props.auth.user.is_subscribed">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Anggota</h1>
                    <p class="text-sm text-slate-500 mt-1">Kelola anggota posko dan atur hak akses mereka.</p>
                </div>
                <div class="flex gap-2">
                    <Button v-if="isHost" @click="openCustomRoleModal" variant="outline" class="border-slate-300">
                        Kelola Role Kustom
                    </Button>
                    <Button v-if="isHost" @click="openCreateModal" class="bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white">
                        Tambah Anggota
                    </Button>
                    <Button v-if="isHost" @click="openBatchModal" variant="outline" class="border-[#38BDF8] text-[#38BDF8] hover:bg-slate-100 dark:hover:bg-slate-800">
                        Tambah Batch
                    </Button>
                </div>
            </div>

            <!-- Pending DPL Requests Card -->
            <div v-if="isHost && pendingDpls && pendingDpls.length > 0" class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 rounded-2xl p-5 space-y-3">
                <h3 class="text-sm font-bold text-amber-800 dark:text-amber-300 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    Permintaan Pemantauan DPL Baru ({{ pendingDpls.length }})
                </h3>
                <p class="text-xs text-amber-700 dark:text-amber-400">
                    Dosen di bawah ini meminta akses untuk memantau aktivitas posko Anda secara read-only.
                </p>
                <div class="space-y-2">
                    <div 
                        v-for="req in pendingDpls" 
                        :key="req.id" 
                        class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-amber-100 dark:border-amber-950 flex flex-col sm:flex-row justify-between sm:items-center gap-3"
                    >
                        <div>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ req.dpl.name }}</h4>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">{{ req.dpl.email }}</p>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <button 
                                @click="approveDpl(req.id)"
                                class="px-3 py-1.5 bg-emerald-555 hover:bg-emerald-600 text-white text-[10px] font-bold rounded-lg transition cursor-pointer"
                            >
                                Setujui
                            </button>
                            <button 
                                @click="rejectDpl(req.id)"
                                class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[10px] font-bold rounded-lg transition cursor-pointer"
                            >
                                Tolak
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search & Filter -->
            <div class="flex flex-col sm:flex-row gap-4 justify-between items-center bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
                <div class="relative w-full sm:w-72">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-slate-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" v-model="searchQuery" class="block w-full p-2 pl-10 text-sm text-slate-900 border border-slate-300 rounded-lg bg-slate-50 focus:ring-[#38BDF8] focus:border-[#38BDF8] dark:bg-slate-800 dark:border-slate-700 dark:text-white" placeholder="Cari nama atau email...">
                </div>
                <div class="w-full sm:w-48">
                    <select v-model="filterRole" class="block w-full p-2 text-sm text-slate-900 border border-slate-300 rounded-lg bg-slate-50 focus:ring-[#38BDF8] focus:border-[#38BDF8] dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                        <option value="all">Semua Role</option>
                        <option v-for="(roleData, key) in availableRoles" :key="key" :value="key">
                            {{ roleData.label }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="rounded-xl border bg-card overflow-y-auto flex-1 min-h-[300px] shadow-sm">
                <table class="w-full text-sm text-left text-slate-600 relative">
                    <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b sticky top-0 z-10 shadow-sm">
                        <tr>
                            <th scope="col" class="px-6 py-4">Nama Lengkap</th>
                            <th scope="col" class="px-6 py-4">Email</th>
                            <th scope="col" class="px-6 py-4">Role</th>
                            <th v-if="isHost" scope="col" class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="member in filteredMembers" :key="member.id" class="border-b hover:bg-slate-50/50">
                            <td class="px-6 py-4 font-medium text-slate-900">
                                {{ member.name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ member.email }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full bg-[#38BDF8]/10 px-2.5 py-0.5 text-xs font-semibold text-[#38BDF8] capitalize">
                                    {{ member.role }}
                                </span>
                            </td>
                            <td v-if="isHost" class="px-6 py-4 text-right space-x-3">
                                <button @click="openEditModal(member)" class="font-medium text-[#38BDF8] hover:underline">Edit</button>
                                <button @click="deleteMember(member.id)" class="font-medium text-red-500 hover:underline">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="filteredMembers.length === 0">
                            <td :colspan="isHost ? 4 : 3" class="px-6 py-8 text-center text-slate-500">
                                <span v-if="members.length === 0">Belum ada anggota yang ditambahkan.</span>
                                <span v-else>Tidak ada anggota yang cocok dengan pencarian/filter.</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Active DPLs (Supervising DPL) List -->
            <div v-if="activeDpls && activeDpls.length > 0" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 space-y-3">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4.5 h-4.5 text-sky-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.746 3.746 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                    </svg>
                    Dosen Pembimbing Lapangan (DPL) Terhubung
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Daftar Dosen Pembimbing Lapangan yang memiliki akses read-only untuk memantau data posko Anda.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div 
                        v-for="dpl in activeDpls" 
                        :key="dpl.id" 
                        class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-xl flex items-center justify-between"
                    >
                        <div>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ dpl.dpl.name }}</h4>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">{{ dpl.dpl.email }}</p>
                        </div>
                        <span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 text-[10px] font-semibold rounded-full border border-emerald-200/30">
                            Aktif Memantau
                        </span>
                    </div>
                </div>
            </div>

            <Dialog v-model:open="isModalOpen">
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>{{ editingMember ? 'Edit Anggota' : 'Tambah Anggota' }}</DialogTitle>
                        <DialogDescription>
                            {{ editingMember ? 'Ubah detail anggota.' : 'Tambahkan anggota baru ke dalam kelompok posko Anda.' }}
                        </DialogDescription>
                    </DialogHeader>
                    
                    <form @submit.prevent="submitForm" class="space-y-4 py-4">
                        <div class="grid gap-2">
                            <Label for="name">Nama Lengkap</Label>
                            <Input id="name" v-model="form.name" placeholder="John Doe" />
                            <InputError :message="form.errors.name" />
                        </div>
                        
                        <div class="grid gap-2">
                            <Label for="email">Email</Label>
                            <Input id="email" type="email" v-model="form.email" placeholder="john@example.com" />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password">Password {{ editingMember ? '(Kosongkan jika tidak ingin diubah)' : '' }}</Label>
                            <div class="relative">
                                <Input 
                                    id="password" 
                                    :type="showPassword ? 'text' : 'password'" 
                                    v-model="form.password" 
                                    class="pr-10" 
                                    placeholder="Masukkan password"
                                />
                                <button 
                                    type="button" 
                                    @click="showPassword = !showPassword" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none"
                                >
                                    <Eye v-if="!showPassword" class="size-4" />
                                    <EyeOff v-else class="size-4" />
                                </button>
                            </div>
                            <InputError :message="form.errors.password" />

                            <!-- Password Strength Indicator -->
                            <div v-if="form.password" class="mt-2 space-y-1.5 bg-slate-50 dark:bg-slate-900/50 p-3 rounded-xl border text-xs leading-normal">
                                <div class="font-semibold text-slate-500 mb-1">Persyaratan Password:</div>
                                <div class="flex items-center gap-2" :class="passwordRules.minLength ? 'text-green-600 dark:text-green-400' : 'text-slate-400'">
                                    <span class="size-1.5 rounded-full shrink-0" :class="passwordRules.minLength ? 'bg-green-600 dark:bg-green-400' : 'bg-slate-300 dark:bg-slate-700'"></span>
                                    <span>Minimal 8 karakter</span>
                                </div>
                                <div class="flex items-center gap-2" :class="passwordRules.hasUppercase ? 'text-green-600 dark:text-green-400' : 'text-slate-400'">
                                    <span class="size-1.5 rounded-full shrink-0" :class="passwordRules.hasUppercase ? 'bg-green-600 dark:bg-green-400' : 'bg-slate-300 dark:bg-slate-700'"></span>
                                    <span>Mengandung huruf besar (A-Z)</span>
                                </div>
                                <div class="flex items-center gap-2" :class="passwordRules.hasLowercase ? 'text-green-600 dark:text-green-400' : 'text-slate-400'">
                                    <span class="size-1.5 rounded-full shrink-0" :class="passwordRules.hasLowercase ? 'bg-green-600 dark:bg-green-400' : 'bg-slate-300 dark:bg-slate-700'"></span>
                                    <span>Mengandung huruf kecil (a-z)</span>
                                </div>
                                <div class="flex items-center gap-2" :class="passwordRules.hasNumber ? 'text-green-600 dark:text-green-400' : 'text-slate-400'">
                                    <span class="size-1.5 rounded-full shrink-0" :class="passwordRules.hasNumber ? 'bg-green-600 dark:bg-green-400' : 'bg-slate-300 dark:bg-slate-700'"></span>
                                    <span>Mengandung angka (0-9)</span>
                                </div>
                                <div class="flex items-center gap-2" :class="passwordRules.hasSpecial ? 'text-green-600 dark:text-green-400' : 'text-slate-400'">
                                    <span class="size-1.5 rounded-full shrink-0" :class="passwordRules.hasSpecial ? 'bg-green-600 dark:bg-green-400' : 'bg-slate-300 dark:bg-slate-700'"></span>
                                    <span>Mengandung simbol atau karakter khusus</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="role">Peran (Role)</Label>
                            <Select v-model="form.role">
                                <SelectTrigger>
                                    <SelectValue placeholder="Pilih Role" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="(roleData, roleKey) in availableRoles"
                                        :key="roleKey"
                                        :value="roleKey"
                                        :disabled="!roleData.available && (editingMember ? editingMember.role !== roleKey : true)"
                                    >
                                        {{ roleData.label }}
                                        <template v-if="roleData.capacity > 0">
                                            ({{ roleData.current }}/{{ roleData.capacity }})
                                        </template>
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.role" />
                        </div>

                        <div v-if="form.role === 'lainnya'" class="grid gap-2">
                            <Label for="custom_role">Pilih Role Kustom</Label>
                            <Select v-model="form.custom_role_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Pilih Role Kustom" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="cr in customRoles"
                                        :key="cr.id"
                                        :value="cr.id"
                                    >
                                        {{ cr.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.custom_role_id" />
                            <p class="text-xs text-slate-500">Anda dapat membuat role kustom baru di menu "Kelola Role Kustom".</p>
                        </div>

                        <DialogFooter class="pt-4">
                            <Button type="button" variant="outline" @click="isModalOpen = false">Batal</Button>
                            <Button type="submit" :disabled="form.processing" class="bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white">Simpan</Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <!-- Transfer Ketua Confirmation Modal -->
            <Dialog v-model:open="isTransferModalOpen">
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>Transfer Jabatan Ketua</DialogTitle>
                        <DialogDescription>
                            Apakah Anda yakin ingin mengganti role Anda (Ketua) dengan akun <strong>{{ editingMember?.name }}</strong>? Jabatan Ketua dan kendali kepemilikan posko akan dipindahkan.
                        </DialogDescription>
                    </DialogHeader>

                    <form @submit.prevent="submitTransfer" class="space-y-4 py-4">
                        <div class="rounded-xl bg-amber-50 dark:bg-amber-950/30 p-4 border border-amber-200 text-xs text-amber-800 dark:text-amber-300 leading-relaxed">
                            <strong>PENTING:</strong> Setelah transfer disetujui, Anda akan kehilangan hak akses penuh kepemilikan posko dan akun Anda akan diturunkan perannya sesuai yang Anda pilih di bawah ini.
                        </div>

                        <div class="grid gap-2">
                            <Label for="new_self_role">Pilih Peran Baru untuk Diri Anda Sendiri</Label>
                            <Select v-model="transferForm.new_self_role">
                                <SelectTrigger>
                                    <SelectValue placeholder="Pilih Peran Baru" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="(roleData, roleKey) in availableRoles"
                                        :key="roleKey"
                                        :value="roleKey"
                                        :disabled="roleKey === 'ketua' || (!roleData.available && currentUserRole !== roleKey)"
                                    >
                                        {{ roleData.label }}
                                        <template v-if="roleData.capacity > 0">
                                            ({{ roleData.current }}/{{ roleData.capacity }})
                                        </template>
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="transferForm.errors.new_self_role" />
                        </div>

                        <DialogFooter class="pt-4">
                            <Button type="button" variant="outline" @click="isTransferModalOpen = false">Batal</Button>
                            <Button type="submit" :disabled="transferForm.processing" class="bg-red-500 hover:bg-red-600 text-white">Ya, Transfer & Ubah Peran Saya</Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <!-- Batch Members Modal -->
            <Dialog v-model:open="isBatchModalOpen">
                <DialogContent class="sm:max-w-[650px] max-h-[85vh] flex flex-col">
                    <DialogHeader>
                        <DialogTitle>Tambah Anggota Batch</DialogTitle>
                        <DialogDescription>
                            Tambahkan beberapa anggota kelompok sekaligus. Password acak akan digenerate otomatis dan dikirimkan ke email masing-masing.
                        </DialogDescription>
                    </DialogHeader>

                    <!-- Tabs -->
                    <div class="flex border-b border-slate-100 dark:border-slate-800 -mx-6 px-6">
                        <button 
                            type="button"
                            @click="batchTab = 'manual'"
                            class="px-4 py-2 text-xs font-bold border-b-2 transition"
                            :class="[batchTab === 'manual' ? 'border-[#38BDF8] text-[#38BDF8]' : 'border-transparent text-slate-500 hover:text-slate-700']"
                        >
                            Ketik Manual
                        </button>
                        <button 
                            type="button"
                            @click="batchTab = 'csv'"
                            class="px-4 py-2 text-xs font-bold border-b-2 transition"
                            :class="[batchTab === 'csv' ? 'border-[#38BDF8] text-[#38BDF8]' : 'border-transparent text-slate-500 hover:text-slate-700']"
                        >
                            Upload File CSV
                        </button>
                    </div>

                    <form @submit.prevent="submitBatchForm" class="flex-1 flex flex-col overflow-hidden space-y-4 py-4">
                        <div v-if="batchForm.errors.batch_error" class="rounded-xl bg-red-50 dark:bg-red-950/30 p-3 border border-red-200 text-xs text-red-800 dark:text-red-300">
                            {{ batchForm.errors.batch_error }}
                        </div>

                        <!-- Manual Grid View -->
                        <template v-if="batchTab === 'manual'">
                            <!-- Rows Wrapper -->
                            <div class="flex-1 overflow-y-auto pr-1 space-y-3 min-h-[200px] max-h-[40vh]">
                                <div 
                                    v-for="(memberRow, idx) in batchForm.members" 
                                    :key="idx" 
                                    class="grid grid-cols-12 gap-2 items-start border-b pb-3 border-slate-100 dark:border-slate-800"
                                >
                                    <!-- Name -->
                                    <div class="col-span-4">
                                        <Label class="text-[10px] uppercase font-bold text-slate-400">Nama Lengkap</Label>
                                        <Input 
                                            v-model="memberRow.name" 
                                            placeholder="Nama" 
                                            class="h-9 text-xs" 
                                            required 
                                        />
                                        <InputError :message="batchForm.errors[`members.${idx}.name`]" class="text-[10px] mt-0.5" />
                                    </div>

                                    <!-- Email -->
                                    <div class="col-span-4">
                                        <Label class="text-[10px] uppercase font-bold text-slate-400">Email</Label>
                                        <Input 
                                            v-model="memberRow.email" 
                                            type="email" 
                                            placeholder="Email" 
                                            class="h-9 text-xs" 
                                            required 
                                        />
                                        <InputError :message="batchForm.errors[`members.${idx}.email`]" class="text-[10px] mt-0.5" />
                                    </div>

                                    <!-- Role Select -->
                                    <div class="col-span-3">
                                        <Label class="text-[10px] uppercase font-bold text-slate-400">Role</Label>
                                        <select 
                                            v-model="memberRow.role" 
                                            class="w-full h-9 rounded-md border border-input bg-transparent px-3 py-1 text-xs shadow-xs focus-visible:outline-none dark:bg-slate-900 dark:text-white"
                                        >
                                            <option 
                                                v-for="(roleData, roleKey) in availableRoles" 
                                                :key="roleKey" 
                                                :value="roleKey"
                                                :disabled="!roleData.available"
                                            >
                                                {{ roleData.label }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Action button -->
                                    <div class="col-span-1 pt-6 flex justify-center">
                                        <button 
                                            type="button" 
                                            @click="removeBatchRow(idx)" 
                                            class="text-red-500 hover:text-red-650 disabled:opacity-30 disabled:cursor-not-allowed"
                                            :disabled="batchForm.members.length <= 1"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-between items-center pt-2">
                                <Button 
                                    type="button" 
                                    variant="outline" 
                                    size="sm" 
                                    @click="addBatchRow"
                                    class="text-xs text-sky-500 border-sky-500 hover:bg-sky-50 dark:hover:bg-sky-950/20"
                                >
                                    + Tambah Baris
                                </Button>

                                <div class="flex gap-2">
                                    <Button 
                                        type="button" 
                                        variant="outline" 
                                        size="sm" 
                                        @click="isBatchModalOpen = false"
                                    >
                                        Batal
                                    </Button>
                                    <Button 
                                        type="submit" 
                                        size="sm" 
                                        :disabled="batchForm.processing"
                                        class="bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white text-xs font-bold"
                                    >
                                        Simpan Batch
                                    </Button>
                                </div>
                            </div>
                        </template>

                        <!-- CSV Upload View -->
                        <template v-else>
                            <div class="flex-1 flex flex-col items-center justify-center p-8 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl text-center space-y-4 min-h-[220px]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-[#38BDF8] animate-bounce">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">Unggah berkas CSV Anda</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pastikan nama kolom adalah: nama, email, role</p>
                                </div>
                                <div class="flex flex-col sm:flex-row gap-3 w-full max-w-xs justify-center pt-2">
                                    <a 
                                        href="/templates/import_anggota_template.csv" 
                                        download 
                                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-350 transition"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                        </svg>
                                        Download Template CSV
                                    </a>
                                    <label class="px-4 py-2 bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white rounded-xl text-xs font-bold transition cursor-pointer flex items-center justify-center gap-1.5">
                                        Pilih File CSV
                                        <input 
                                            type="file" 
                                            accept=".csv" 
                                            @change="handleCsvUpload" 
                                            class="hidden" 
                                        />
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2 pt-2">
                                <Button 
                                    type="button" 
                                    variant="outline" 
                                    size="sm" 
                                    @click="isBatchModalOpen = false"
                                >
                                    Batal
                                </Button>
                            </div>
                        </template>
                    </form>
                </DialogContent>
            </Dialog>
            <!-- Custom Roles Modal -->
            <Dialog v-model:open="isCustomRoleModalOpen">
                <DialogContent class="sm:max-w-[550px] max-h-[85vh] overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>Kelola Role Kustom</DialogTitle>
                        <DialogDescription>
                            Buat role kustom untuk kelompok Anda dan atur hak aksesnya. Role ini bersifat privat untuk kelompok Anda.
                        </DialogDescription>
                    </DialogHeader>
                    
                    <div class="space-y-4 py-4">
                        <form @submit.prevent="submitCustomRole" class="border p-4 rounded-xl space-y-4 bg-slate-50 dark:bg-slate-900/50">
                            <div>
                                <Label class="mb-2 block">Nama Role Baru / Edit</Label>
                                <Input v-model="customRoleForm.name" placeholder="Misal: Divisi Kesenian" required />
                                <InputError :message="customRoleForm.errors.name" class="mt-1" />
                            </div>
                            
                            <div>
                                <Label class="mb-2 block">Hak Akses Modul</Label>
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" v-model="customRoleForm.permissions.can_manage_members" class="rounded border-gray-300 text-[#38BDF8] shadow-sm focus:border-[#38BDF8] focus:ring focus:ring-[#38BDF8] focus:ring-opacity-50">
                                        <span>Kelola Anggota</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" v-model="customRoleForm.permissions.can_write_finance" class="rounded border-gray-300 text-[#38BDF8] shadow-sm focus:border-[#38BDF8] focus:ring focus:ring-[#38BDF8] focus:ring-opacity-50">
                                        <span>E-Bendahara (Keuangan)</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" v-model="customRoleForm.permissions.can_write_logistic" class="rounded border-gray-300 text-[#38BDF8] shadow-sm focus:border-[#38BDF8] focus:ring focus:ring-[#38BDF8] focus:ring-opacity-50">
                                        <span>Logistik & Konsumsi</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" v-model="customRoleForm.permissions.can_write_inventory" class="rounded border-gray-300 text-[#38BDF8] shadow-sm focus:border-[#38BDF8] focus:ring focus:ring-[#38BDF8] focus:ring-opacity-50">
                                        <span>Inventaris Barang</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" v-model="customRoleForm.permissions.can_write_proker" class="rounded border-gray-300 text-[#38BDF8] shadow-sm focus:border-[#38BDF8] focus:ring focus:ring-[#38BDF8] focus:ring-opacity-50">
                                        <span>Repository Proker</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" v-model="customRoleForm.permissions.can_manage_immich" class="rounded border-gray-300 text-[#38BDF8] shadow-sm focus:border-[#38BDF8] focus:ring focus:ring-[#38BDF8] focus:ring-opacity-50">
                                        <span>Kelola Dokumentasi & Immich</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" v-model="customRoleForm.permissions.can_write_schedule" class="rounded border-gray-300 text-[#38BDF8] shadow-sm focus:border-[#38BDF8] focus:ring focus:ring-[#38BDF8] focus:ring-opacity-50">
                                        <span>Jadwal & Piket</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" v-model="customRoleForm.permissions.can_write_contact" class="rounded border-gray-300 text-[#38BDF8] shadow-sm focus:border-[#38BDF8] focus:ring focus:ring-[#38BDF8] focus:ring-opacity-50">
                                        <span>Buku Kontak</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" v-model="customRoleForm.permissions.can_write_group_logbook" class="rounded border-gray-300 text-[#38BDF8] shadow-sm focus:border-[#38BDF8] focus:ring focus:ring-[#38BDF8] focus:ring-opacity-50">
                                        <span>Logbook Kelompok</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="flex justify-end gap-2">
                                <Button type="button" v-if="customRoleForm.id" variant="outline" size="sm" @click="customRoleForm.reset(); customRoleForm.id = null">Batal Edit</Button>
                                <Button type="submit" size="sm" :disabled="customRoleForm.processing" class="bg-[#38BDF8] text-white">
                                    {{ customRoleForm.id ? 'Simpan Perubahan' : 'Buat Role' }}
                                </Button>
                            </div>
                        </form>

                        <div>
                            <h4 class="font-bold text-sm mb-3">Daftar Role Kustom Kelompok Anda</h4>
                            <div v-if="customRoles.length === 0" class="text-sm text-slate-500 text-center py-4 border rounded-xl">
                                Belum ada role kustom yang dibuat.
                            </div>
                            <div v-else class="space-y-2">
                                <div v-for="cr in customRoles" :key="cr.id" class="flex justify-between items-center p-3 border rounded-xl">
                                    <div>
                                        <div class="font-bold text-sm">{{ cr.name }}</div>
                                        <div class="text-[10px] text-slate-500">{{ Object.values(cr.permissions || {}).filter(Boolean).length }} Hak Akses</div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="button" @click="editCustomRole(cr)" class="text-[#38BDF8] text-xs font-medium hover:underline">Edit</button>
                                        <button type="button" @click="deleteCustomRole(cr.id)" class="text-red-500 text-xs font-medium hover:underline">Hapus</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </DialogContent>
            </Dialog>
        </template>

    </div>
</template>
