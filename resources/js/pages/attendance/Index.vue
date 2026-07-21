<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import InputError from '@/components/InputError.vue';
import { store } from '@/routes/attendance';
import { ref, computed, watch, onMounted, nextTick } from 'vue';
import { MapPin, Loader2, CheckCircle2, QrCode, Download, Printer, X, Phone, Calendar, Clock, Info, User, FileText } from '@lucide/vue';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

const props = defineProps<{
    todayAttendance: any;
    recap: any[];
    members: any[];
    daysInMonth: number;
    isLeader: boolean;
    hostPosko?: {
        name: string;
        group_number: number;
    };
    supportInfo?: {
        instagram: string;
        whatsapp: string;
    };
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

// Modal Detail Presensi Sel Tabel
const selectedCellDetail = ref<{ member: any; day: number; record: any } | null>(null);

const openCellDetail = (member: any, day: number) => {
    const record = getCellData(member.id, day);
    selectedCellDetail.value = {
        member,
        day,
        record
    };
};

const closeCellDetail = () => {
    selectedCellDetail.value = null;
};

// Modal QR Code Poster (Format Portrait 4:5)
const showQrModal = ref(false);

const scanQrUrl = computed(() => {
    if (typeof window === 'undefined') return '';
    return `${window.location.origin}/absensi/scan-qr`;
});

const formattedGroupName = computed(() => {
    const groupNum = props.hostPosko?.group_number;
    if (!groupNum) return 'POSKO KKN';
    const str = String(groupNum).trim();
    if (str.toLowerCase().startsWith('kelompok')) {
        return str.toUpperCase();
    }
    return `KELOMPOK ${str}`;
});

const qrCodeApiUrl = computed(() => {
    return `https://api.qrserver.com/v1/create-qr-code/?size=350x350&margin=10&data=${encodeURIComponent(scanQrUrl.value)}`;
});

const printQrPoster = () => {
    const posterEl = document.getElementById('qr-poster-printable');
    if (!posterEl) return;
    
    const printWin = window.open('', '_blank');
    if (!printWin) return;

    printWin.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>QR Code Presensi - Kelompok ${props.hostPosko?.group_number || '01'}</title>
            <script src="https://cdn.tailwindcss.com"><\/script>
            <style>
                @page { size: A4 portrait; margin: 0; }
                body { margin: 0; padding: 20px; display: flex; align-items: center; justify-content: center; min-h: 100vh; background: #fff; }
            </style>
        </head>
        <body onload="window.print(); window.close();">
            <div style="width: 100%; max-width: 500px; aspect-ratio: 4/5;">
                ${posterEl.innerHTML}
            </div>
        </body>
        </html>
    `);
    printWin.document.close();
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

    <div class="relative flex flex-col gap-6 rounded-xl p-4 sm:p-6 min-h-[400px] w-full max-w-full">
        
        <!-- Header Page & Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Absensi Harian</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Lakukan absen setiap hari menggunakan lokasi GPS atau scan QR Code posko.</p>
            </div>

            <!-- Header Leader Action: Button QR Code Absen -->
            <div v-if="isLeader" class="flex items-center gap-3">
                <Button 
                    @click="showQrModal = true"
                    class="bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white font-bold text-sm px-4 py-2.5 rounded-xl shadow-md shadow-sky-500/20 flex items-center gap-2 cursor-pointer"
                >
                    <QrCode class="w-4 h-4" />
                    <span>QR Code Absen Posko</span>
                </Button>
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

            <!-- Box Keterangan Warna & Petunjuk Klik Detail -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-6 flex flex-col justify-between">
                <div>
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

                <!-- Tip Klik/Tap Cell -->
                <div class="mt-6 p-3 bg-sky-50 dark:bg-sky-950/40 border border-sky-100 dark:border-sky-900/50 rounded-xl text-xs text-sky-700 dark:text-sky-300 flex items-center gap-2">
                    <Info class="w-4 h-4 shrink-0" />
                    <span><strong>Petunjuk:</strong> Klik atau tekan salah satu kotak tanggal pada tabel untuk melihat rincian jam, lokasi, dan catatan presensi anggota.</span>
                </div>
            </div>
        </div>

        <!-- Tabel Rekap Laporan Bulanan (Semua Anggota) -->
        <div class="w-full min-w-0 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-6 mt-2">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-4">
                <div>
                    <h3 class="text-lg font-bold">Laporan Kehadiran Bulanan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar presensi harian seluruh tim posko bulan ini.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <select v-model="selectedMonth" @change="changeFilter" class="rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 focus:border-[#38BDF8] focus:ring-[#38BDF8] text-sm">
                        <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                    </select>
                    <select v-model="selectedYear" @change="changeFilter" class="rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 focus:border-[#38BDF8] focus:ring-[#38BDF8] text-sm">
                        <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                    </select>
                </div>
            </div>

            <!-- Mobile Scroll Hint Banner -->
            <div class="block sm:hidden mb-3 text-[11px] text-slate-500 bg-slate-100 dark:bg-slate-800/60 py-1.5 px-3 rounded-lg text-center font-medium">
                ← Geser tabel ke kanan untuk melihat seluruh tanggal →
            </div>

            <div class="overflow-x-auto w-full pb-4 scrollbar-thin">
                <table class="w-full text-sm text-left border-collapse min-w-max">
                    <thead>
                        <tr>
                            <th class="border border-slate-300 dark:border-slate-700 bg-emerald-600 text-white font-bold py-2 px-3 text-center w-10">No.</th>
                            <th class="border border-slate-300 dark:border-slate-700 bg-emerald-600 text-white font-bold py-2 px-4 sticky left-0 z-20 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.15)] bg-emerald-600">Member</th>
                            
                            <!-- Kolom Tanggal (1 sampai daysInMonth) -->
                            <th 
                                v-for="day in daysInMonth" 
                                :key="day"
                                class="border border-slate-300 dark:border-slate-700 py-2 px-1 text-center text-xs w-9"
                                :class="isWeekend(day) ? 'bg-red-600 text-white' : 'bg-emerald-600 text-white'"
                            >
                                {{ day.toString().padStart(2, '0') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(member, index) in members" :key="member.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                            <td class="border border-slate-300 dark:border-slate-700 py-2 px-3 text-center text-slate-600 dark:text-slate-400 font-medium">
                                {{ index + 1 }}
                            </td>
                            <td class="border border-slate-300 dark:border-slate-700 py-2 px-4 font-semibold text-slate-800 dark:text-slate-200 sticky left-0 z-20 bg-white dark:bg-slate-900 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.15)]">
                                <span class="truncate block max-w-[140px] sm:max-w-none">{{ member.name }}</span>
                            </td>
                            
                            <!-- Cell Data Kehadiran -->
                            <td 
                                v-for="day in daysInMonth" 
                                :key="day"
                                class="border border-slate-300 dark:border-slate-700 p-0 text-center"
                            >
                                <button 
                                    type="button"
                                    @click="openCellDetail(member, day)"
                                    class="w-9 h-9 sm:w-8 sm:h-8 flex items-center justify-center transition-transform hover:scale-110 active:scale-95 focus:outline-none cursor-pointer"
                                    :class="getCellClass(member.id, day)"
                                    :title="`${member.name} (${day} ${months.find(m => m.value === selectedMonth)?.label}): ${getCellData(member.id, day)?.status || 'Belum ada data'}`"
                                >
                                </button>
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

        <!-- Dialog Modal Rincian Presensi Sel Tabel -->
        <Dialog :open="!!selectedCellDetail" @update:open="!$event && closeCellDetail()">
            <DialogContent class="max-w-md bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-2xl text-slate-900 dark:text-white">
                <div v-if="selectedCellDetail" class="space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-sky-100 dark:bg-sky-950 text-sky-600 flex items-center justify-center font-bold text-lg">
                                {{ selectedCellDetail.member.name.charAt(0) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-base">{{ selectedCellDetail.member.name }}</h3>
                                <p class="text-xs text-slate-500 capitalize">{{ selectedCellDetail.member.role || 'Anggota Posko' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Date & Status Info -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 bg-slate-50 dark:bg-slate-850 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tanggal</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5 mt-1">
                                <Calendar class="w-3.5 h-3.5 text-sky-500" />
                                {{ selectedCellDetail.day }} {{ months.find(m => m.value === selectedMonth)?.label }} {{ selectedYear }}
                            </span>
                        </div>

                        <div class="p-3 bg-slate-50 dark:bg-slate-850 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Status Presensi</span>
                            <div class="mt-1">
                                <span 
                                    v-if="selectedCellDetail.record" 
                                    :class="[
                                        'px-2.5 py-0.5 text-[11px] font-extrabold rounded-full inline-block',
                                        selectedCellDetail.record.status === 'Hadir' ? 'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-300' :
                                        selectedCellDetail.record.status === 'Izin' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-950 dark:text-yellow-300' :
                                        selectedCellDetail.record.status === 'Sakit' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' :
                                        'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300'
                                    ]"
                                >
                                    {{ selectedCellDetail.record.status }}
                                </span>
                                <span v-else class="text-xs font-bold text-slate-400">Belum Ada Data</span>
                            </div>
                        </div>
                    </div>

                    <!-- Time & Radius Status -->
                    <template v-if="selectedCellDetail.record">
                        <div class="space-y-2 pt-1">
                            <div class="flex items-center justify-between text-xs p-2.5 bg-slate-50 dark:bg-slate-850 rounded-xl">
                                <span class="text-slate-500 font-medium flex items-center gap-1.5">
                                    <Clock class="w-3.5 h-3.5 text-slate-400" /> Jam Absen:
                                </span>
                                <span class="font-bold text-slate-800 dark:text-slate-200 font-mono">{{ selectedCellDetail.record.time }} WIB</span>
                            </div>

                            <div class="flex items-center justify-between text-xs p-2.5 bg-slate-50 dark:bg-slate-850 rounded-xl">
                                <span class="text-slate-500 font-medium flex items-center gap-1.5">
                                    <MapPin class="w-3.5 h-3.5 text-slate-400" /> Status Radius:
                                </span>
                                <span 
                                    :class="[
                                        'font-bold px-2 py-0.5 rounded text-[10px]',
                                        selectedCellDetail.record.is_outside_radius ? 'bg-black text-white' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300'
                                    ]"
                                >
                                    {{ selectedCellDetail.record.is_outside_radius ? 'Luar Radius Posko' : 'Dalam Radius Posko' }}
                                </span>
                            </div>
                        </div>

                        <!-- Location Address -->
                        <div v-if="selectedCellDetail.record.village || selectedCellDetail.record.district || selectedCellDetail.record.latitude" class="p-3 bg-slate-50 dark:bg-slate-850 rounded-xl border border-slate-100 dark:border-slate-800 text-xs">
                            <span class="font-bold text-slate-700 dark:text-slate-300 block mb-1">Detail Lokasi GPS:</span>
                            <p class="text-slate-600 dark:text-slate-400 text-[11px] leading-relaxed">
                                <template v-if="selectedCellDetail.record.village">
                                    Desa/Kel. {{ selectedCellDetail.record.village }}, Kec. {{ selectedCellDetail.record.district }}, {{ selectedCellDetail.record.regency }}, Prov. {{ selectedCellDetail.record.province }}
                                </template>
                                <template v-else-if="selectedCellDetail.record.latitude">
                                    Koordinat: {{ selectedCellDetail.record.latitude }}, {{ selectedCellDetail.record.longitude }}
                                </template>
                            </p>
                        </div>

                        <!-- Notes / Catatan -->
                        <div v-if="selectedCellDetail.record.notes" class="p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 rounded-xl text-xs">
                            <span class="font-bold text-amber-800 dark:text-amber-300 flex items-center gap-1.5 mb-1">
                                <FileText class="w-3.5 h-3.5" /> Catatan / Keterangan:
                            </span>
                            <p class="text-amber-700 dark:text-amber-400 font-medium">{{ selectedCellDetail.record.notes }}</p>
                        </div>
                    </template>

                    <div class="pt-2">
                        <Button @click="closeCellDetail" class="w-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl h-10">
                            Tutup
                        </Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>

        <!-- Dialog Modal QR Code Poster (Format Portrait 4:5) -->
        <Dialog :open="showQrModal" @update:open="showQrModal = $event">
            <DialogContent class="max-w-lg bg-slate-900/90 border border-slate-800 rounded-3xl p-4 sm:p-6 shadow-2xl text-slate-900">
                <div class="flex items-center justify-between text-white mb-3">
                    <h3 class="font-bold text-sm sm:text-base flex items-center gap-2">
                        <QrCode class="w-5 h-5 text-sky-400" />
                        Poster QR Code Presensi Posko
                    </h3>
                    <div class="flex items-center gap-2">
                        <Button @click="printQrPoster" variant="outline" size="sm" class="h-8 text-xs font-bold bg-white/10 text-white hover:bg-white/20 border-white/20">
                            <Printer class="w-3.5 h-3.5 mr-1.5" /> Cetak / Unduh
                        </Button>
                    </div>
                </div>

                <!-- Printable 4:5 Poster Container -->
                <div class="flex items-center justify-center overflow-y-auto max-h-[75vh] p-1">
                    <div 
                        id="qr-poster-printable"
                        class="bg-white text-slate-900 w-full max-w-[420px] aspect-[4/5] p-6 sm:p-7 flex flex-col justify-between rounded-2xl shadow-2xl border border-slate-200 relative overflow-hidden font-sans select-none"
                    >
                        <!-- Top Bar: Logo SuperPosko -->
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <img src="/logo_superposko.png" alt="SuperPosko Logo" class="h-8 sm:h-9 object-contain" />
                            <span class="text-[9px] font-extrabold uppercase tracking-widest text-sky-600 bg-sky-50 px-2.5 py-1 rounded-full border border-sky-100">
                                Official Absensi
                            </span>
                        </div>

                        <!-- Center Poster Body -->
                        <div class="text-center my-auto py-2">
                            <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight leading-snug">
                                Scan QR di bawah untuk absen
                            </h2>
                            <p class="text-xs sm:text-sm font-extrabold text-sky-600 mt-1 uppercase tracking-wider">
                                {{ formattedGroupName }}
                            </p>

                            <!-- High Contrast QR Code -->
                            <div class="my-4 sm:my-5 flex items-center justify-center">
                                <div class="p-3 bg-white border-2 border-slate-900 rounded-2xl shadow-md inline-block">
                                    <img 
                                        :src="qrCodeApiUrl" 
                                        alt="QR Code Absensi Posko" 
                                        class="w-44 h-44 sm:w-52 sm:h-52 object-contain"
                                    />
                                </div>
                            </div>

                            <p class="text-[10px] sm:text-[11px] text-slate-500 max-w-[260px] mx-auto leading-relaxed">
                                Arahkan kamera smartphone Anda ke QR Code ini untuk otomatis merekam presensi harian posko.
                            </p>
                        </div>

                        <!-- Footer Support Contacts (Instagram & WhatsApp) -->
                        <div class="pt-3 border-t border-slate-200 text-left">
                            <p class="text-[10px] font-semibold text-slate-500 mb-1.5">
                                Kendala atau masalah saat presensi? Hubungi tim support SuperPosko:
                            </p>
                            <div class="flex items-center justify-between gap-2 text-[11px] font-bold text-slate-800">
                                <div class="flex items-center gap-1.5 bg-slate-100 px-2.5 py-1.5 rounded-lg border border-slate-200/60">
                                    <svg class="w-3.5 h-3.5 text-pink-600 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                        <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
                                    </svg>
                                    <span>{{ supportInfo?.instagram || '@kuukok.id' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 bg-slate-100 px-2.5 py-1.5 rounded-lg border border-slate-200/60">
                                    <Phone class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                    <span>{{ supportInfo?.whatsapp || '+62 851-7173-9232' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>

    </div>
</template>
