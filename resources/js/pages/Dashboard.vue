<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { 
    Wallet, BookOpen, Box, ClipboardList, Users, Vote, 
    ChevronLeft, ChevronRight, Calendar, Clock, CheckCircle, Info, MapPin, CheckCircle2
} from '@lucide/vue';
import { ref, computed } from 'vue';

interface RosterMember {
    id: number;
    name: string;
    email: string;
}

interface DutyRoster {
    id: number;
    day_of_week: string;
    task_name: string;
    user?: RosterMember | null;
}

interface CalendarEvent {
    id: number;
    title: string;
    description: string | null;
    start_time: string;
    end_time: string | null;
    location: string | null;
}

interface DashboardMetrics {
    finance: {
        balance: number;
        total_income: number;
        total_expense: number;
    };
    proker: {
        count: number;
        total_pagu: number;
        total_spent: number;
    };
    inventory: {
        count: number;
        good_count: number;
    };
    logistics: {
        count: number;
        critical_count: number;
    };
    members: {
        count: number;
    };
    voting: {
        active_polls: number;
        unresponded_aspirations: number;
    };
}

import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    metrics: DashboardMetrics;
    todayRoster: DutyRoster[];
    events: CalendarEvent[];
    todayAttendance?: {
        id: number;
        status: string;
        time: string;
        date: string;
        village?: string;
        district?: string;
        regency?: string;
        province?: string;
    } | null;
    isDplGateway?: boolean;
    allPoskos?: Array<{ id: number; name: string; university: string; group_number: number }>;
    pendingRequests?: Array<{ id: number; host: { id: number; name: string; university: string; group_number: number }; status: string }>;
}>();

const requestForm = useForm({
    host_id: '',
});

const submitRequest = () => {
    requestForm.post('/dpl/request-monitoring', {
        onSuccess: () => {
            requestForm.reset();
        }
    });
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: '/dashboard',
            },
        ],
    },
});

// Calendar State
const currentDate = ref(new Date());
const monthNames = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

const currentMonthName = computed(() => monthNames[currentDate.value.getMonth()]);
const currentYear = computed(() => currentDate.value.getFullYear());

// Calendar Navigation
const prevMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1);
};

const nextMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1);
};

// Check if two dates represent the same day
const isSameDay = (d1: Date, d2: Date) => {
    return d1.getFullYear() === d2.getFullYear() &&
           d1.getMonth() === d2.getMonth() &&
           d1.getDate() === d2.getDate();
};

// Generate calendar days grid (Sunday to Saturday)
const calendarDays = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();
    
    // First day of current month
    const firstDay = new Date(year, month, 1);
    const startDayOfWeek = firstDay.getDay();
    
    // Days in current month
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    // Days in previous month
    const daysInPrevMonth = new Date(year, month, 0).getDate();
    
    const days: Array<{
        date: Date;
        dayNum: number;
        isCurrentMonth: boolean;
        isToday: boolean;
    }> = [];
    
    // Previous month padding days
    for (let i = startDayOfWeek - 1; i >= 0; i--) {
        const prevDate = new Date(year, month - 1, daysInPrevMonth - i);
        days.push({
            date: prevDate,
            dayNum: daysInPrevMonth - i,
            isCurrentMonth: false,
            isToday: isSameDay(prevDate, new Date()),
        });
    }
    
    // Current month days
    for (let i = 1; i <= daysInMonth; i++) {
        const date = new Date(year, month, i);
        days.push({
            date: date,
            dayNum: i,
            isCurrentMonth: true,
            isToday: isSameDay(date, new Date()),
        });
    }
    
    // Next month padding days to fill 42 cells (6 rows)
    const remainingCells = 42 - days.length;

    for (let i = 1; i <= remainingCells; i++) {
        const nextDate = new Date(year, month + 1, i);
        days.push({
            date: nextDate,
            dayNum: i,
            isCurrentMonth: false,
            isToday: isSameDay(nextDate, new Date()),
        });
    }
    
    return days;
});

// Group the 42 cells into 6 weeks with their event spans
const calendarWeeks = computed(() => {
    const days = calendarDays.value;
    const result = [];
    
    for (let i = 0; i < days.length; i += 7) {
        const weekDays = days.slice(i, i + 7);
        
        // Find start and end datetime limits for this week row
        const weekStart = weekDays[0].date;
        const weekEnd = new Date(weekDays[6].date.getFullYear(), weekDays[6].date.getMonth(), weekDays[6].date.getDate(), 23, 59, 59);
        
        // Filter events that occur/overlap in this week
        const weekEvents = props.events.filter(e => {
            const eStart = new Date(e.start_time);
            const eEnd = e.end_time ? new Date(e.end_time) : eStart;

            return eStart <= weekEnd && eEnd >= weekStart;
        });

        // Sort events: multi-day first, then by start time
        weekEvents.sort((a, b) => {
            const aStart = new Date(a.start_time);
            const bStart = new Date(b.start_time);
            const aEnd = a.end_time ? new Date(a.end_time) : aStart;
            const bEnd = b.end_time ? new Date(b.end_time) : bStart;
            const aDur = aEnd.getTime() - aStart.getTime();
            const bDur = bEnd.getTime() - bStart.getTime();

            if (aDur !== bDur) {
return bDur - aDur;
}

            return aStart.getTime() - bStart.getTime();
        });

        // Layout events on levels/tracks to prevent overlaps
        const levels: Array<Array<CalendarEvent | null>> = [];
        const eventLayouts: Array<{
            event: CalendarEvent;
            startIdx: number;
            span: number;
            level: number;
        }> = [];

        weekEvents.forEach(event => {
            const eStart = new Date(event.start_time);
            const eEnd = event.end_time ? new Date(event.end_time) : eStart;
            
            // Find start column index in this week (0-6)
            let startIdx = 0;

            if (eStart >= weekStart) {
                startIdx = Math.floor((eStart.getTime() - weekStart.getTime()) / (24 * 60 * 60 * 1000));
                startIdx = Math.max(0, Math.min(6, startIdx));
            }

            // Find end column index in this week (0-6)
            let endIdx = 6;

            if (eEnd <= weekEnd) {
                endIdx = Math.floor((eEnd.getTime() - weekStart.getTime()) / (24 * 60 * 60 * 1000));
                endIdx = Math.max(0, Math.min(6, endIdx));
            }

            // Find first free track/level for these columns
            let levelIdx = 0;

            while (true) {
                if (!levels[levelIdx]) {
                    levels[levelIdx] = Array(7).fill(null);
                }

                let isFree = true;

                for (let d = startIdx; d <= endIdx; d++) {
                    if (levels[levelIdx][d] !== null) {
                        isFree = false;
                        break;
                    }
                }

                if (isFree) {
                    for (let d = startIdx; d <= endIdx; d++) {
                        levels[levelIdx][d] = event;
                    }

                    break;
                }

                levelIdx++;
            }

            eventLayouts.push({
                event,
                startIdx,
                span: endIdx - startIdx + 1,
                level: levelIdx
            });
        });

        result.push({
            days: weekDays,
            eventLayouts,
            maxLevel: levels.length
        });
    }

    return result;
});

const formatEventTime = (timeStr: string) => {
    const d = new Date(timeStr);
    let hours = d.getHours();
    const minutes = d.getMinutes();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    const minutesStr = minutes < 10 ? '0' + minutes : minutes;

    return `${hours}:${minutesStr} ${ampm}`;
};

const getEventColorClass = (eventId: number) => {
    const colors = [
        'bg-sky-500/90 text-white border-sky-400 dark:bg-sky-500/20 dark:text-sky-400 dark:border-sky-500/30',
        'bg-emerald-500/90 text-white border-emerald-400 dark:bg-emerald-500/20 dark:text-emerald-400 dark:border-emerald-500/30',
        'bg-purple-500/90 text-white border-purple-400 dark:bg-purple-500/20 dark:text-purple-400 dark:border-purple-500/30',
        'bg-amber-500/90 text-white border-amber-400 dark:bg-amber-500/20 dark:text-amber-400 dark:border-amber-500/30',
        'bg-rose-500/90 text-white border-rose-400 dark:bg-rose-500/20 dark:text-rose-400 dark:border-rose-500/30',
        'bg-indigo-500/90 text-white border-indigo-400 dark:bg-indigo-500/20 dark:text-indigo-400 dark:border-indigo-500/30'
    ];

    return colors[eventId % colors.length];
};

const getTodayName = () => {
    return new Date().toLocaleDateString('id-ID', { weekday: 'long' });
};
</script>

<template>
    <Head title="Dashboard Utama - SuperPosko" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-7xl mx-auto font-sans">
        
        <!-- DPL Gateway Welcome Screen -->
        <template v-if="isDplGateway">
            <div class="max-w-2xl mx-auto w-full space-y-6 pt-6">
                <!-- Welcome Banner -->
                <div class="bg-gradient-to-r from-sky-500 to-indigo-600 text-white rounded-3xl p-8 shadow-md">
                    <h2 class="text-2xl font-black">Selamat Datang di Portal DPL</h2>
                    <p class="text-sm opacity-90 mt-2 leading-relaxed">
                        Sebagai Dosen Pembimbing Lapangan (DPL), Anda dapat memantau administrasi, keuangan, logbook, dan program kerja kelompok KKN binaan Anda secara real-time.
                    </p>
                    <div class="mt-4 inline-flex items-center gap-2 px-3 py-1 bg-white/20 rounded-full text-xs font-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 111.084-1.085l-.041.02H11.25zm0 1.5H12v3.75h-.75V12.75zm9.75-3.562A9.75 9.75 0 111.5 12a9.75 9.75 0 0119.5 0z" />
                        </svg>
                        Pilih kelompok di header atas untuk mulai memantau kelompok yang sudah terhubung.
                    </div>
                </div>

                <!-- Request Access Form Card -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Minta Akses Pemantauan Kelompok</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                        Cari kelompok posko KKN binaan Anda dan kirim permintaan pemantauan. Setelah Ketua, Wakil, atau Sekretaris posko menyetujui, Anda dapat memantau data mereka secara penuh.
                    </p>

                    <form @submit.prevent="submitRequest" class="space-y-4">
                        <div v-if="requestForm.errors.host_id" class="text-red-500 text-xs font-semibold">
                            {{ requestForm.errors.host_id }}
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <select 
                                v-model="requestForm.host_id"
                                class="flex-1 h-11 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 px-4 text-sm text-slate-855 dark:text-slate-100 focus:outline-none focus:border-sky-500"
                                required
                            >
                                <option value="" disabled>-- Pilih Kelompok KKN --</option>
                                <option 
                                    v-for="posko in allPoskos" 
                                    :key="posko.id" 
                                    :value="posko.id"
                                >
                                    {{ posko.name }} - Kelompok {{ posko.group_number }} ({{ posko.university }})
                                </option>
                            </select>
                            
                            <button 
                                type="submit" 
                                :disabled="requestForm.processing"
                                class="h-11 px-6 bg-sky-500 hover:bg-sky-600 disabled:bg-sky-400 text-white font-bold rounded-xl text-sm transition shrink-0 cursor-pointer"
                            >
                                Kirim Permintaan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Pending Requests Card -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-4">Menunggu Konfirmasi Kelompok ({{ pendingRequests?.length || 0 }})</h3>
                    
                    <div v-if="pendingRequests && pendingRequests.length > 0" class="space-y-3">
                        <div 
                            v-for="req in pendingRequests" 
                            :key="req.id" 
                            class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-150 dark:border-slate-855 rounded-2xl flex items-center justify-between gap-4"
                        >
                            <div>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ req.host.name }}</h4>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Kelompok {{ req.host.group_number }} - {{ req.host.university }}</p>
                            </div>
                            <span class="px-3 py-1 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 text-[10px] font-bold rounded-full border border-amber-200/50">
                                Pending
                            </span>
                        </div>
                    </div>

                    <div v-else class="text-center py-8 border border-dashed border-slate-200 dark:border-slate-800 rounded-2xl">
                        <p class="text-xs text-slate-400">Tidak ada permintaan pemantauan yang aktif.</p>
                    </div>
                </div>
            </div>
        </template>

        <template v-else>
            <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Pusat Kendali KKN</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Ringkasan administrasi, program kerja, keuangan, dan agenda posko dalam satu halaman.</p>
            </div>
        </div>

        <!-- Attendance Quick Action & Status Widget Card -->
        <div 
            :class="[
                'p-4 sm:p-5 rounded-2xl border transition-all duration-300 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4',
                todayAttendance 
                    ? 'bg-emerald-500/10 dark:bg-emerald-950/30 border-emerald-300/50 dark:border-emerald-800/50' 
                    : 'bg-gradient-to-r from-sky-500/10 via-sky-500/5 to-indigo-500/10 dark:from-sky-950/40 dark:to-indigo-950/30 border-sky-300/60 dark:border-sky-800/60 shadow-xs'
            ]"
        >
            <div class="flex items-center gap-3.5 min-w-0">
                <div 
                    :class="[
                        'w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 font-bold',
                        todayAttendance 
                            ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' 
                            : 'bg-sky-500 text-white shadow-md shadow-sky-500/20 animate-pulse'
                    ]"
                >
                    <CheckCircle2 v-if="todayAttendance" class="w-6 h-6" />
                    <MapPin v-else class="w-6 h-6" />
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                            {{ todayAttendance ? 'Sudah Absen Hari Ini' : 'Belum Absen Hari Ini' }}
                        </h3>
                        <span 
                            :class="[
                                'px-2.5 py-0.5 text-[10px] font-extrabold rounded-full uppercase tracking-wider',
                                todayAttendance 
                                    ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' 
                                    : 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300'
                            ]"
                        >
                            {{ todayAttendance ? todayAttendance.status : 'Perlu Aksi' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5 truncate">
                        <template v-if="todayAttendance">
                            Tercatat pukul <strong>{{ todayAttendance.time }}</strong>
                            <span v-if="todayAttendance.village"> • {{ todayAttendance.village }}</span>
                        </template>
                        <template v-else>
                            Lakukan pengisian absensi presensi harian lokasi GPS anggota posko hari ini.
                        </template>
                    </p>
                </div>
            </div>

            <Link 
                href="/absensi"
                :class="[
                    'w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold text-xs transition duration-200 shrink-0 text-center flex items-center justify-center gap-2 shadow-xs cursor-pointer',
                    todayAttendance
                        ? 'bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700'
                        : 'bg-sky-500 hover:bg-sky-600 text-white shadow-sky-500/25'
                ]"
            >
                <MapPin class="w-4 h-4" />
                <span>{{ todayAttendance ? 'Lihat Details Absensi' : 'Rekam Absen Sekarang' }}</span>
            </Link>
        </div>

        <!-- 6 Metrics Cards Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- Kas Card -->
            <Link 
                href="/finance"
                class="group flex flex-col justify-between p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:border-sky-500 dark:hover:border-sky-500/80 transition-all duration-300 cursor-pointer"
            >
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Saldo Kas</span>
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl text-emerald-600 shrink-0">
                        <Wallet class="size-4" />
                    </div>
                </div>
                <div class="mt-4">
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate">
                        Rp {{ Number(metrics.finance.balance).toLocaleString('id-ID') }}
                    </h4>
                    <span class="text-[9px] font-semibold text-slate-400">Keuangan Posko</span>
                </div>
            </Link>

            <!-- Proker Card -->
            <Link 
                href="/logbook"
                class="group flex flex-col justify-between p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:border-sky-500 dark:hover:border-sky-500/80 transition-all duration-300 cursor-pointer"
            >
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Anggaran Proker</span>
                    <div class="p-2 bg-purple-50 dark:bg-purple-500/10 rounded-xl text-purple-600 shrink-0">
                        <BookOpen class="size-4" />
                    </div>
                </div>
                <div class="mt-4">
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate">
                        Rp {{ Number(metrics.proker.total_spent).toLocaleString('id-ID') }}
                    </h4>
                    <span class="text-[9px] font-semibold text-slate-400">{{ metrics.proker.count }} Proker Terdaftar</span>
                </div>
            </Link>

            <!-- Inventaris Card -->
            <Link 
                href="/management/inventory"
                class="group flex flex-col justify-between p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:border-sky-500 dark:hover:border-sky-500/80 transition-all duration-300 cursor-pointer"
            >
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Inventaris</span>
                    <div class="p-2 bg-blue-50 dark:bg-blue-500/10 rounded-xl text-blue-600 shrink-0">
                        <Box class="size-4" />
                    </div>
                </div>
                <div class="mt-4">
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">
                        {{ metrics.inventory.count }} Barang
                    </h4>
                    <span class="text-[9px] font-semibold text-slate-400">{{ metrics.inventory.good_count }} Kondisi Bagus</span>
                </div>
            </Link>

            <!-- Logistik Card -->
            <Link 
                href="/management/logistic"
                class="group flex flex-col justify-between p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:border-sky-500 dark:hover:border-sky-500/80 transition-all duration-300 cursor-pointer"
            >
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Logistik</span>
                    <div class="p-2 bg-amber-50 dark:bg-amber-500/10 rounded-xl text-amber-600 shrink-0">
                        <ClipboardList class="size-4" />
                    </div>
                </div>
                <div class="mt-4">
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                        {{ metrics.logistics.count }} Bahan
                        <span v-if="metrics.logistics.critical_count > 0" class="flex w-2 h-2 bg-red-500 rounded-full animate-ping"></span>
                    </h4>
                    <span :class="['text-[9px] font-bold', metrics.logistics.critical_count > 0 ? 'text-red-500' : 'text-slate-400']">
                        {{ metrics.logistics.critical_count > 0 ? metrics.logistics.critical_count + ' Menipis/Habis' : 'Stok Aman' }}
                    </span>
                </div>
            </Link>

            <!-- Anggota Card -->
            <Link 
                href="/management/members"
                class="group flex flex-col justify-between p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:border-sky-500 dark:hover:border-sky-500/80 transition-all duration-300 cursor-pointer"
            >
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Anggota</span>
                    <div class="p-2 bg-sky-50 dark:bg-sky-500/10 rounded-xl text-sky-600 shrink-0">
                        <Users class="size-4" />
                    </div>
                </div>
                <div class="mt-4">
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">
                        {{ metrics.members.count }} Orang
                    </h4>
                    <span class="text-[9px] font-semibold text-slate-400">Total Tim Posko</span>
                </div>
            </Link>

            <!-- Voting & Aspirasi Card -->
            <Link 
                href="/voting"
                class="group flex flex-col justify-between p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:border-sky-500 dark:hover:border-sky-500/80 transition-all duration-300 cursor-pointer"
            >
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Voting & Aspirasi</span>
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl text-indigo-600 shrink-0">
                        <Vote class="size-4" />
                    </div>
                </div>
                <div class="mt-4">
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">
                        {{ metrics.voting.active_polls }} Voting Aktif
                    </h4>
                    <span :class="['text-[9px] font-bold', metrics.voting.unresponded_aspirations > 0 ? 'text-amber-500' : 'text-slate-400']">
                        {{ metrics.voting.unresponded_aspirations > 0 ? metrics.voting.unresponded_aspirations + ' Aspirasi Baru' : 'Aspirasi Terjawab' }}
                    </span>
                </div>
            </Link>
        </div>

        <!-- 2 Column Layout: Calendar & Day's Roster -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            
            <!-- Left 3 Columns: Interactive Spanning Agenda Calendar (Vuetify Style) -->
            <div class="lg:col-span-3 flex flex-col bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs overflow-hidden">
                <!-- Calendar Header controls -->
                <div class="p-4 border-b border-slate-200 dark:border-slate-800/80 bg-slate-50 dark:bg-slate-950 flex flex-col sm:flex-row justify-between items-center gap-3">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-850 dark:text-slate-200 flex items-center gap-2">
                        <Calendar class="size-4.5 text-sky-500" />
                        Agenda & Kegiatan Posko
                    </h3>

                    <!-- Navigate Month controls -->
                    <div class="flex items-center gap-3">
                        <button 
                            @click="prevMonth"
                            class="p-1.5 hover:bg-slate-200 dark:hover:bg-slate-850 rounded-lg transition text-slate-500 cursor-pointer"
                        >
                            <ChevronLeft class="size-3.5" />
                        </button>
                        <span class="text-[10px] sm:text-xs font-bold text-slate-700 dark:text-slate-350 min-w-28 text-center uppercase tracking-wider">
                            {{ currentMonthName }} {{ currentYear }}
                        </span>
                        <button 
                            @click="nextMonth"
                            class="p-1.5 hover:bg-slate-200 dark:hover:bg-slate-850 rounded-lg transition text-slate-500 cursor-pointer"
                        >
                            <ChevronRight class="size-3.5" />
                        </button>
                    </div>
                </div>

                <!-- Calendar Weekdays -->
                <div class="grid grid-cols-7 border-b border-slate-200 dark:border-slate-800 text-center font-bold text-[8px] sm:text-[10px] text-slate-400 bg-slate-50/50 dark:bg-slate-950/20 py-1.5">
                    <div>MINGGU</div>
                    <div>SENIN</div>
                    <div>SELASA</div>
                    <div>RABU</div>
                    <div>KAMIS</div>
                    <div>JUMAT</div>
                    <div>SABTU</div>
                </div>

                <!-- Calendar Month Grid (Week by Week Spanning Event bars) -->
                <div class="flex flex-col">
                    <div 
                        v-for="(week, wIdx) in calendarWeeks" 
                        :key="wIdx"
                        class="relative border-b last:border-0 border-slate-200 dark:border-slate-800 min-h-[56px] sm:min-h-[68px]"
                    >
                        <!-- Day Cells Background Layer -->
                        <div class="grid grid-cols-7 divide-x divide-slate-150 dark:divide-slate-850 h-full min-h-[56px] sm:min-h-[68px]">
                            <div 
                                v-for="day in week.days" 
                                :key="day.date.toISOString()"
                                :class="[
                                    'p-1 flex flex-col justify-start h-full min-h-[56px] sm:min-h-[68px]',
                                    day.isCurrentMonth ? 'bg-white dark:bg-slate-900' : 'bg-slate-50/40 dark:bg-slate-950/20 text-slate-400'
                                ]"
                            >
                                <span 
                                    :class="[
                                        'w-4.5 h-4.5 sm:w-5 sm:h-5 flex items-center justify-center text-[8px] sm:text-[10px] font-bold rounded-full',
                                        day.isToday 
                                            ? 'bg-sky-500 text-white shadow-xs' 
                                            : 'text-slate-850 dark:text-slate-350'
                                    ]"
                                >
                                    {{ day.dayNum }}
                                </span>
                            </div>
                        </div>

                        <!-- Spanning Event Bars Layer (Vuetify Style overlay) -->
                        <div class="absolute inset-x-0 top-6 bottom-0.5 pointer-events-none grid grid-cols-7 gap-y-0.5 auto-rows-max px-0.5">
                            <Link 
                                v-for="layout in week.eventLayouts" 
                                :key="layout.event.id"
                                :href="`/management/schedule?event_id=${layout.event.id}`"
                                :class="[
                                    'mx-0.5 sm:mx-1 px-1.5 py-0.5 text-[8px] sm:text-[9px] font-medium rounded-md truncate pointer-events-auto cursor-pointer shadow-xs border transition duration-200 block border-transparent',
                                    getEventColorClass(layout.event.id)
                                ]"
                                :style="{
                                    gridColumnStart: layout.startIdx + 1,
                                    gridColumnEnd: `span ${layout.span}`,
                                    gridRowStart: layout.level + 1
                                }"
                                :title="`${layout.event.title} (${layout.event.location || 'Posko'})`"
                            >
                                <span class="opacity-80 pr-1 font-mono text-[7px] sm:text-[8px]">{{ formatEventTime(layout.event.start_time) }}</span>
                                {{ layout.event.title }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right 1 Column: Today's Piket Roster -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs p-5 flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2 mb-3">
                        <Clock class="size-5 text-emerald-500" />
                        Piket Hari Ini
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                        Daftar penugasan piket untuk hari <strong>{{ getTodayName() }}</strong>.
                    </p>

                    <!-- Roster List -->
                    <div v-if="todayRoster.length > 0" class="space-y-3.5">
                        <div 
                            v-for="r in todayRoster" 
                            :key="r.id"
                            class="p-3 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-xl flex items-center justify-between gap-3"
                        >
                            <div>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">{{ r.task_name }}</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ r.user ? r.user.name : 'Belum Ditugaskan' }}</p>
                            </div>
                            
                            <CheckCircle class="size-5 text-emerald-500 shrink-0" />
                        </div>
                    </div>

                    <!-- Roster Empty State -->
                    <div v-else class="text-center py-8 bg-slate-50/50 dark:bg-slate-950/20 border border-dashed border-slate-200 dark:border-slate-850 rounded-xl">
                        <Info class="size-5 text-slate-400 mx-auto mb-2" />
                        <p class="text-xs text-slate-500 dark:text-slate-400">Tidak ada jadwal piket hari ini.</p>
                    </div>
                </div>

                <!-- Footer button to schedule page -->
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-850">
                    <Link 
                        href="/management/schedule"
                        class="w-full text-center block bg-slate-100 hover:bg-slate-200 dark:bg-slate-850 dark:hover:bg-slate-800/80 text-slate-700 dark:text-slate-300 font-bold py-2 rounded-xl text-xs transition duration-200 cursor-pointer"
                    >
                        Kelola Jadwal & Agenda
                    </Link>
                </div>
            </div>
        </div>
        </template>
    </div>
</template>
