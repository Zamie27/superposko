<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, Key, Shield, ArrowLeft, Check, AlertCircle } from '@lucide/vue';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Spinner } from '@/components/ui/spinner';
import { useConfirm } from '@/composables/useConfirm';
import { useToast } from '@/composables/useToast';

const props = defineProps<{
    users: {
        data: Array<{
            id: number;
            name: string;
            email: string;
            role: string;
            host_id: number | null;
            university: string | null;
            banned_at: string | null;
            trial_ends_at: string | null;
        }>;
        links: Array<any>;
        current_page: number;
        last_page: number;
    };
    hosts: Array<{
        id: number;
        name: string;
        email: string;
    }>;
    filters: {
        search?: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard Admin', href: '/admin/dashboard' },
            { title: 'Manajemen User', href: '/admin/users' },
        ],
    },
});

const searchQuery = ref(props.filters.search || '');
const isResetModalOpen = ref(false);
const isRoleModalOpen = ref(false);
const selectedUser = ref<any>(null);
const newPassword = ref('');
const isProcessing = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const { confirm } = useConfirm();
const toast = useToast();

// Create User State
const isCreateModalOpen = ref(false);
const createForm = ref({
    name: '',
    email: '',
    password: '',
    role: 'user',
    host_id: '' as string | number,
});

// Role Editing State
const roleForm = ref({
    role: 'host',
    host_id: '' as string | number,
});

watch(searchQuery, (value) => {
    router.get('/admin/users', { search: value }, {
        preserveState: true,
        replace: true,
    });
});

const openResetModal = (user: any) => {
    selectedUser.value = user;
    newPassword.value = '';
    successMessage.value = '';
    errorMessage.value = '';
    isResetModalOpen.value = true;
};

const handleResetPassword = async () => {
    if (!newPassword.value) {
        errorMessage.value = 'Silakan isi password baru.';

        return;
    }

    if (newPassword.value.length < 8) {
        errorMessage.value = 'Password minimal 8 karakter.';

        return;
    }
    
    isProcessing.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    try {
        const response = await fetch('/admin/users/reset-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                user_id: selectedUser.value.id,
                password: newPassword.value,
            }),
        });

        const data = await response.json();

        if (data.success) {
            successMessage.value = data.message;
            setTimeout(() => {
                isResetModalOpen.value = false;
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

const openCreateModal = () => {
    createForm.value = {
        name: '',
        email: '',
        password: '',
        role: 'user',
        host_id: '',
    };
    successMessage.value = '';
    errorMessage.value = '';
    isCreateModalOpen.value = true;
};

const handleCreateUser = async () => {
    if (!createForm.value.name || !createForm.value.email || !createForm.value.password) {
        errorMessage.value = 'Nama, email, dan password wajib diisi.';
        return;
    }

    isProcessing.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    try {
        const response = await fetch('/admin/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                name: createForm.value.name,
                email: createForm.value.email,
                password: createForm.value.password,
                role: createForm.value.role,
                host_id: ['user', 'admin', 'host', 'trial', 'dpl'].includes(createForm.value.role) ? null : (createForm.value.host_id ? Number(createForm.value.host_id) : null),
            }),
        });

        const data = await response.json();

        if (response.ok && data.success) {
            successMessage.value = data.message;
            router.reload({ only: ['users'] });
            setTimeout(() => {
                isCreateModalOpen.value = false;
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

const handleSendResetEmail = async (user: any) => {
    const isConfirmed = await confirm({
        title: 'Reset Password?',
        message: `Kirim link reset password ke email ${user.email}?`,
        confirmText: 'Ya, Kirim',
        cancelText: 'Batal',
    });

    if (!isConfirmed) {
        return;
    }

    try {
        const response = await fetch('/admin/users/send-reset-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                user_id: user.id,
            }),
        });

        const data = await response.json();
        toast.success(data.message || 'Email reset password berhasil dikirim.');
    } catch {
        toast.error('Gagal mengirimkan email reset password.');
    }
};

const openRoleModal = (user: any) => {
    selectedUser.value = user;
    roleForm.value.role = user.role;
    roleForm.value.host_id = user.host_id || '';
    successMessage.value = '';
    errorMessage.value = '';
    isRoleModalOpen.value = true;
};

const handleUpdateRole = async () => {
    isProcessing.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    try {
        const response = await fetch(`/admin/users/${selectedUser.value.id}/role`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                role: roleForm.value.role,
                host_id: ['user', 'admin', 'host', 'trial', 'dpl'].includes(roleForm.value.role) ? null : (roleForm.value.host_id ? Number(roleForm.value.host_id) : null),
            }),
        });

        const data = await response.json();

        if (data.success) {
            successMessage.value = data.message;
            router.reload({ only: ['users'] });
            setTimeout(() => {
                isRoleModalOpen.value = false;
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

// Trial Management Logic
const isTrialModalOpen = ref(false);
const trialDays = ref(5);

const getTrialDaysLeft = (endsAtStr: string | null) => {
    if (!endsAtStr) {
return 0;
}

    const endsAt = new Date(endsAtStr);
    const now = new Date();
    const diffTime = endsAt.getTime() - now.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    return Math.max(0, diffDays);
};

const openTrialModal = (user: any) => {
    selectedUser.value = user;
    trialDays.value = user.trial_ends_at ? getTrialDaysLeft(user.trial_ends_at) || 5 : 5;
    successMessage.value = '';
    errorMessage.value = '';
    isTrialModalOpen.value = true;
};

const handleUpdateTrial = async () => {
    isProcessing.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    try {
        const response = await fetch(`/admin/users/${selectedUser.value.id}/trial`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                trial_days: trialDays.value,
            }),
        });

        const data = await response.json();

        if (data.success) {
            successMessage.value = data.message;
            router.reload({ only: ['users'] });
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

const handleBanUser = async (user: any) => {
    const isConfirmed = await confirm({
        title: 'Ban User?',
        message: `Apakah Anda yakin ingin mem-ban akun ${user.name}? User ini tidak akan bisa mengakses platform dan akan diarahkan ke halaman banned.`,
        confirmText: 'Ya, Ban',
        cancelText: 'Batal',
    });

    if (!isConfirmed) {
return;
}

    try {
        const response = await fetch(`/admin/users/${user.id}/ban`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
        });

        const data = await response.json();

        if (data.success) {
            toast.success(data.message);
            router.reload({ only: ['users'] });
        } else {
            toast.error(data.message || 'Terjadi kesalahan.');
        }
    } catch {
        toast.error('Gagal mem-ban user.');
    }
};

const handleUnbanUser = async (user: any) => {
    const isConfirmed = await confirm({
        title: 'Batalkan Ban?',
        message: `Apakah Anda yakin ingin membuka kembali akses untuk akun ${user.name}?`,
        confirmText: 'Ya, Buka',
        cancelText: 'Batal',
    });

    if (!isConfirmed) {
return;
}

    try {
        const response = await fetch(`/admin/users/${user.id}/unban`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
        });

        const data = await response.json();

        if (data.success) {
            toast.success(data.message);
            router.reload({ only: ['users'] });
        } else {
            toast.error(data.message || 'Terjadi kesalahan.');
        }
    } catch {
        toast.error('Gagal membuka ban.');
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

const handleSwitchPosko = (user: any) => {
    router.post('/dpl/switch-posko', { host_id: user.id }, {
        onSuccess: () => {
            router.get('/host/dashboard');
        },
        onError: (errors) => {
            toast.error(errors.host_id || 'Gagal masuk ke posko');
        }
    });
};
</script>

<template>
    <Head title="Manajemen User - Admin" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-6xl mx-auto font-sans">
        <div class="flex items-center gap-3">
            <Link href="/admin/dashboard" class="rounded-lg p-2 hover:bg-slate-100 text-slate-500 transition-colors">
                <ArrowLeft class="size-5" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Pengguna</h1>
                <p class="text-sm text-slate-500">Kelola akun, edit role, ganti password, atau hubungkan member dengan posko host.</p>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="flex items-center justify-between gap-4">
            <div class="relative w-full max-w-md">
                <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari nama, email, atau universitas..."
                    class="w-full rounded-xl border border-slate-200 pl-9 pr-4 py-2.5 text-sm focus:border-sky-500 focus:outline-none shadow-sm"
                />
            </div>
            <Button @click="openCreateModal" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl">
                + Tambah Pengguna
            </Button>
        </div>

        <!-- User Table Card -->
        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-500">
                    <thead class="text-xs uppercase bg-slate-50 text-slate-700 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Nama & Email</th>
                            <th class="px-6 py-4 font-semibold">Universitas</th>
                            <th class="px-6 py-4 font-semibold">Role</th>
                            <th class="px-6 py-4 font-semibold">Grup Host ID</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="user in users.data" :key="user.id" class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ user.name }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ user.email }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ user.university || '-' }}
                            </td>
                             <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold" :class="{
                                    'bg-purple-50 text-purple-700': user.role === 'admin',
                                    'bg-sky-50 text-sky-700': user.role === 'host',
                                    'bg-amber-50 text-amber-700 border border-amber-200': user.role === 'trial',
                                    'bg-slate-100 text-slate-600': user.role !== 'admin' && user.role !== 'host' && user.role !== 'trial',
                                }">
                                    {{ user.role.toUpperCase() }}
                                </span>
                                <div v-if="user.role === 'trial'" class="text-[10px] text-amber-600 mt-1 font-semibold">
                                    {{ getTrialDaysLeft(user.trial_ends_at) }} hari tersisa
                                </div>
                                <div v-if="user.banned_at" class="mt-1">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 text-red-700 px-2 py-0.5 text-[10px] font-semibold border border-red-200">
                                        BANNED
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                <span v-if="user.role === 'member'">
                                    Host ID: {{ user.host_id || '-' }}
                                </span>
                                <span v-else class="text-slate-300">-</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="outline" size="sm" class="h-8 border-slate-200">
                                            Pengaturan
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end" class="w-48">
                                        <DropdownMenuItem v-if="!user.host_id && user.role !== 'admin'" @click="handleSwitchPosko(user)" class="cursor-pointer font-medium text-blue-600 focus:text-blue-700">
                                            Masuk Posko
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="openRoleModal(user)" class="cursor-pointer font-medium">
                                            Edit Role
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="openTrialModal(user)" class="cursor-pointer font-medium text-amber-600 focus:text-amber-700">
                                            Atur Trial
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="openResetModal(user)" class="cursor-pointer font-medium">
                                            Reset Password
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="handleSendResetEmail(user)" class="cursor-pointer font-medium">
                                            Kirim Email Reset
                                        </DropdownMenuItem>
                                        <DropdownMenuItem v-if="user.banned_at" @click="handleUnbanUser(user)" class="cursor-pointer font-medium text-emerald-600 focus:text-emerald-700">
                                            Batal Ban
                                        </DropdownMenuItem>
                                        <DropdownMenuItem v-else @click="handleBanUser(user)" class="cursor-pointer font-medium text-red-600 focus:text-red-700">
                                            Ban Akun
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                                Pengguna tidak ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between" v-if="users.last_page > 1">
                <span class="text-xs text-slate-400">Halaman {{ users.current_page }} dari {{ users.last_page }}</span>
                <div class="flex items-center gap-2">
                    <Link
                        v-for="link in users.links"
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

        <!-- Create User Modal -->
        <Dialog v-model:open="isCreateModalOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <Shield class="size-5 text-indigo-500" /> Tambah Pengguna Baru
                    </DialogTitle>
                </DialogHeader>
                <div class="space-y-4 py-3 max-h-[70vh] overflow-y-auto">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Nama Lengkap</label>
                        <input v-model="createForm.name" type="text" placeholder="Masukkan nama" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Alamat Email</label>
                        <input v-model="createForm.email" type="email" placeholder="Masukkan email" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Kata Sandi (Min. 8 Karakter)</label>
                        <input v-model="createForm.password" type="password" placeholder="Buat kata sandi" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none" />
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Role Pengguna</label>
                        <select v-model="createForm.role" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none">
                            <option value="admin">Admin / Pengurus Pusat</option>
                            <option value="user">User Biasa (Belum Masuk Posko)</option>
                            <option value="trial">User Trial</option>
                            <option value="dpl">DPL (Dosen Pembimbing)</option>
                            <option value="host">Ketua Posko (Host Baru)</option>
                            <optgroup label="Anggota Posko (Pilih Divisi)">
                                <option value="bendahara">Bendahara</option>
                                <option value="logistik">Logistik</option>
                                <option value="pdd">PDD</option>
                                <option value="humas">Humas</option>
                                <option value="acara">Acara</option>
                                <option value="perlengkapan">Perlengkapan</option>
                                <option value="anggota">Anggota Biasa</option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- Host ID Pick (If not admin/user/trial/dpl/host) -->
                    <div v-if="!['admin', 'user', 'trial', 'dpl', 'host'].includes(createForm.role)" class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Pilih Posko Host Terkait</label>
                        <select v-model="createForm.host_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none">
                            <option value="">-- Wajib Pilih Host --</option>
                            <option v-for="h in hosts" :key="h.id" :value="h.id">
                                {{ h.name }} ({{ h.email }}) - {{ h.university || 'Tanpa Universitas' }}
                            </option>
                        </select>
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
                    <Button :disabled="isProcessing" @click="handleCreateUser" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold">
                        <Spinner v-if="isProcessing" /> Tambahkan Pengguna
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Reset Password Modal -->
        <Dialog v-model:open="isResetModalOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <Key class="size-5 text-amber-500" /> Reset Password Manual
                    </DialogTitle>
                </DialogHeader>
                <div class="space-y-4 py-3">
                    <p class="text-xs text-slate-500">
                        Ubah kata sandi secara paksa untuk pengguna <strong>{{ selectedUser?.name }}</strong> ({{ selectedUser?.email }}).
                    </p>
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Password Baru (Min. 8 karakter)</label>
                        <input
                            v-model="newPassword"
                            type="password"
                            placeholder="Ketik password baru"
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
                    <Button :disabled="isProcessing" @click="handleResetPassword" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold">
                        <Spinner v-if="isProcessing" /> Ubah Password
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Edit Role Modal -->
        <Dialog v-model:open="isRoleModalOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <Shield class="size-5 text-sky-500" /> Sesuaikan Peran Akun (Role)
                    </DialogTitle>
                </DialogHeader>
                <div class="space-y-4 py-3">
                    <p class="text-xs text-slate-500">
                        Atur peran & posko grup user <strong>{{ selectedUser?.name }}</strong>.
                    </p>

                    <!-- Role Dropdown -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Role Pengguna</label>
                        <select v-model="roleForm.role" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none">
                            <option value="admin">Admin / Pengurus Pusat</option>
                            <option value="user">User Biasa (Belum Masuk Posko)</option>
                            <option value="trial">User Trial</option>
                            <option value="dpl">DPL (Dosen Pembimbing)</option>
                            <option value="host">Ketua Posko (Host Baru)</option>
                            <optgroup label="Anggota Posko (Pilih Divisi)">
                                <option value="bendahara">Bendahara</option>
                                <option value="logistik">Logistik</option>
                                <option value="pdd">PDD</option>
                                <option value="humas">Humas</option>
                                <option value="acara">Acara</option>
                                <option value="perlengkapan">Perlengkapan</option>
                                <option value="anggota">Anggota Biasa</option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- Host ID Pick (If not admin/user/trial/dpl/host) -->
                    <div v-if="!['admin', 'user', 'trial', 'dpl', 'host'].includes(roleForm.role)" class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Pilih Posko Host Terkait</label>
                        <select v-model="roleForm.host_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none">
                            <option value="">-- Wajib Pilih Host --</option>
                            <option v-for="h in hosts" :key="h.id" :value="h.id">
                                {{ h.name }} ({{ h.email }}) - {{ h.university || 'Tanpa Universitas' }}
                            </option>
                        </select>
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
                    <Button :disabled="isProcessing" @click="handleUpdateRole" class="bg-sky-500 hover:bg-sky-600 text-white font-semibold">
                        <Spinner v-if="isProcessing" /> Simpan Perubahan
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Edit Trial Modal -->
        <Dialog v-model:open="isTrialModalOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <Shield class="size-5 text-amber-500" /> Atur/Beri Masa Trial Akun
                    </DialogTitle>
                </DialogHeader>
                <div class="space-y-4 py-3">
                    <p class="text-xs text-slate-500">
                        Atur sisa durasi masa trial untuk pengguna <strong>{{ selectedUser?.name }}</strong> ({{ selectedUser?.email }}). Ini akan otomatis mengubah role mereka menjadi 'trial' dengan sisa durasi hari yang ditentukan.
                    </p>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Durasi Trial (Hari)</label>
                        <input
                            v-model="trialDays"
                            type="number"
                            min="1"
                            placeholder="Contoh: 5"
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
                    <Button :disabled="isProcessing" @click="handleUpdateTrial" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold">
                        <Spinner v-if="isProcessing" /> Simpan Trial
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
