<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Loader2, CheckCircle2, AlertCircle, MapPin } from '@lucide/vue';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    user: any;
}>();

const status = ref<'loading' | 'error'>('loading');
const errorMessage = ref('');

const processQrAttendance = () => {
    status.value = 'loading';
    errorMessage.value = '';

    const submitWithCoords = (lat?: number, lng?: number) => {
        router.post('/absensi/scan-qr', {
            latitude: lat ?? null,
            longitude: lng ?? null,
        }, {
            onError: (errors) => {
                status.value = 'error';
                errorMessage.value = errors.message || 'Gagal memproses absensi QR Code.';
            }
        });
    };

    if (!navigator.geolocation) {
        // Fallback without coords
        submitWithCoords();
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            submitWithCoords(position.coords.latitude, position.coords.longitude);
        },
        (err) => {
            // If GPS permission denied or timeout, fallback submit so user isn't stuck
            submitWithCoords();
        },
        { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
    );
};

onMounted(() => {
    processQrAttendance();
});
</script>

<template>
    <Head title="Proses Absensi QR Code" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 flex flex-col items-center justify-center p-4 font-sans">
        <div class="w-full max-w-md bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 shadow-xl text-center flex flex-col items-center">
            
            <!-- Logo Header -->
            <div class="mb-6 flex items-center justify-center gap-3">
                <img src="/logo_superposko.png" alt="SuperPosko" class="h-10 object-contain" />
            </div>

            <!-- Loading State -->
            <template v-if="status === 'loading'">
                <div class="w-16 h-16 bg-sky-50 dark:bg-sky-950/50 rounded-2xl flex items-center justify-center text-sky-500 mb-6 shadow-inner animate-pulse">
                    <Loader2 class="w-8 h-8 animate-spin" />
                </div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Memproses Absensi QR Code...</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                    Halo <strong>{{ user.name }}</strong>, sistem sedang secara otomatis mengunggah data presensi Hadir Anda beserta koordinat lokasi GPS.
                </p>
                <div class="mt-6 flex items-center justify-center gap-2 text-xs font-semibold text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/40 px-4 py-2 rounded-full">
                    <MapPin class="w-4 h-4 animate-bounce" />
                    Mendeteksi lokasi GPS...
                </div>
            </template>

            <!-- Error State -->
            <template v-else-if="status === 'error'">
                <div class="w-16 h-16 bg-red-50 dark:bg-red-950/50 rounded-2xl flex items-center justify-center text-red-500 mb-6 shadow-inner">
                    <AlertCircle class="w-8 h-8" />
                </div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Gagal Memproses Absensi</h2>
                <p class="text-sm text-red-600 dark:text-red-400 mt-2">
                    {{ errorMessage || 'Terjadi kesalahan saat memproses absensi.' }}
                </p>
                <div class="mt-6 flex flex-col gap-3 w-full">
                    <Button @click="processQrAttendance" class="w-full h-12 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-xl">
                        Coba Lagi
                    </Button>
                    <a href="/absensi" class="text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 py-2">
                        Kembali ke Halaman Absensi
                    </a>
                </div>
            </template>

        </div>
    </div>
</template>
