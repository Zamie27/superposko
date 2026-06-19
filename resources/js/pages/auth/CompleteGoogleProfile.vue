<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

const props = defineProps<{
    name: string;
    email: string;
}>();

defineOptions({
    layout: {
        title: 'Lengkapi Profil KKN',
        description: 'Langkah terakhir untuk mengaktifkan posko KKN Anda.',
    },
});

const form = useForm({
    university: '',
    group_number: '',
    kkn_address: '',
    password: '',
    password_confirmation: '',
});

const showPasswordFields = ref(false);

// Password requirements check
const hasMinLength = computed(() => form.password.length >= 8);
const hasUppercase = computed(() => /[A-Z]/.test(form.password));
const hasLowercase = computed(() => /[a-z]/.test(form.password));
const hasNumber = computed(() => /[0-9]/.test(form.password));
const hasSymbol = computed(() => /[^A-Za-z0-9]/.test(form.password));
const isMatched = computed(() => form.password !== '' && form.password === form.password_confirmation);

const submit = () => {
    form.post('/auth/google/complete');
};
</script>

<template>
    <Head title="Lengkapi Profil" />

    <div class="space-y-6">
        <div>
            <h2 class="text-lg font-medium text-slate-900">Halo, {{ props.name }}!</h2>
            <p class="text-sm text-muted-foreground mt-1">
                Akun Google Anda ({{ props.email }}) berhasil terhubung. Silakan lengkapi data posko KKN Anda di bawah ini untuk memulai.
            </p>
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <!-- Informasi KKN -->
            <div class="grid gap-2">
                <Label for="university">Universitas / Perguruan Tinggi</Label>
                <Input
                    id="university"
                    type="text"
                    required
                    v-model="form.university"
                    placeholder="Nama universitas Anda"
                />
                <InputError :message="form.errors.university" />
            </div>

            <div class="grid gap-2">
                <Label for="group_number">Nomor / Nama Kelompok KKN</Label>
                <Input
                    id="group_number"
                    type="text"
                    required
                    v-model="form.group_number"
                    placeholder="Contoh: Kelompok 12 atau Posko Gedangan"
                />
                <InputError :message="form.errors.group_number" />
            </div>

            <div class="grid gap-2">
                <Label for="kkn_address">Alamat Posko KKN</Label>
                <textarea
                    id="kkn_address"
                    required
                    v-model="form.kkn_address"
                    rows="3"
                    placeholder="Alamat lengkap posko KKN kelompok Anda"
                    class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                ></textarea>
                <InputError :message="form.errors.kkn_address" />
            </div>

            <!-- Opsi Tambah Password -->
            <div class="border-t border-slate-200 pt-4 mt-2">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm font-semibold text-slate-900">Tambahkan kata sandi ke akun Anda?</span>
                        <p class="text-xs text-muted-foreground mt-0.5">
                            Ini opsional. Jika dilewati, Anda hanya bisa masuk menggunakan akun Google Anda.
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="showPasswordFields = !showPasswordFields"
                        class="text-xs font-semibold text-sky-600 hover:text-sky-700 underline"
                    >
                        {{ showPasswordFields ? 'Sembunyikan' : 'Tambah Kata Sandi' }}
                    </button>
                </div>

                <div v-if="showPasswordFields" class="space-y-4 mt-4">
                    <div class="grid gap-2">
                        <Label for="password">Kata Sandi Baru</Label>
                        <PasswordInput
                            id="password"
                            name="password"
                            placeholder="Masukkan kata sandi baru"
                            v-model="form.password"
                        />
                        <!-- Password Strength Indicators -->
                        <div class="mt-2 space-y-1.5 text-xs text-muted-foreground">
                            <div class="flex items-center gap-2">
                                <span :class="['h-2 w-2 rounded-full', hasMinLength ? 'bg-green-500' : 'bg-slate-300']"></span>
                                <span :class="hasMinLength ? 'text-green-600 font-medium' : ''">Minimal 8 karakter</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span :class="['h-2 w-2 rounded-full', hasUppercase ? 'bg-green-500' : 'bg-slate-300']"></span>
                                <span :class="hasUppercase ? 'text-green-600 font-medium' : ''">Mengandung huruf besar (A-Z)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span :class="['h-2 w-2 rounded-full', hasLowercase ? 'bg-green-500' : 'bg-slate-300']"></span>
                                <span :class="hasLowercase ? 'text-green-600 font-medium' : ''">Mengandung huruf kecil (a-z)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span :class="['h-2 w-2 rounded-full', hasNumber ? 'bg-green-500' : 'bg-slate-300']"></span>
                                <span :class="hasNumber ? 'text-green-600 font-medium' : ''">Mengandung angka (0-9)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span :class="['h-2 w-2 rounded-full', hasSymbol ? 'bg-green-500' : 'bg-slate-300']"></span>
                                <span :class="hasSymbol ? 'text-green-600 font-medium' : ''">Mengandung simbol atau karakter khusus</span>
                            </div>
                        </div>
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation">Konfirmasi Kata Sandi</Label>
                        <PasswordInput
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Ulangi kata sandi baru"
                            v-model="form.password_confirmation"
                        />
                        <!-- Confirmation matches indicator -->
                        <div v-if="form.password_confirmation" class="mt-1 flex items-center gap-2 text-xs">
                            <span :class="['h-2 w-2 rounded-full', isMatched ? 'bg-green-500' : 'bg-red-500']"></span>
                            <span :class="isMatched ? 'text-green-600 font-medium' : 'text-red-600 font-medium'">
                                {{ isMatched ? 'Kata sandi cocok' : 'Kata sandi belum cocok' }}
                            </span>
                        </div>
                        <InputError :message="form.errors.password_confirmation" />
                    </div>
                </div>
            </div>

            <Button
                type="submit"
                class="mt-4 w-full"
                :disabled="form.processing"
            >
                <Spinner v-if="form.processing" />
                Simpan & Lanjutkan
            </Button>
        </form>
    </div>
</template>
