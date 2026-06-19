<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineProps<{
    passwordRules: string;
}>();

defineOptions({
    layout: {
        title: 'Daftar Akun KKN',
        description: 'Lengkapi formulir di bawah ini untuk mendaftarkan posko KKN Anda',
    },
});

const password = ref('');
const passwordConfirmation = ref('');

// Password requirements check
const hasMinLength = computed(() => password.value.length >= 8);
const hasUppercase = computed(() => /[A-Z]/.test(password.value));
const hasLowercase = computed(() => /[a-z]/.test(password.value));
const hasNumber = computed(() => /[0-9]/.test(password.value));
const hasSymbol = computed(() => /[^A-Za-z0-9]/.test(password.value));
const isMatched = computed(() => password.value !== '' && password.value === passwordConfirmation.value);
</script>

<template>
    <Head title="Daftar" />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <!-- Informasi Admin Posko -->
            <div class="grid gap-2">
                <Label for="name">Nama Lengkap Admin</Label>
                <Input
                    id="name"
                    type="text"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    name="name"
                    placeholder="Nama lengkap Anda"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Alamat Email</Label>
                <Input
                    id="email"
                    type="email"
                    required
                    :tabindex="2"
                    autocomplete="email"
                    name="email"
                    placeholder="email@example.com"
                />
                <InputError :message="errors.email" />
            </div>

            <!-- Informasi KKN -->
            <div class="grid gap-2">
                <Label for="university">Universitas / Perguruan Tinggi</Label>
                <Input
                    id="university"
                    type="text"
                    required
                    :tabindex="3"
                    name="university"
                    placeholder="Nama universitas Anda"
                />
                <InputError :message="errors.university" />
            </div>

            <div class="grid gap-2">
                <Label for="group_number">Nomor / Nama Kelompok KKN</Label>
                <Input
                    id="group_number"
                    type="text"
                    required
                    :tabindex="4"
                    name="group_number"
                    placeholder="Contoh: Kelompok 12 atau Posko Gedangan"
                />
                <InputError :message="errors.group_number" />
            </div>

            <div class="grid gap-2">
                <Label for="kkn_address">Alamat Posko KKN</Label>
                <textarea
                    id="kkn_address"
                    required
                    :tabindex="5"
                    name="kkn_address"
                    rows="3"
                    placeholder="Alamat lengkap posko KKN kelompok Anda"
                    class="flex min-h-[80px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                ></textarea>
                <InputError :message="errors.kkn_address" />
            </div>

            <!-- Kata Sandi -->
            <div class="grid gap-2">
                <Label for="password">Kata Sandi</Label>
                <PasswordInput
                    id="password"
                    required
                    :tabindex="6"
                    autocomplete="new-password"
                    name="password"
                    placeholder="Masukkan kata sandi"
                    v-model="password"
                />
                
                <!-- Password Strength Indicators -->
                <div class="mt-2 space-y-1.5 text-xs text-muted-foreground">
                    <div class="flex items-center gap-2">
                        <span :class="['h-2 w-2 rounded-full', hasMinLength ? 'bg-green-500' : 'bg-slate-350']"></span>
                        <span :class="hasMinLength ? 'text-green-600 dark:text-green-400 font-medium' : ''">Minimal 8 karakter</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span :class="['h-2 w-2 rounded-full', hasUppercase ? 'bg-green-500' : 'bg-slate-350']"></span>
                        <span :class="hasUppercase ? 'text-green-600 dark:text-green-400 font-medium' : ''">Mengandung huruf besar (A-Z)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span :class="['h-2 w-2 rounded-full', hasLowercase ? 'bg-green-500' : 'bg-slate-350']"></span>
                        <span :class="hasLowercase ? 'text-green-600 dark:text-green-400 font-medium' : ''">Mengandung huruf kecil (a-z)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span :class="['h-2 w-2 rounded-full', hasNumber ? 'bg-green-500' : 'bg-slate-350']"></span>
                        <span :class="hasNumber ? 'text-green-600 dark:text-green-400 font-medium' : ''">Mengandung angka (0-9)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span :class="['h-2 w-2 rounded-full', hasSymbol ? 'bg-green-500' : 'bg-slate-350']"></span>
                        <span :class="hasSymbol ? 'text-green-600 dark:text-green-400 font-medium' : ''">Mengandung simbol atau karakter khusus</span>
                    </div>
                </div>
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">Konfirmasi Kata Sandi</Label>
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="7"
                    autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="Ulangi kata sandi"
                    v-model="passwordConfirmation"
                />
                
                <!-- Confirmation matches indicator -->
                <div v-if="passwordConfirmation" class="mt-1 flex items-center gap-2 text-xs">
                    <span :class="['h-2 w-2 rounded-full', isMatched ? 'bg-green-500' : 'bg-red-500']"></span>
                    <span :class="isMatched ? 'text-green-600 dark:text-green-400 font-medium' : 'text-red-600 font-medium'">
                        {{ isMatched ? 'Kata sandi cocok' : 'Kata sandi belum cocok' }}
                    </span>
                </div>
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-2 w-full"
                tabindex="8"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" />
                Daftar Akun
            </Button>

            <div class="relative flex py-2 items-center">
                <div class="flex-grow border-t border-muted"></div>
                <span class="flex-shrink mx-4 text-xs text-muted-foreground uppercase">Atau</span>
                <div class="flex-grow border-t border-muted"></div>
            </div>

            <a
                href="/auth/google"
                class="flex items-center justify-center gap-2 w-full rounded-md border border-input bg-background px-4 py-2.5 text-sm font-medium hover:bg-accent hover:text-accent-foreground transition-colors duration-200"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Daftar dengan Google
            </a>
        </div>

        <div class="text-center text-sm text-muted-foreground">
            Sudah punya akun?
            <TextLink
                :href="login()"
                class="underline underline-offset-4"
                :tabindex="9"
                >Masuk</TextLink
            >
        </div>
    </Form>
</template>
