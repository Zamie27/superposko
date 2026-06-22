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

defineProps<{
    members: any[];
    isHost: boolean;
}>();

const isModalOpen = ref(false);
const editingMember = ref<any>(null);
const { confirm } = useConfirm();

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'anggota',
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
    form.clearErrors();
    showPassword.value = false;
    isModalOpen.value = true;
};

const submitForm = () => {
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
                <Button v-if="isHost" @click="openCreateModal" class="bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white">
                    Tambah Anggota
                </Button>
            </div>

            <div class="rounded-xl border bg-card overflow-hidden">
                <table class="w-full text-sm text-left text-slate-600">
                    <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b">
                        <tr>
                            <th scope="col" class="px-6 py-4">Nama Lengkap</th>
                            <th scope="col" class="px-6 py-4">Email</th>
                            <th scope="col" class="px-6 py-4">Role</th>
                            <th v-if="isHost" scope="col" class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="member in members" :key="member.id" class="border-b hover:bg-slate-50/50">
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
                        <tr v-if="members.length === 0">
                            <td :colspan="isHost ? 4 : 3" class="px-6 py-8 text-center text-slate-500">
                                Belum ada anggota yang ditambahkan.
                            </td>
                        </tr>
                    </tbody>
                </table>
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
                                    <SelectItem value="anggota">Anggota Biasa</SelectItem>
                                    <SelectItem value="bendahara">Bendahara</SelectItem>
                                    <SelectItem value="sekretaris">Sekretaris</SelectItem>
                                    <SelectItem value="pdd">PDD</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.role" />
                        </div>

                        <DialogFooter class="pt-4">
                            <Button type="button" variant="outline" @click="isModalOpen = false">Batal</Button>
                            <Button type="submit" :disabled="form.processing" class="bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white">Simpan</Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </template>

    </div>
</template>
