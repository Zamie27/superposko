<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { store } from '@/routes/attendance';
import { ref, computed, watch, onMounted, nextTick } from 'vue';
import { MapPin, Loader2, CheckCircle2 } from '@lucide/vue';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

const props = defineProps<{
    todayAttendance: any;
    recap: any[];
    members: any[];
    daysInMonth: number;
    isLeader: boolean;
    settings: {
        lat: number | null;
        lng: number | null;
        radius: number | null;
    };
    filters: {
        month: number;
        year: number;
    }
}>();

const form = useForm({
    latitude: '' as string | number,
    longitude: '' as string | number,
    status: 'Hadir',
    notes: '',
});

const isLocating = ref(false);
const locationError = ref('');

const submitAttendance = () => {
    isLocating.value = true;
    locationError.value = '';

    if (!navigator.geolocation) {
        locationError.value = 'Browser Anda tidak mendukung fitur lokasi (GPS).';
        isLocating.value = false;
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            form.latitude = position.coords.latitude;
            form.longitude = position.coords.longitude;
            
            form.post(store(), {
                preserveScroll: true,
                onSuccess: () => {
                    form.reset();
                    isLocating.value = false;
                },
                onError: () => {
                    isLocating.value = false;
                }
            });
        },
        (error) => {
            isLocating.value = false;
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    locationError.value = 'Anda menolak permintaan akses lokasi. Mohon izinkan akses lokasi di browser Anda.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    locationError.value = 'Informasi lokasi tidak tersedia.';
                    break;
                case error.TIMEOUT:
                    locationError.value = 'Waktu permintaan lokasi habis (timeout).';
                    break;
                default:
                    locationError.value = 'Terjadi kesalahan tidak dikenal saat mengambil lokasi.';
                    break;
            }
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
};

// Map Settings Form (Khusus Ketua/Wakil/Sekretaris)
const mapContainer = ref<HTMLElement | null>(null);
const map = ref<L.Map | null>(null);
const marker = ref<L.Marker | null>(null);
const circle = ref<L.Circle | null>(null);

const settingsForm = useForm({
    lat: props.settings?.lat ?? -6.200000,
    lng: props.settings?.lng ?? 106.816666,
    radius: props.settings?.radius ?? 100,
});

const saveSettings = () => {
    settingsForm.post('/absensi/settings', {
        preserveScroll: true,
        onSuccess: () => {
            // success
        }
    });
};

watch(() => props.isLeader, (newVal) => {
    if (newVal) {
        nextTick(() => {
            initMap();
        });
    }
}, { immediate: true });

const initMap = () => {
    if (!mapContainer.value) return;
    if (map.value) return;

    map.value = L.map(mapContainer.value).setView([Number(settingsForm.lat), Number(settingsForm.lng)], 15);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map.value);

    // Fix default icon issue with webpack/vite
    delete (L.Icon.Default.prototype as any)._getIconUrl;
    L.Icon.Default.mergeOptions({
        iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
        iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
    });

    marker.value = L.marker([Number(settingsForm.lat), Number(settingsForm.lng)], { draggable: true }).addTo(map.value);
    
    circle.value = L.circle([Number(settingsForm.lat), Number(settingsForm.lng)], {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.2,
        radius: Number(settingsForm.radius)
    }).addTo(map.value);

    marker.value.on('dragend', (e) => {
        const latlng = e.target.getLatLng();
        settingsForm.lat = latlng.lat;
        settingsForm.lng = latlng.lng;
        circle.value?.setLatLng(latlng);
    });

    map.value.on('click', (e: L.LeafletMouseEvent) => {
        const latlng = e.latlng;
        settingsForm.lat = latlng.lat;
        settingsForm.lng = latlng.lng;
        marker.value?.setLatLng(latlng);
        circle.value?.setLatLng(latlng);
    });
};

watch(() => settingsForm.radius, (newRadius) => {
    if (circle.value) {
        circle.value.setRadius(Number(newRadius));
    }
});

// Filter Bulan & Tahun Laporan
const selectedMonth = ref(props.filters?.month ?? new Date().getMonth() + 1);
const selectedYear = ref(props.filters?.year ?? new Date().getFullYear());

const months = [
    { value: 1, label: 'Januari' },
    { value: 2, label: 'Februari' },
    { value: 3, label: 'Maret' },
    { value: 4, label: 'April' },
    { value: 5, label: 'Mei' },
    { value: 6, label: 'Juni' },
    { value: 7, label: 'Juli' },
    { value: 8, label: 'Agustus' },
    { value: 9, label: 'September' },
    { value: 10, label: 'Oktober' },
    { value: 11, label: 'November' },
    { value: 12, label: 'Desember' }
];

const years = computed(() => {
    const current = new Date().getFullYear();
    return [current - 1, current, current + 1];
});

const changeFilter = () => {
    router.get('/absensi', {
        month: selectedMonth.value,
        year: selectedYear.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

// Update refs jika props berubah dari server (misal navigasi balik)
watch(() => props.filters, (newFilters) => {
    if (newFilters) {
        selectedMonth.value = newFilters.month;
        selectedYear.value = newFilters.year;
    }
}, { deep: true });

// Tabel Grid Helper
const isWeekend = (day: number) => {
    const date = new Date(selectedYear.value, selectedMonth.value - 1, day);
    const dayOfWeek = date.getDay();
    return dayOfWeek === 0 || dayOfWeek === 6; // Sunday or Saturday
};

const getCellData = (userId: number, day: number) => {
    const record = props.recap.find(r => {
        if (r.user_id !== userId) return false;
        const date = new Date(r.date);
        return date.getDate() === day;
    });
    return record;
};

const getCellClass = (userId: number, day: number) => {
    const record = getCellData(userId, day);
    if (!record) {
        return isWeekend(day) ? 'bg-slate-200 dark:bg-slate-800' : 'bg-transparent';
    }
    
    if (record.is_outside_radius) {
        return 'bg-black text-white';
    }
    
    switch (record.status) {
        case 'Hadir': return 'bg-green-500';
        case 'Izin': return 'bg-yellow-400';
        case 'Sakit': return 'bg-blue-500';
        case 'Alfa': return 'bg-red-500';
        default: return 'bg-green-500';
    }
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

    <div class="relative flex flex-col gap-6 rounded-xl p-6 min-h-[400px] w-full max-w-full">
        
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Absensi Harian</h1>
                <p class="text-sm text-slate-500 mt-1">Lakukan absen setiap hari menggunakan lokasi GPS perangkat Anda.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Absen Box -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6">
                <h3 class="text-lg font-bold mb-4">Absen Hari Ini</h3>

                <div v-if="todayAttendance" class="bg-green-50 dark:bg-green-950/30 border border-green-200 p-6 rounded-xl text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900 text-green-600 rounded-full mb-3">
                        <CheckCircle2 class="w-6 h-6" />
                    </div>
                    <h4 class="font-bold text-green-800 dark:text-green-300">Anda sudah absen!</h4>
                    <p class="text-sm text-green-600 mt-1">Tercatat pada {{ todayAttendance.time }}</p>
                    
                    <div v-if="todayAttendance.village || todayAttendance.district" class="mt-4 p-3 bg-white/60 dark:bg-black/20 rounded-lg text-sm text-green-700 dark:text-green-400 text-left border border-green-100 dark:border-green-900/50">
                        <p class="flex items-start gap-2">
                            <MapPin class="w-4 h-4 shrink-0 mt-0.5" />
                            <span>
                                <strong>Lokasi Tercatat:</strong><br>
                                <span v-if="todayAttendance.village">Desa/Kel. {{ todayAttendance.village }}, </span>
                                <span v-if="todayAttendance.district">Kec. {{ todayAttendance.district }}, </span>
                                <span v-if="todayAttendance.regency">{{ todayAttendance.regency }}, </span>
                                <span v-if="todayAttendance.province">Prov. {{ todayAttendance.province }}</span>
                            </span>
                        </p>
                    </div>
                </div>

                <form v-else @submit.prevent="submitAttendance" class="space-y-4">
                    <div v-if="locationError" class="p-3 text-sm text-red-600 bg-red-50 rounded-lg border border-red-100">
                        {{ locationError }}
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Status Kehadiran</label>
                        <select v-model="form.status" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 focus:border-[#38BDF8] focus:ring-[#38BDF8]">
                            <option value="Hadir">Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Alfa">Alfa (Tanpa Keterangan)</option>
                        </select>
                    </div>
                    
                    <div v-if="form.status !== 'Hadir'">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Keterangan / Catatan</label>
                        <textarea v-model="form.notes" rows="2" class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 focus:border-[#38BDF8] focus:ring-[#38BDF8]" placeholder="Alasan izin/sakit..."></textarea>
                    </div>

                    <div class="pt-4">
                        <Button 
                            type="submit" 
                            class="w-full h-14 bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white font-bold text-lg shadow-lg shadow-sky-500/20" 
                            :disabled="isLocating || form.processing"
                        >
                            <template v-if="isLocating || form.processing">
                                <Loader2 class="w-5 h-5 mr-2 animate-spin" />
                                Memproses Lokasi...
                            </template>
                            <template v-else>
                                <MapPin class="w-5 h-5 mr-2" />
                                Rekam Absen Sekarang
                            </template>
                        </Button>
                        <p class="text-xs text-center text-slate-500 mt-3">Pastikan Anda telah mengizinkan akses lokasi pada browser.</p>
                    </div>
                </form>
            </div>

            <!-- Box Keterangan Warna -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-6">
                <h3 class="text-lg font-bold mb-4">Keterangan Laporan</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded bg-green-500 shadow-sm"></div>
                        <span class="text-sm font-medium">Hadir</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded bg-yellow-400 shadow-sm"></div>
                        <span class="text-sm font-medium">Izin</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded bg-blue-500 shadow-sm"></div>
                        <span class="text-sm font-medium">Sakit</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded bg-red-500 shadow-sm"></div>
                        <span class="text-sm font-medium">Alfa</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded bg-slate-200 dark:bg-slate-800 shadow-sm border border-slate-300 dark:border-slate-700"></div>
                        <span class="text-sm font-medium">Hari Libur / Akhir Pekan</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded bg-black shadow-sm"></div>
                        <span class="text-sm font-medium">Luar Radius (Hitam)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Rekap Laporan Bulanan (Semua Anggota) -->
        <div class="w-full min-w-0 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-6 mt-2">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <h3 class="text-lg font-bold">Laporan Kehadiran Bulanan</h3>
                <div class="flex flex-wrap items-center gap-3">
                    <select v-model="selectedMonth" @change="changeFilter" class="rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 focus:border-[#38BDF8] focus:ring-[#38BDF8] text-sm">
                        <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                    </select>
                    <select v-model="selectedYear" @change="changeFilter" class="rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 focus:border-[#38BDF8] focus:ring-[#38BDF8] text-sm">
                        <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                    </select>
                </div>
            </div>
            
            <div class="overflow-x-auto w-full pb-4">
                <table class="w-full text-sm text-left border-collapse min-w-max">
                    <thead>
                        <tr>
                            <th class="border border-slate-300 dark:border-slate-700 bg-emerald-500 text-white font-bold py-2 px-3 text-center w-10">No.</th>
                            <th class="border border-slate-300 dark:border-slate-700 bg-emerald-500 text-white font-bold py-2 px-4 sticky left-0 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">Member</th>
                            
                            <!-- Kolom Tanggal (1 sampai daysInMonth) -->
                            <th 
                                v-for="day in daysInMonth" 
                                :key="day"
                                class="border border-slate-300 dark:border-slate-700 py-2 px-2 text-center text-xs w-8"
                                :class="isWeekend(day) ? 'bg-red-600 text-white' : 'bg-emerald-500 text-white'"
                            >
                                {{ day.toString().padStart(2, '0') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(member, index) in members" :key="member.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="border border-slate-300 dark:border-slate-700 py-1.5 px-3 text-center text-slate-600 dark:text-slate-400">
                                {{ index + 1 }}
                            </td>
                            <td class="border border-slate-300 dark:border-slate-700 py-1.5 px-4 font-medium text-slate-800 dark:text-slate-200 sticky left-0 z-10 bg-white dark:bg-slate-900 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                {{ member.name }}
                            </td>
                            
                            <!-- Cell Data Kehadiran -->
                            <td 
                                v-for="day in daysInMonth" 
                                :key="day"
                                class="border border-slate-300 dark:border-slate-700 p-0"
                            >
                                <div 
                                    class="w-full h-8 flex items-center justify-center transition-colors"
                                    :class="getCellClass(member.id, day)"
                                    :title="getCellData(member.id, day)?.status || 'Belum ada data'"
                                >
                                </div>
                            </td>
                        </tr>
                        <tr v-if="members.length === 0">
                            <td :colspan="daysInMonth + 2" class="border border-slate-300 dark:border-slate-700 py-4 px-4 text-center text-slate-500">
                                Tidak ada data member.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Settings Map Khusus Leader -->
        <div v-if="isLeader" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-6 mt-6">
            <h3 class="text-lg font-bold mb-4">Pengaturan Batas Lokasi Kehadiran (Posko)</h3>
            <p class="text-sm text-slate-500 mb-6">Atur koordinat pusat posko dan batas radius (dalam meter) toleransi absen. Anda bisa menggeser pin merah pada peta atau mengisi form di bawah.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Form Settings -->
                <form @submit.prevent="saveSettings" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Latitude</label>
                        <input v-model="settingsForm.lat" type="number" step="any" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 focus:border-[#38BDF8] focus:ring-[#38BDF8]">
                        <InputError :message="settingsForm.errors.lat" class="mt-1" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Longitude</label>
                        <input v-model="settingsForm.lng" type="number" step="any" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 focus:border-[#38BDF8] focus:ring-[#38BDF8]">
                        <InputError :message="settingsForm.errors.lng" class="mt-1" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Radius (meter)</label>
                        <input v-model="settingsForm.radius" type="number" min="10" max="5000" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 focus:border-[#38BDF8] focus:ring-[#38BDF8]">
                        <InputError :message="settingsForm.errors.radius" class="mt-1" />
                    </div>
                    <Button 
                        type="submit" 
                        class="w-full h-10 mt-2 bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white font-bold" 
                        :disabled="settingsForm.processing"
                    >
                        <template v-if="settingsForm.processing">
                            <Loader2 class="w-4 h-4 mr-2 animate-spin" />
                            Menyimpan...
                        </template>
                        <template v-else>
                            Simpan Pengaturan
                        </template>
                    </Button>
                </form>

                <!-- Map View -->
                <div class="md:col-span-2">
                    <div ref="mapContainer" class="w-full h-[300px] rounded-lg border border-slate-300 dark:border-slate-700 z-10 relative isolate"></div>
                </div>
            </div>
        </div>

    </div>
</template>
