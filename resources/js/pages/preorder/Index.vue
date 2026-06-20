<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { CheckCircle, AlertCircle, Upload, Phone, Mail, User, Info, ArrowLeft } from '@lucide/vue';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { dashboard } from '@/routes';

const props = defineProps<{
    preorderPrice: number;
    preorderStrikePrice: number;
    existingPreorder: {
        status: string;
        created_at: string;
    } | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Preorder Langganan',
                href: '/user/preorder',
            },
        ],
    },
});

const fileInput = ref<HTMLInputElement | null>(null);
const previewUrl = ref<string | null>(null);

// Get current user details from Inertia page props
const currentUser = ref<any>((window as any).Laravel?.user || {});

const form = useForm({
    name: '',
    email: '',
    whatsapp: '',
    payment_proof: null as File | null,
});

// Init form autofill
const initForm = () => {
    // We can also extract this from Inertia's global props
    // eslint-disable-next-line
    const user = (window as any).Inertia?.page?.props?.auth?.user || {};
    form.name = user.name || '';
    form.email = user.email || '';
};

import { onMounted } from 'vue';
onMounted(() => {
    initForm();
});

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        const file = target.files[0];
        form.payment_proof = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const triggerFileSelect = () => {
    fileInput.value?.click();
};

const submitPreorder = () => {
    form.post('/user/preorder', {
        forceFormData: true,
        onSuccess: () => {
            form.reset('whatsapp', 'payment_proof');
            previewUrl.value = null;
        },
    });
};
</script>

<template>
    <Head title="Preorder Langganan SuperPosko" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-4xl mx-auto font-sans">
        <div class="flex items-center gap-3">
            <Link :href="dashboard()" class="rounded-lg p-2 hover:bg-slate-100 text-slate-500 transition-colors">
                <ArrowLeft class="size-5" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Preorder Langganan SuperPosko</h1>
                <p class="text-sm text-slate-500">Dapatkan akses full SaaS posko KKN dengan harga promosi miring.</p>
            </div>
        </div>

        <!-- If preorder already exists -->
        <div v-if="existingPreorder" class="rounded-2xl border p-6 bg-white shadow-sm space-y-4">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-xl" :class="{
                    'bg-amber-50 text-amber-600': existingPreorder.status === 'pending',
                    'bg-green-50 text-green-600': existingPreorder.status === 'approved',
                    'bg-red-50 text-red-600': existingPreorder.status === 'rejected',
                }">
                    <AlertCircle v-if="existingPreorder.status === 'pending'" class="size-6" />
                    <CheckCircle v-else-if="existingPreorder.status === 'approved'" class="size-6" />
                    <AlertCircle v-else class="size-6" />
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">Status Pengajuan Preorder Anda</h3>
                    <p class="text-xs text-slate-500">Dikirim pada {{ new Date(existingPreorder.created_at).toLocaleDateString('id-ID', { dateStyle: 'long' }) }}</p>
                </div>
            </div>

            <div class="p-4 rounded-xl border" :class="{
                'border-amber-200 bg-amber-50/30 text-amber-900': existingPreorder.status === 'pending',
                'border-green-200 bg-green-50/30 text-green-900': existingPreorder.status === 'approved',
                'border-red-200 bg-red-50/30 text-red-900': existingPreorder.status === 'rejected',
            }">
                <p v-if="existingPreorder.status === 'pending'" class="text-sm">
                    <strong>Preorder Pending:</strong> Bukti transfer Anda sedang kami tinjau. Akun Anda akan otomatis diaktifkan menjadi Host segera setelah verifikasi selesai (biasanya kurang dari 24 jam).
                </p>
                <p v-else-if="existingPreorder.status === 'approved'" class="text-sm">
                    <strong>Preorder Disetujui:</strong> Akun Anda telah sukses dipromosikan menjadi Host. Anda sekarang memiliki akses penuh ke seluruh fitur premium SuperPosko. Terima kasih!
                </p>
                <p v-else class="text-sm">
                    <strong>Preorder Ditolak:</strong> Maaf, bukti pembayaran Anda tidak dapat divalidasi. Silakan hubungi admin di kuukok.id@gmail.com jika ini merupakan kesalahan.
                </p>
            </div>

            <div class="pt-2 flex justify-end" v-if="existingPreorder.status === 'approved'">
                <Link :href="dashboard()" class="rounded-xl bg-sky-500 text-white font-bold px-6 py-2.5 hover:bg-sky-600 transition">
                    Kembali ke Dashboard
                </Link>
            </div>
        </div>

        <!-- Form Preorder -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Side: QRIS & Instructions -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-between">
                <div class="space-y-4">
                    <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-600">
                        <Info class="size-3.5" /> Pembayaran QRIS Flat
                    </span>
                    <h3 class="text-lg font-bold text-slate-900">Scan QRIS Untuk Preorder</h3>
                    <div class="flex items-baseline gap-2">
                        <span class="text-xs line-through text-slate-400">Rp {{ preorderStrikePrice.toLocaleString('id-ID') }}</span>
                        <span class="text-2xl font-extrabold text-slate-950">Rp {{ preorderPrice.toLocaleString('id-ID') }}</span>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Silakan scan kode QRIS di bawah menggunakan e-wallet (Gopay, OVO, Dana, LinkAja) atau m-Banking Anda. Pastikan nominal transfer sesuai dengan harga preorder di atas.
                    </p>

                    <!-- QRIS Image Container -->
                    <div class="rounded-xl border p-3 bg-slate-50 flex items-center justify-center">
                        <img src="/images/qris.jpg" alt="QRIS SuperPosko" class="max-w-[220px] h-auto rounded-lg shadow-sm border" />
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t text-xs text-slate-400 text-center">
                    GPN QRIS - Muhammad Ridho Al Zamzami, Digital & Kreatif
                </div>
            </div>

            <!-- Right Side: Submission Form -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900 mb-5">Formulir Pendaftaran Preorder</h3>

                <form @submit.prevent="submitPreorder" class="space-y-4">
                    <!-- Name Input -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                            <User class="size-3.5" /> Nama Lengkap
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Nama Anda"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                            required
                        />
                        <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <!-- Email Input -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                            <Mail class="size-3.5" /> Alamat Email
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="Email Anda"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                            required
                        />
                        <p v-if="form.errors.email" class="text-xs text-red-500">{{ form.errors.email }}</p>
                    </div>

                    <!-- WhatsApp Input -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                            <Phone class="size-3.5" /> Nomor WhatsApp
                        </label>
                        <input
                            v-model="form.whatsapp"
                            type="text"
                            placeholder="Contoh: 08123456789"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                            required
                        />
                        <p v-if="form.errors.whatsapp" class="text-xs text-red-500">{{ form.errors.whatsapp }}</p>
                    </div>

                    <!-- Payment Proof Screenshot -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                            <Upload class="size-3.5" /> Bukti Pembayaran (Screenshot)
                        </label>
                        
                        <!-- Upload Button/Area -->
                        <div 
                            @click="triggerFileSelect"
                            class="border-2 border-dashed border-slate-200 rounded-xl p-5 text-center cursor-pointer hover:bg-slate-50/50 transition flex flex-col items-center justify-center gap-2"
                        >
                            <input 
                                ref="fileInput" 
                                type="file" 
                                class="hidden" 
                                accept="image/*"
                                @change="handleFileChange"
                            />
                            
                            <template v-if="!previewUrl">
                                <Upload class="size-8 text-slate-400" />
                                <span class="text-xs font-medium text-slate-600">Klik untuk mengunggah gambar</span>
                                <span class="text-[10px] text-slate-400">PNG, JPG, JPEG (Maks. 2MB)</span>
                            </template>
                            <template v-else>
                                <img :src="previewUrl" alt="Preview bukti transfer" class="max-h-28 object-contain rounded-lg border shadow-sm" />
                                <span class="text-xs text-sky-600 font-semibold underline">Ganti Gambar</span>
                            </template>
                        </div>
                        <p v-if="form.errors.payment_proof" class="text-xs text-red-500">{{ form.errors.payment_proof }}</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 rounded-xl transition duration-200 shadow-sm flex items-center justify-center gap-2"
                        >
                            <Spinner v-if="form.processing" class="size-4" />
                            Kirim Formulir Preorder
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
