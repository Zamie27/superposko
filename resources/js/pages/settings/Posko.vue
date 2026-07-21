<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { useToast } from '@/composables/useToast';
import { MapPin, Upload, Building2, Image as ImageIcon, Sparkles, CheckCircle2 } from '@lucide/vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pengaturan Posko',
                href: '/settings/posko',
            },
        ],
    },
});

const props = defineProps<{
    posko: {
        group_number: string;
        full_group_name: string;
        kkn_address: string;
        posko_village: string;
        posko_district: string;
        posko_regency: string;
        posko_province: string;
        posko_postal_code: string;
        posko_logo_url: string | null;
    };
    canEdit: boolean;
}>();

const toast = useToast();
const isLocating = ref(false);
const logoPreview = ref<string | null>(props.posko.posko_logo_url);
const logoInputRef = ref<HTMLInputElement | null>(null);

// Form data posko
const poskoForm = useForm({
    group_number: props.posko.group_number || '',
    posko_village: props.posko.posko_village || '',
    posko_district: props.posko.posko_district || '',
    posko_regency: props.posko.posko_regency || '',
    posko_province: props.posko.posko_province || '',
    posko_postal_code: props.posko.posko_postal_code || '',
    kkn_address: props.posko.kkn_address || '',
});

// Form upload logo
const logoForm = useForm({
    logo: null as File | null,
});

const submitPoskoInfo = () => {
    poskoForm.patch('/settings/posko', {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Data Informasi Posko KKN berhasil disimpan.');
        },
    });
};

// GPS Auto-Fill Address Logic (Reverse Geocoding via Nominatim)
const fetchGpsAddress = () => {
    if (!navigator.geolocation) {
        toast.error('Browser Anda tidak mendukung Geolocation.');
        return;
    }

    isLocating.value = true;

    navigator.geolocation.getCurrentPosition(
        async (position) => {
            const { latitude, longitude } = position.coords;
            try {
                const response = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&accept-language=id`
                );
                const data = await response.json();

                if (data && data.address) {
                    const addr = data.address;
                    
                    // Desa / Kelurahan
                    poskoForm.posko_village = addr.village || addr.suburb || addr.hamlet || addr.quarter || poskoForm.posko_village;
                    // Kecamatan
                    poskoForm.posko_district = addr.city_district || addr.subdistrict || addr.town || poskoForm.posko_district;
                    // Kabupaten / Kota
                    poskoForm.posko_regency = addr.city || addr.regency || addr.county || poskoForm.posko_regency;
                    // Provinsi
                    poskoForm.posko_province = addr.state || poskoForm.posko_province;
                    // Kode Pos
                    poskoForm.posko_postal_code = addr.postcode || poskoForm.posko_postal_code;
                    // Alamat Lengkap
                    if (data.display_name && !poskoForm.kkn_address) {
                        poskoForm.kkn_address = data.display_name;
                    }

                    toast.success('Alamat berhasil terisi otomatis dari GPS lokasi Anda!');
                } else {
                    toast.error('Gagal mendapatkan detail nama wilayah dari GPS.');
                }
            } catch (err) {
                console.error('Reverse geocoding error:', err);
                toast.error('Gagal terhubung ke layanan lokasi.');
            } finally {
                isLocating.value = false;
            }
        },
        (err) => {
            isLocating.value = false;
            toast.error('Izin lokasi ditolak atau GPS tidak aktif.');
        },
        { enableHighAccuracy: true, timeout: 15000 }
    );
};

// Handle file selection for logo
const handleLogoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        logoForm.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submitLogo = () => {
    if (!logoForm.logo) return;

    logoForm.post('/settings/posko/logo', {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Logo Posko KKN berhasil diperbarui!');
            logoForm.reset();
        },
    });
};
</script>

<template>
    <Head title="Pengaturan Kelompok / Posko" />

    <h1 class="sr-only">Pengaturan Kelompok / Posko</h1>

    <div class="flex flex-col space-y-8 font-sans">
        
        <!-- Header & Logo Upload Card -->
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-6">
            <Heading
                variant="small"
                title="Identitas & Logo Kelompok"
                description="Kelola nama kelompok dan logo resmi posko KKN Anda"
            />

            <div class="flex flex-col sm:flex-row items-center gap-6 pt-2">
                <!-- Logo Preview Circle -->
                <div class="relative group shrink-0">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 flex items-center justify-center overflow-hidden shadow-inner">
                        <img 
                            v-if="logoPreview" 
                            :src="logoPreview" 
                            alt="Logo Posko" 
                            class="w-full h-full object-contain p-2"
                        />
                        <div v-else class="flex flex-col items-center text-slate-400 gap-1 p-2 text-center">
                            <Building2 class="w-8 h-8 stroke-[1.5]" />
                            <span class="text-[10px] font-semibold">Belum ada logo</span>
                        </div>
                    </div>
                </div>

                <!-- Logo Action Controls -->
                <form @submit.prevent="submitLogo" class="flex-1 space-y-3 w-full">
                    <div>
                        <Label class="text-xs font-bold text-slate-700 dark:text-slate-300">Unggah Gambar Logo / Icon Kelompok</Label>
                        <p class="text-[11px] text-slate-500 mt-0.5">Format PNG, JPG, SVG, atau WEBP. Maksimal 5MB.</p>
                    </div>

                    <input 
                        ref="logoInputRef"
                        type="file" 
                        accept="image/*" 
                        class="hidden" 
                        @change="handleLogoChange"
                    />

                    <div class="flex flex-wrap items-center gap-3">
                        <Button 
                            type="button" 
                            variant="outline"
                            @click="logoInputRef?.click()"
                            class="border-slate-300 font-bold text-xs cursor-pointer"
                        >
                            <Upload class="w-4 h-4 mr-1.5" />
                            Pilih Berkas Logo
                        </Button>

                        <Button 
                            v-if="logoForm.logo"
                            type="submit"
                            :disabled="logoForm.processing"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs cursor-pointer"
                        >
                            <Spinner v-if="logoForm.processing" />
                            Simpan Logo Baru
                        </Button>
                    </div>
                    <InputError :message="logoForm.errors.logo" />
                </form>
            </div>
        </div>

        <!-- Detail Posko & Alamat Form Card -->
        <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                <Heading
                    variant="small"
                    title="Nomor & Alamat Wilayah Posko"
                    description="Perbarui nomor kelompok dan koordinat wilayah posko KKN"
                />

                <!-- GPS Auto-Fill Button -->
                <Button 
                    type="button"
                    @click="fetchGpsAddress"
                    :disabled="isLocating"
                    class="bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800/60 font-bold text-xs rounded-xl cursor-pointer flex items-center gap-2 shrink-0 py-2.5 px-4 shadow-xs"
                >
                    <Spinner v-if="isLocating" class="w-4 h-4" />
                    <Sparkles v-else class="w-4 h-4 text-emerald-600" />
                    <span>{{ isLocating ? 'Mendeteksi Lokasi...' : 'Isi Otomatis Alamat dari GPS saat ini' }}</span>
                </Button>
            </div>

            <form @submit.prevent="submitPoskoInfo" class="space-y-6">
                <!-- Group Number Input with 'Kelompok' Prefix Label -->
                <div class="grid gap-2 max-w-md">
                    <Label for="group_number">Nomor / Posko KKN</Label>
                    <div class="flex items-center rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden bg-slate-50 dark:bg-slate-950 focus-within:border-sky-500 focus-within:ring-1 focus-within:ring-sky-500 transition">
                        <span class="px-4 py-2.5 text-xs font-extrabold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 select-none uppercase tracking-wider">
                            Kelompok
                        </span>
                        <input
                            id="group_number"
                            type="text"
                            v-model="poskoForm.group_number"
                            required
                            placeholder="Contoh: 18"
                            class="w-full bg-transparent px-4 py-2 text-sm font-bold text-slate-900 dark:text-white focus:outline-none"
                        />
                    </div>
                    <p class="text-[11px] text-slate-500">Cukup ketikkan nomor kelompoknya (misal: 18), sistem akan otomatis menampilkan "Kelompok 18".</p>
                    <InputError :message="poskoForm.errors.group_number" />
                </div>

                <!-- 2-Column Grid for Address Fields -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="posko_village">Desa / Kelurahan</Label>
                        <Input
                            id="posko_village"
                            v-model="poskoForm.posko_village"
                            placeholder="Nama Desa / Kelurahan"
                        />
                        <InputError :message="poskoForm.errors.posko_village" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="posko_district">Kecamatan</Label>
                        <Input
                            id="posko_district"
                            v-model="poskoForm.posko_district"
                            placeholder="Nama Kecamatan"
                        />
                        <InputError :message="poskoForm.errors.posko_district" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="posko_regency">Kabupaten / Kota</Label>
                        <Input
                            id="posko_regency"
                            v-model="poskoForm.posko_regency"
                            placeholder="Nama Kabupaten / Kota"
                        />
                        <InputError :message="poskoForm.errors.posko_regency" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="posko_province">Provinsi</Label>
                        <Input
                            id="posko_province"
                            v-model="poskoForm.posko_province"
                            placeholder="Nama Provinsi"
                        />
                        <InputError :message="poskoForm.errors.posko_province" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="posko_postal_code">Kode Pos</Label>
                        <Input
                            id="posko_postal_code"
                            v-model="poskoForm.posko_postal_code"
                            placeholder="Kode Pos"
                        />
                        <InputError :message="poskoForm.errors.posko_postal_code" />
                    </div>
                </div>

                <!-- Full Address Text Area / Input -->
                <div class="grid gap-2">
                    <Label for="kkn_address">Detail Alamat Posko KKN</Label>
                    <Input
                        id="kkn_address"
                        v-model="poskoForm.kkn_address"
                        placeholder="Alamat lengkap posko (Jalan, RT/RW, Patokan)"
                    />
                    <InputError :message="poskoForm.errors.kkn_address" />
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <Button :disabled="poskoForm.processing" class="bg-sky-500 hover:bg-sky-600 text-white font-bold cursor-pointer">
                        <Spinner v-if="poskoForm.processing" />
                        Simpan Data Posko
                    </Button>
                </div>
            </form>
        </div>

    </div>
</template>
