<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ChevronLeft, Send } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

const props = defineProps<{
    defaultEmail: string;
    defaultType: string;
    defaultTitle: string;
    defaultDesc: string;
}>();

const form = useForm({
    email: props.defaultEmail,
    type: props.defaultType,
    title: props.defaultTitle,
    description: props.defaultDesc,
});

const submitReport = () => {
    form.post('/laporan/buat', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('title', 'description');
        },
    });
};
</script>

<template>
    <Head title="Kirim Laporan Pengaduan" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Back to Home -->
            <div class="mb-6 flex justify-start">
                <Link href="/" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-500 hover:text-slate-700 transition">
                    <ChevronLeft class="size-4" /> Kembali ke Beranda
                </Link>
            </div>

            <!-- Header -->
            <h2 class="text-center text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">
                Pusat Pengaduan & Laporan
            </h2>
            <p class="mt-2 text-center text-sm text-slate-500 dark:text-slate-400">
                Hubungi tim administrasi kami jika Anda menemui kendala teknis, keluhan layanan, atau masalah keamanan akun.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-xl">
            <div class="bg-white dark:bg-slate-900 py-8 px-4 shadow-xl rounded-2xl border border-slate-100 dark:border-slate-800 sm:px-10">
                <form @submit.prevent="submitReport" class="space-y-6">
                    <!-- Email -->
                    <div class="grid gap-2">
                        <Label for="email">Alamat Email Anda</Label>
                        <Input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            placeholder="nama@email.com"
                        />
                        <p class="text-[11px] text-slate-400">Kami akan menghubungi Anda kembali melalui alamat email ini.</p>
                        <span v-if="form.errors.email" class="text-xs text-red-500">{{ form.errors.email }}</span>
                    </div>

                    <!-- Type -->
                    <div class="grid gap-2">
                        <Label for="type">Jenis Pengaduan / Masalah</Label>
                        <select
                            id="type"
                            v-model="form.type"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3.5 py-2.5 text-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:border-sky-500 focus:outline-none"
                            required
                        >
                            <option value="complaint">Keluhan / Pertanyaan Umum</option>
                            <option value="bug">Masalah Aplikasi (Bug / Error)</option>
                            <option value="security">Masalah Keamanan (Akun Kompromi / Hack)</option>
                        </select>
                        <span v-if="form.errors.type" class="text-xs text-red-500">{{ form.errors.type }}</span>
                    </div>

                    <!-- Title -->
                    <div class="grid gap-2">
                        <Label for="title">Judul Laporan</Label>
                        <Input
                            id="title"
                            type="text"
                            v-model="form.title"
                            required
                            placeholder="Contoh: Gagal verifikasi email / Error di halaman keuangan"
                        />
                        <span v-if="form.errors.title" class="text-xs text-red-500">{{ form.errors.title }}</span>
                    </div>

                    <!-- Description -->
                    <div class="grid gap-2">
                        <Label for="description">Rincian Laporan (Teks Detail)</Label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="6"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3.5 py-2.5 text-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:border-sky-500 focus:outline-none"
                            required
                            placeholder="Tuliskan kronologi, langkah-langkah memicu error, atau keluhan Anda secara detail di sini..."
                        ></textarea>
                        <span v-if="form.errors.description" class="text-xs text-red-500">{{ form.errors.description }}</span>
                    </div>

                    <!-- Submit Button -->
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-6 rounded-xl flex items-center justify-center gap-2 cursor-pointer transition"
                    >
                        <Spinner v-if="form.processing" />
                        <Send class="size-4" /> Kirim Laporan Pengaduan
                    </Button>
                </form>
            </div>
        </div>
    </div>
</template>
