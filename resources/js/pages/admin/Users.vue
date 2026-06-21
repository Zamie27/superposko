<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, Mail, Key, Shield, User, ArrowLeft, Check, AlertCircle } from '@lucide/vue';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
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
    } catch (e) {
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
    } catch (e) {
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
                host_id: roleForm.value.role === 'member' && roleForm.value.host_id ? Number(roleForm.value.host_id) : null,
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
    } catch (e) {
        errorMessage.value = 'Gagal menghubungi server.';
    } finally {
        isProcessing.value = false;
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

        <!-- Search Bar -->
        <div class="relative max-w-md">
            <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Cari nama, email, atau universitas..."
                class="w-full rounded-xl border border-slate-200 pl-9 pr-4 py-2.5 text-sm focus:border-sky-500 focus:outline-none shadow-sm"
            />
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
                                    'bg-slate-100 text-slate-600': user.role !== 'admin' && user.role !== 'host',
                                }">
                                    {{ user.role.toUpperCase() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                <span v-if="user.role === 'member'">
                                    Host ID: {{ user.host_id || '-' }}
                                </span>
                                <span v-else class="text-slate-300">-</span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button @click="openRoleModal(user)" class="text-xs font-semibold text-sky-600 hover:text-sky-700 underline">
                                    Edit Role
                                </button>
                                <button @click="openResetModal(user)" class="text-xs font-semibold text-amber-600 hover:text-amber-700 underline">
                                    Reset Password
                                </button>
                                <button @click="handleSendResetEmail(user)" class="text-xs font-semibold text-slate-500 hover:text-slate-700 underline">
                                    Kirim Email Reset
                                </button>
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
                            <option value="admin">Admin</option>
                            <option value="host">Host</option>
                            <option value="user">User</option>
                            <option value="member">Member</option>
                        </select>
                    </div>

                    <!-- Host ID Pick (Only if role is member) -->
                    <div v-if="roleForm.role === 'member'" class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Pilih Posko Host Terkait</label>
                        <select v-model="roleForm.host_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none">
                            <option value="">-- Tanpa Hubungan Host --</option>
                            <option v-for="h in hosts" :key="h.id" :value="h.id">
                                {{ h.name }} ({{ h.email }})
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
    </div>
</template>
