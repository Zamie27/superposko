<script setup lang="ts">
import { Head, useForm, router, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    InputOTP,
    InputOTPGroup,
    InputOTPSlot,
} from '@/components/ui/input-otp';
import { Spinner } from '@/components/ui/spinner';
import { logout } from '@/routes';

defineOptions({
    layout: {
        title: 'Verifikasi Akun',
        description: 'Silakan masukkan 6 digit kode OTP yang telah kami kirimkan ke email Anda.',
    },
});

defineProps<{
    status?: string;
}>();

const page = usePage();
const email = computed(() => (page.props.auth as any)?.user?.email || '');

const otpCode = ref('');
const form = useForm({
    otp: '',
});

const submitVerify = () => {
    form.otp = otpCode.value;
    form.post('/email/verify-otp', {
        onFinish: () => {
            otpCode.value = '';
        }
    });
};

const resendForm = useForm({});
const submitResend = () => {
    resendForm.post('/email/resend-otp');
};

const handleLogout = () => {
    router.flushAll();
};
</script>

<template>
    <Head title="Verifikasi OTP" />

    <div class="space-y-6">
        <!-- Display Registered Email -->
        <div class="text-center text-sm text-slate-600 bg-slate-50 rounded-xl p-3 border border-slate-100/60">
            Kode OTP dikirim ke: <span class="font-bold text-slate-800 break-all">{{ email }}</span>
        </div>

        <!-- OTP Input Form -->
        <form @submit.prevent="submitVerify" class="space-y-4">
            <div class="flex flex-col items-center justify-center space-y-3 text-center">
                <div class="flex w-full items-center justify-center">
                    <InputOTP
                        id="otp"
                        v-model="otpCode"
                        :maxlength="6"
                        :disabled="form.processing"
                        autofocus
                    >
                        <InputOTPGroup>
                            <InputOTPSlot
                                v-for="index in 6"
                                :key="index"
                                :index="index - 1"
                            />
                        </InputOTPGroup>
                    </InputOTP>
                </div>
                <InputError :message="form.errors.otp" />
            </div>

            <Button type="submit" class="w-full" :disabled="form.processing || otpCode.length < 6">
                <Spinner v-if="form.processing" />
                Verifikasi Akun
            </Button>
        </form>

        <!-- Information Info Box -->
        <div class="rounded-xl bg-slate-50 border border-slate-100 p-4 text-xs text-slate-500 space-y-2 leading-relaxed">
            <p class="font-semibold text-slate-700">Tidak menerima email?</p>
            <ul class="list-disc pl-4 space-y-1 text-slate-500">
                <li>Silakan periksa folder <strong>Spam</strong> atau <strong>Junk</strong> di email Anda.</li>
                <li>Pastikan alamat email yang Anda masukkan saat mendaftar sudah benar.</li>
                <li>Tunggu beberapa menit sebelum meminta kode OTP yang baru.</li>
            </ul>
        </div>

        <!-- Resend and Logout Actions -->
        <div class="flex flex-col gap-3 items-center text-center">
            <form @submit.prevent="submitResend" class="w-full">
                <button
                    type="submit"
                    class="text-xs font-semibold text-slate-600 hover:text-slate-900 underline disabled:opacity-50"
                    :disabled="resendForm.processing"
                >
                    <Spinner v-if="resendForm.processing" class="inline size-3 mr-1" />
                    Kirim Ulang Kode OTP
                </button>
            </form>

            <Link
                class="text-xs font-semibold text-sky-600 hover:text-sky-800 underline cursor-pointer"
                href="/logout-to-register"
                method="post"
                as="button"
            >
                Email yang Anda masukkan tidak sesuai? Ubah di sini.
            </Link>

            <Link
                class="text-xs text-slate-400 hover:text-slate-600 underline cursor-pointer"
                :href="logout()"
                @click="handleLogout"
                as="button"
                method="post"
            >
                Keluar / Ganti Akun
            </Link>
        </div>
    </div>
</template>
