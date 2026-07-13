<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { ref } from 'vue';

const props = defineProps<{
    todayAttendance: any;
    recap: any[];
    isLeader: boolean;
    hasImmichConfig: boolean;
}>();

const form = useForm({
    photo: null as File | null,
});

const fileInput = ref<HTMLInputElement | null>(null);
const previewUrl = ref<string | null>(null);

const handlePhotoUpload = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.photo = target.files[0];
        
        // Preview
        const reader = new FileReader();
        reader.onload = (e) => {
            previewUrl.value = e.target?.result as string;
        };
        reader.readAsDataURL(target.files[0]);
    }
};

const submitAttendance = () => {
    if (!form.photo) return;
    
    form.post(route('attendance.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            previewUrl.value = null;
            if (fileInput.value) fileInput.value.value = '';
        }
    });
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Absensi', href: '/absensi' },
        ],
    },
});
</script>

<template>
    <Head title="Absensi Harian" />

    <div class="relative flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6 min-h-[400px]">
        
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Absensi Harian</h1>
                <p class="text-sm text-slate-500 mt-1">Lakukan absen setiap hari dengan mengunggah foto (disarankan menggunakan kamera Timestamp).</p>
            </div>
        </div>

        <!-- Warning If Immich Not Configured -->
        <div v-if="!hasImmichConfig" class="bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/50 rounded-2xl p-5">
            <h3 class="text-sm font-bold text-red-800 dark:text-red-300">Penyimpanan Belum Dikonfigurasi</h3>
            <p class="text-xs text-red-700 dark:text-red-400 mt-1">
                Absensi memerlukan penyimpanan dokumentasi (Immich) yang dikonfigurasi. Hubungi Ketua kelompok Anda untuk mengaturnya di menu Pengaturan.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Absen Box -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6">
                <h3 class="text-lg font-bold mb-4">Absen Hari Ini</h3>

                <div v-if="todayAttendance" class="bg-green-50 dark:bg-green-950/30 border border-green-200 p-6 rounded-xl text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900 text-green-600 rounded-full mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                    <h4 class="font-bold text-green-800 dark:text-green-300">Anda sudah absen!</h4>
                    <p class="text-sm text-green-600 mt-1">Tercatat pada {{ todayAttendance.time }}</p>
                </div>

                <form v-else @submit.prevent="submitAttendance" class="space-y-4">
                    <div 
                        class="border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-xl text-center cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition relative overflow-hidden group"
                        :class="previewUrl ? 'min-h-[300px]' : 'p-8'"
                        @click="fileInput?.click()"
                    >
                        <input 
                            type="file" 
                            accept="image/*" 
                            capture="user" 
                            class="hidden" 
                            ref="fileInput" 
                            @change="handlePhotoUpload"
                        />
                        
                        <template v-if="previewUrl">
                            <img :src="previewUrl" class="w-full h-auto object-contain bg-slate-100 dark:bg-slate-800" />
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="text-white font-medium text-sm">Ganti Foto</span>
                            </div>
                        </template>
                        <template v-else>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mx-auto text-slate-400 mb-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                            </svg>
                            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Ambil/Pilih Foto Absen</p>
                            <p class="text-xs text-slate-400 mt-1">Sangat disarankan memakai kamera ber-timestamp (seperti Timemark/OpenCamera)</p>
                        </template>
                    </div>
                    <InputError :message="form.errors.photo" />

                    <Button 
                        type="submit" 
                        class="w-full bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white font-bold" 
                        :disabled="!form.photo || form.processing || !hasImmichConfig"
                    >
                        {{ form.processing ? 'Mengunggah...' : 'Kirim Absensi' }}
                    </Button>
                </form>
            </div>

            <!-- Rekap Absen Box (Leader Only) -->
            <div v-if="isLeader" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 flex flex-col h-[500px]">
                <h3 class="text-lg font-bold mb-4">Rekap Absensi Kelompok</h3>

                <div class="flex-1 overflow-y-auto pr-2 space-y-3">
                    <div v-if="recap.length === 0" class="text-sm text-center text-slate-500 py-8">
                        Belum ada data absensi.
                    </div>
                    <div 
                        v-for="item in recap" 
                        :key="item.id" 
                        class="flex items-center gap-4 p-3 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50"
                    >
                        <!-- Immich Thumbnail Proxy (we reuse DocumentationController thumbnail if possible, or just link to it) -->
                        <div class="w-12 h-12 bg-slate-200 dark:bg-slate-800 rounded-lg shrink-0 overflow-hidden">
                            <img v-if="item.immich_asset_id" :src="`/host/documentation/thumbnail/${item.immich_asset_id}`" class="w-full h-full object-cover" />
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 dark:text-slate-200 truncate">{{ item.user.name }}</p>
                            <p class="text-xs text-slate-500 capitalize">{{ item.user.role === 'lainnya' ? (item.user.custom_role?.name ?? 'Role Kustom') : item.user.role }}</p>
                        </div>
                        
                        <div class="text-right shrink-0">
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ item.date }}</p>
                            <p class="text-[10px] text-slate-500">{{ item.time }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
