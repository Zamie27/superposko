<script setup lang="ts">
import { Head, usePage, useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { computed, watch, onMounted, ref } from 'vue';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { useToast } from '@/composables/useToast';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pengaturan Profil',
                href: edit(),
            },
        ],
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user as any);

// Name Form
const nameForm = useForm({
    name: user.value.name,
    email: user.value.email, // backend requires email in validation rules
    university: user.value.university || '',
    npm: user.value.npm || '',
    group_number: user.value.group_number || '',
    kkn_address: user.value.kkn_address || '',
});

const submitName = () => {
    nameForm.patch('/settings/profile', {
        preserveScroll: true,
    });
};

// Email OTP Form
const emailForm = useForm({
    email: '',
});

const otpForm = useForm({
    email: '',
    otp: '',
});

// Sync OTP state from page props
const otpSent = computed(() => page.props.otp_sent);
const newEmailAttempt = computed(() => page.props.new_email_attempt as string);

watch(newEmailAttempt, (newVal) => {
    if (newVal) {
        otpForm.email = newVal;
        emailForm.email = newVal;
    }
}, { immediate: true });

const sendOtp = () => {
    emailForm.post('/settings/profile/email-otp', {
        preserveScroll: true,
    });
};

const verifyOtp = () => {
    otpForm.put('/settings/profile/email-change', {
        preserveScroll: true,
        onSuccess: () => {
            emailForm.reset();
            otpForm.reset();
        },
    });
};

const toast = useToast();
const isNotificationSupported = ref('serviceWorker' in navigator && 'PushManager' in window);
const isSubscribed = ref(false);
const isPushLoading = ref(false);

const getCookie = (name: string): string => {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);

    if (parts.length === 2) {
        return decodeURIComponent(parts.pop()?.split(';').shift() || '');
    }

    return '';
};

const urlBase64ToUint8Array = (base64String: string) => {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }

    return outputArray;
};

// Check initial push subscription status
onMounted(async () => {
    if (!isNotificationSupported.value) {
return;
}

    try {
        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();
        isSubscribed.value = !!subscription;
    } catch (e) {
        console.error('Gagal memuat status push subscription:', e);
    }
});

const togglePushSubscription = async () => {
    if (!isNotificationSupported.value) {
return;
}

    isPushLoading.value = true;

    try {
        const registration = await navigator.serviceWorker.ready;
        const currentSubscription = await registration.pushManager.getSubscription();

        if (currentSubscription) {
            // Unsubscribe from browser push manager
            await currentSubscription.unsubscribe();
            
            // Delete from database
            await fetch('/push-subscriptions', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    endpoint: currentSubscription.endpoint
                })
            });
            isSubscribed.value = false;
            toast.success('Notifikasi push browser berhasil dinonaktifkan.');
        } else {
            // Request permissions
            const permission = await Notification.requestPermission();

            if (permission !== 'granted') {
                toast.error('Izin notifikasi ditolak oleh pengguna.');

                return;
            }

            // Retrieve VAPID public key shared globally via Inertia
            const vapidKey = page.props.vapid_public_key as string;

            if (!vapidKey) {
                toast.error('Kunci VAPID belum terkonfigurasi di server.');

                return;
            }

            const convertedVapidKey = urlBase64ToUint8Array(vapidKey);
            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: convertedVapidKey
            });

            // Store in database
            const response = await fetch('/push-subscriptions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify(subscription)
            });

            if (response.ok) {
                isSubscribed.value = true;
                toast.success('Notifikasi push browser berhasil diaktifkan!');
            } else {
                toast.error('Gagal mendaftarkan notifikasi ke server.');
            }
        }
    } catch (e) {
        console.error('Gagal mengelola push subscription:', e);
        toast.error('Terjadi kesalahan saat mengaktifkan notifikasi.');
    } finally {
        isPushLoading.value = false;
    }
};
</script>

<template>
    <Head title="Pengaturan Profil" />

    <h1 class="sr-only">Pengaturan Profil</h1>

    <div class="flex flex-col space-y-8 font-sans">
        <!-- 1. Form Nama & Informasi Umum -->
        <div class="space-y-4">
            <Heading
                variant="small"
                title="Profil Umum"
                description="Perbarui informasi profil dan detail KKN Anda"
            />

            <form @submit.prevent="submitName" class="space-y-6 max-w-xl">
                <div class="grid gap-2">
                    <Label for="name">Nama Lengkap</Label>
                    <Input
                        id="name"
                        class="mt-1 block w-full"
                        v-model="nameForm.name"
                        required
                        autocomplete="name"
                        placeholder="Nama Lengkap"
                    />
                    <InputError class="mt-2" :message="nameForm.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="university">Asal Universitas</Label>
                    <Input
                        id="university"
                        class="mt-1 block w-full"
                        v-model="nameForm.university"
                        required
                        placeholder="Asal Universitas"
                    />
                    <InputError class="mt-2" :message="nameForm.errors.university" />
                </div>

                <div class="grid gap-2">
                    <Label for="npm">NPM / NIM</Label>
                    <Input
                        id="npm"
                        class="mt-1 block w-full"
                        v-model="nameForm.npm"
                        required
                        placeholder="NPM / NIM"
                    />
                    <InputError class="mt-2" :message="nameForm.errors.npm" />
                </div>

                <div class="grid gap-2">
                    <Label for="group_number">Nomor / Kelompok KKN</Label>
                    <Input
                        id="group_number"
                        class="mt-1 block w-full"
                        v-model="nameForm.group_number"
                        required
                        placeholder="Nomor / Kelompok KKN"
                    />
                    <InputError class="mt-2" :message="nameForm.errors.group_number" />
                </div>

                <div class="grid gap-2">
                    <Label for="kkn_address">Alamat Posko KKN</Label>
                    <Input
                        id="kkn_address"
                        class="mt-1 block w-full"
                        v-model="nameForm.kkn_address"
                        required
                        placeholder="Alamat Posko KKN"
                    />
                    <InputError class="mt-2" :message="nameForm.errors.kkn_address" />
                </div>

                <div class="flex items-center gap-4">
                    <Button :disabled="nameForm.processing" class="bg-sky-500 hover:bg-sky-600 text-white font-bold cursor-pointer">
                        <Spinner v-if="nameForm.processing" />
                        Simpan Perubahan
                    </Button>
                </div>
            </form>
        </div>

        <hr class="border-slate-100" />

        <!-- 2. Form Ganti Email dengan OTP -->
        <div class="space-y-4">
            <Heading
                variant="small"
                title="Alamat Email"
                description="Ubah email akun dengan verifikasi OTP aman"
            />

            <div class="max-w-xl space-y-6">
                <!-- Tampilkan Email Aktif -->
                <div class="grid gap-1.5 p-4 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-sm">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Email Aktif Saat Ini</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ user.email }}</span>
                </div>

                <!-- Input Email Baru & Kirim OTP -->
                <div class="flex gap-2 items-end">
                    <div class="grid gap-2 flex-grow">
                        <Label for="new_email">Email Baru</Label>
                        <Input
                            id="new_email"
                            type="email"
                            v-model="emailForm.email"
                            required
                            placeholder="emailbaru@example.com"
                        />
                    </div>
                    <Button
                        type="button"
                        @click="sendOtp"
                        :disabled="emailForm.processing"
                        class="bg-sky-500 hover:bg-sky-600 text-white font-bold cursor-pointer"
                    >
                        <Spinner v-if="emailForm.processing" />
                        Kirim OTP
                    </Button>
                </div>
                <InputError :message="emailForm.errors.email" />

                <!-- Input OTP Verification Form (Tampil jika OTP sudah dikirim) -->
                <div
                    v-if="otpSent"
                    class="space-y-4 p-5 bg-sky-50/50 dark:bg-sky-950/20 border border-sky-100 dark:border-sky-900/50 rounded-2xl animate-in fade-in slide-in-from-top-4 duration-300"
                >
                    <div class="grid gap-3">
                        <div class="text-center">
                            <Label for="otp" class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                Masukkan 6-Digit Kode OTP
                            </Label>
                            <p class="text-xs text-slate-500 mt-1">
                                Kode telah dikirimkan ke <strong>{{ newEmailAttempt }}</strong>. Silakan periksa kotak masuk atau spam email Anda.
                            </p>
                        </div>
                        <Input
                            v-model="otpForm.otp"
                            id="otp"
                            type="text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            maxlength="6"
                            class="text-center text-lg font-bold tracking-[1em] pl-[1em] py-6 w-48 mx-auto bg-white"
                            placeholder="000000"
                            required
                        />
                        <InputError class="mt-2 text-center" :message="otpForm.errors.otp" />
                    </div>
                    <Button
                        type="button"
                        @click="verifyOtp"
                        :disabled="otpForm.processing"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-6 cursor-pointer"
                    >
                        <Spinner v-if="otpForm.processing" />
                        Verifikasi & Ubah Email
                    </Button>
                </div>

                <!-- Resend & Status Link for Email Verification (Standard Laravel) -->
                <div v-if="page.props.mustVerifyEmail && !user.email_verified_at" class="mt-4">
                    <p class="text-xs text-muted-foreground">
                        Email akun Anda belum terverifikasi secara global.
                        <Link
                            :href="send()"
                            as="button"
                            class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                        >
                            Kirim ulang email verifikasi akun.
                        </Link>
                    </p>

                    <div
                        v-if="page.props.status === 'verification-link-sent'"
                        class="mt-2 text-xs font-medium text-green-600"
                    >
                        Link verifikasi baru telah dikirim ke alamat email Anda.
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-slate-100" />

        <!-- 3. Push Notification Settings -->
        <div class="space-y-4" v-if="isNotificationSupported">
            <Heading
                variant="small"
                title="Notifikasi Push Browser (PWA)"
                description="Terima pemberitahuan instan saat server bermasalah atau pengumuman penting langsung di perangkat ini."
            />

            <div class="max-w-xl space-y-4">
                <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl text-sm">
                    <div class="flex flex-col gap-0.5">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Notifikasi Browser Ini</span>
                        <span class="font-bold" :class="isSubscribed ? 'text-green-600' : 'text-amber-600'">
                            {{ isSubscribed ? 'Aktif (Menerima Notifikasi)' : 'Nonaktif (Belum Terdaftar)' }}
                        </span>
                    </div>
                    <Button
                        type="button"
                        @click="togglePushSubscription"
                        :disabled="isPushLoading"
                        :class="isSubscribed ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-sky-500 hover:bg-sky-600 text-white'"
                        class="font-bold cursor-pointer"
                    >
                        <Spinner v-if="isPushLoading" />
                        {{ isSubscribed ? 'Nonaktifkan' : 'Aktifkan' }}
                    </Button>
                </div>
            </div>
        </div>
    </div>

    <DeleteUser />
</template>
