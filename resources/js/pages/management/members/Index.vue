<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import InputError from '@/components/InputError.vue';

const props = defineProps<{
    members: any[];
    isHost: boolean;
}>();

const isModalOpen = ref(false);
const editingMember = ref<any>(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'anggota',
});

const openCreateModal = () => {
    editingMember.value = null;
    form.reset();
    form.clearErrors();
    isModalOpen.value = true;
};

const openEditModal = (member: any) => {
    editingMember.value = member;
    form.name = member.name;
    form.email = member.email;
    form.password = '';
    form.role = member.role;
    form.clearErrors();
    isModalOpen.value = true;
};

const submitForm = () => {
    if (editingMember.value) {
        form.put(`/management/members/${editingMember.value.id}`, {
            onSuccess: () => {
                isModalOpen.value = false;
            },
        });
    } else {
        form.post('/management/members', {
            onSuccess: () => {
                isModalOpen.value = false;
            },
        });
    }
};

const deleteMember = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus anggota ini?')) {
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

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
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
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
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
                        <td class="px-6 py-4 text-right space-x-3">
                            <button @click="openEditModal(member)" class="font-medium text-[#38BDF8] hover:underline">Edit</button>
                            <button @click="deleteMember(member.id)" class="font-medium text-red-500 hover:underline">Hapus</button>
                        </td>
                    </tr>
                    <tr v-if="members.length === 0">
                        <td colspan="4" class="px-6 py-8 text-center text-slate-500">
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
                        <Input id="password" type="password" v-model="form.password" />
                        <InputError :message="form.errors.password" />
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

    </div>
</template>
