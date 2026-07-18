<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { 
    Calendar, Clock, MapPin, Trash2, Plus, Edit3, X, User as UserIcon, CalendarDays, ClipboardList
} from '@lucide/vue';
import { ref, onMounted } from 'vue';
import { Button } from '@/components/ui/button';
import { useConfirm } from '@/composables/useConfirm';
import { useToast } from '@/composables/useToast';

interface Member {
    id: number;
    name: string;
    role: string;
}

interface RosterItem {
    id: number;
    day_of_week: 'monday' | 'tuesday' | 'wednesday' | 'thursday' | 'friday' | 'saturday' | 'sunday';
    task_name: string;
    user_id: number;
    user_name: string;
}

interface EventItem {
    id: number;
    title: string;
    description: string | null;
    start_time: string;
    end_time: string | null;
    location: string | null;
    created_by_name: string;
}

const props = defineProps<{
    members: Member[];
    rosters: RosterItem[];
    events: EventItem[];
    canManage: boolean;
}>();

const toast = useToast();
const { confirm } = useConfirm();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Manajemen',
                href: '#',
            },
            {
                title: 'Piket & Agenda',
                href: '/management/schedule',
            },
        ],
    },
});

const activeTab = ref<'piket' | 'agenda'>('piket');
const highlightedEventId = ref<number | null>(null);

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const eventIdParam = params.get('event_id');

    if (eventIdParam) {
        activeTab.value = 'agenda';
        highlightedEventId.value = parseInt(eventIdParam);
        
        setTimeout(() => {
            const element = document.getElementById(`event-card-${eventIdParam}`);

            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 200);
    }
});

// Roster Form Modal States
const isRosterModalOpen = ref(false);
const rosterForm = useForm({
    day_of_week: 'monday',
    task_name: '',
    user_id: '' as string | number,
});

// Event Form Modal States
const isEventModalOpen = ref(false);
const isEditEventMode = ref(false);
const selectedEventId = ref<number | null>(null);

const eventForm = useForm({
    title: '',
    description: '',
    start_time: '',
    end_time: '',
    location: '',
});

const daysList = [
    { key: 'monday', label: 'Senin' },
    { key: 'tuesday', label: 'Selasa' },
    { key: 'wednesday', label: 'Rabu' },
    { key: 'thursday', label: 'Kamis' },
    { key: 'friday', label: 'Jumat' },
    { key: 'saturday', label: 'Sabtu' },
    { key: 'sunday', label: 'Minggu' },
];

const defaultTasks = [
    'Menyapu & Mengepel',
    'Memasak',
    'Cuci Piring',
    'Belanja Pasar',
    'Buang Sampah'
];

const predefinedTasks = computed(() => {
    const customTasks = new Set<string>();
    props.rosters.forEach(r => {
        if (r.task_name && !defaultTasks.includes(r.task_name)) {
            customTasks.add(r.task_name);
        }
    });
    return [...defaultTasks, ...Array.from(customTasks), 'Lainnya'];
});

const taskOption = ref(defaultTasks[0]);



// Open Roster Modal
const openRosterModal = (dayKey?: string) => {
    rosterForm.reset();
    taskOption.value = predefinedTasks.value[0];
    rosterForm.task_name = taskOption.value;

    if (dayKey) {
        rosterForm.day_of_week = dayKey;
    }

    if (props.members.length > 0) {
        rosterForm.user_id = props.members[0].id;
    }

    isRosterModalOpen.value = true;
};

const closeRosterModal = () => {
    isRosterModalOpen.value = false;
};

// Submit Roster Form
const submitRoster = () => {
    rosterForm.post('/management/schedule/roster', {
        onSuccess: () => {
            closeRosterModal();
            toast.success('Jadwal piket berhasil ditambahkan.');
        },
    });
};

// Delete Roster Assignment
const deleteRoster = async (rosterId: number) => {
    const ok = await confirm({
        title: 'Hapus Jadwal Piket',
        message: 'Apakah Anda yakin ingin menghapus tugas piket ini?',
        confirmText: 'Hapus',
        cancelText: 'Batal',
        variant: 'destructive',
    });

    if (ok) {
        router.delete(`/management/schedule/roster/${rosterId}`, {
            onSuccess: () => {
                toast.success('Jadwal piket berhasil dihapus.');
            },
        });
    }
};

// Open Event Modal
const openEventModal = (event?: EventItem) => {
    eventForm.reset();

    if (event) {
        isEditEventMode.value = true;
        selectedEventId.value = event.id;
        eventForm.title = event.title;
        eventForm.description = event.description || '';
        eventForm.start_time = event.start_time.substring(0, 16);
        eventForm.end_time = event.end_time ? event.end_time.substring(0, 16) : '';
        eventForm.location = event.location || '';
    } else {
        isEditEventMode.value = false;
        selectedEventId.value = null;
    }

    isEventModalOpen.value = true;
};

const closeEventModal = () => {
    isEventModalOpen.value = false;
};

// Submit Event Form
const submitEvent = () => {
    if (isEditEventMode.value && selectedEventId.value) {
        eventForm.put(`/management/schedule/event/${selectedEventId.value}`, {
            onSuccess: () => {
                closeEventModal();
                toast.success('Agenda berhasil diperbarui.');
            },
        });
    } else {
        eventForm.post('/management/schedule/event', {
            onSuccess: () => {
                closeEventModal();
                toast.success('Agenda berhasil ditambahkan.');
            },
        });
    }
};

// Delete Event
const deleteEvent = async (eventId: number) => {
    const ok = await confirm({
        title: 'Hapus Agenda',
        message: 'Apakah Anda yakin ingin menghapus agenda kegiatan ini?',
        confirmText: 'Hapus',
        cancelText: 'Batal',
        variant: 'destructive',
    });

    if (ok) {
        router.delete(`/management/schedule/event/${eventId}`, {
            onSuccess: () => {
                toast.success('Agenda berhasil dihapus.');
            },
        });
    }
};

// Format Datetime to Human Readable ID format
const formatDateTime = (isoString: string) => {
    const date = new Date(isoString);

    return date.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head title="Jadwal Piket & Agenda Kegiatan" />

    <div class="flex flex-col gap-6 p-6 max-w-7xl mx-auto w-full">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Piket & Agenda Kegiatan</h1>
                <p class="text-sm text-slate-500">Koordinasi tugas harian posko dan agenda kegiatan KKN.</p>
            </div>
            
            <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl self-start sm:self-center">
                <button 
                    @click="activeTab = 'piket'"
                    :class="[
                        'flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition cursor-pointer',
                        activeTab === 'piket' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900'
                    ]"
                >
                    <ClipboardList class="size-4" />
                    Jadwal Piket
                </button>
                <button 
                    @click="activeTab = 'agenda'"
                    :class="[
                        'flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition cursor-pointer',
                        activeTab === 'agenda' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900'
                    ]"
                >
                    <CalendarDays class="size-4" />
                    Agenda Kegiatan
                </button>
            </div>
        </div>

        <!-- 1. Jadwal Piket Tab -->
        <div v-if="activeTab === 'piket'" class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Jadwal Tugas Mingguan</h2>
                    <p class="text-xs text-slate-500">Daftar giliran tugas posko harian.</p>
                </div>
                <Button v-if="canManage" @click="() => openRosterModal()" class="flex items-center gap-2 rounded-xl bg-sky-500 hover:bg-sky-600 cursor-pointer">
                    <Plus class="size-4" />
                    Bagi Tugas Piket
                </Button>
            </div>

            <!-- Days Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4">
                <div 
                    v-for="day in daysList" 
                    :key="day.key" 
                    class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden flex flex-col h-full min-h-[250px]"
                >
                    <!-- Day Title Header -->
                    <div class="bg-slate-50 px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                        <span class="font-bold text-sm text-slate-800">{{ day.label }}</span>
                        <button 
                            v-if="canManage" 
                            @click="openRosterModal(day.key)" 
                            class="p-1 rounded-lg hover:bg-slate-200 text-slate-400 hover:text-sky-500 transition cursor-pointer"
                            title="Tambah piket untuk hari ini"
                        >
                            <Plus class="size-4" />
                        </button>
                    </div>

                    <!-- Duties List -->
                    <div class="p-3 flex-1 flex flex-col gap-2 overflow-y-auto">
                        <div 
                            v-for="roster in rosters.filter(r => r.day_of_week === day.key)" 
                            :key="roster.id"
                            class="group relative border border-slate-100 bg-slate-50/50 rounded-xl p-2.5 flex flex-col gap-1 transition hover:border-slate-200 hover:bg-white"
                        >
                            <div class="flex items-start justify-end gap-2">
                                <button 
                                    v-if="canManage" 
                                    @click="deleteRoster(roster.id)" 
                                    class="opacity-0 group-hover:opacity-100 p-0.5 rounded-md text-red-500 hover:bg-red-50 transition cursor-pointer absolute right-1.5 top-1.5"
                                    title="Hapus tugas"
                                >
                                    <Trash2 class="size-3.5" />
                                </button>
                            </div>
                            <div class="mt-1 flex flex-col gap-1">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ roster.task_name || 'Piket' }}</span>
                                <div class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                                    <UserIcon class="size-3.5" />
                                    <span class="text-[10px] font-medium truncate">{{ roster.user_name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Empty state per day -->
                        <div 
                            v-if="rosters.filter(r => r.day_of_week === day.key).length === 0" 
                            class="flex-1 flex flex-col items-center justify-center py-8 text-center text-slate-400"
                        >
                            <span class="text-[11px] font-medium italic">Tidak ada piket</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Agenda Kegiatan Tab -->
        <div v-else class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Kalender Agenda Posko</h2>
                    <p class="text-xs text-slate-500">Rapat, kunjungan, serta rencana program kerja kelompok.</p>
                </div>
                <Button v-if="canManage" @click="() => openEventModal()" class="flex items-center gap-2 rounded-xl bg-sky-500 hover:bg-sky-600 cursor-pointer">
                    <Plus class="size-4" />
                    Tambah Agenda
                </Button>
            </div>

            <!-- Events List -->
            <div v-if="events.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div 
                    v-for="event in events" 
                    :key="event.id" 
                    :id="`event-card-${event.id}`"
                    :class="[
                        'bg-white rounded-2xl border transition p-5 flex flex-col gap-4 relative overflow-hidden',
                        highlightedEventId === event.id 
                            ? 'border-sky-500 shadow-md ring-2 ring-sky-500/20 dark:ring-sky-500/40 bg-sky-50/10 dark:bg-sky-500/5 scale-[1.02] duration-300' 
                            : 'border-slate-100 shadow-xs hover:shadow-md'
                    ]"
                >
                    <!-- Event Accent Border -->
                    <div :class="['absolute left-0 top-0 bottom-0 w-1', highlightedEventId === event.id ? 'bg-sky-600' : 'bg-sky-500']"></div>

                    <!-- Event Content Header -->
                    <div class="flex justify-between items-start gap-4 pl-1">
                        <div class="space-y-1">
                            <h3 class="font-bold text-slate-800 text-base leading-snug">{{ event.title }}</h3>
                            <p class="text-xs text-slate-400">Dibuat oleh: {{ event.created_by_name }}</p>
                        </div>

                        <!-- Action controls -->
                        <div v-if="canManage" class="flex items-center gap-1">
                            <button 
                                @click="openEventModal(event)" 
                                class="p-1.5 rounded-lg text-slate-400 hover:text-sky-500 hover:bg-slate-50 transition cursor-pointer"
                                title="Edit Agenda"
                            >
                                <Edit3 class="size-4" />
                            </button>
                            <button 
                                @click="deleteEvent(event.id)" 
                                class="p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-slate-50 transition cursor-pointer"
                                title="Hapus Agenda"
                            >
                                <Trash2 class="size-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Event Description -->
                    <p v-if="event.description" class="text-slate-600 text-sm pl-1 line-clamp-3 leading-relaxed">
                        {{ event.description }}
                    </p>

                    <!-- Event Meta Info -->
                    <div class="mt-auto pl-1 pt-3 border-t border-slate-50 space-y-2 text-xs text-slate-500">
                        <div class="flex items-center gap-2">
                            <Clock class="size-3.5 text-slate-400 shrink-0" />
                            <span>{{ formatDateTime(event.start_time) }}</span>
                        </div>
                        <div v-if="event.end_time" class="flex items-center gap-2">
                            <div class="w-3.5 h-3.5 shrink-0 flex items-center justify-center">
                                <span class="text-[9px] text-slate-400 font-bold">s/d</span>
                            </div>
                            <span>{{ formatDateTime(event.end_time) }}</span>
                        </div>
                        <div v-if="event.location" class="flex items-center gap-2">
                            <MapPin class="size-3.5 text-slate-400 shrink-0" />
                            <span class="truncate">{{ event.location }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-else class="flex flex-col items-center justify-center py-16 px-4 border border-dashed border-slate-200 rounded-2xl bg-white text-center">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 mb-4">
                    <Calendar class="size-6" />
                </div>
                <h3 class="font-bold text-slate-800 text-base mb-1">Agenda Kosong</h3>
                <p class="text-sm text-slate-500 max-w-sm">Belum ada agenda posko atau program kerja terdekat yang dijadwalkan.</p>
            </div>
        </div>

        <!-- 3. Roster Modal Form -->
        <div v-if="isRosterModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
            <div class="w-full max-w-md bg-white rounded-2xl border border-slate-100 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="p-5 border-b flex justify-between items-center bg-slate-50">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <ClipboardList class="size-5 text-sky-500" />
                        Atur Tugas Piket Posko
                    </h3>
                    <button @click="closeRosterModal" class="p-1 rounded-lg hover:bg-slate-200 text-slate-400 transition cursor-pointer">
                        <X class="size-5" />
                    </button>
                </div>

                <form @submit.prevent="submitRoster" class="p-5 space-y-4">
                    <!-- Day Select -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Hari Tugas</label>
                        <select 
                            v-model="rosterForm.day_of_week"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none"
                            required
                        >
                            <option v-for="day in daysList" :key="day.key" :value="day.key">{{ day.label }}</option>
                        </select>
                    </div>


                    <!-- Task Name Input -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Tugas Piket</label>
                        <select 
                            v-model="taskOption"
                            @change="taskOption !== 'Lainnya' ? rosterForm.task_name = taskOption : rosterForm.task_name = ''"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none"
                        >
                            <option v-for="task in predefinedTasks" :key="task" :value="task">{{ task }}</option>
                        </select>
                        
                        <input 
                            v-if="taskOption === 'Lainnya'"
                            v-model="rosterForm.task_name"
                            type="text"
                            placeholder="Ketik tugas spesifik..."
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none mt-2"
                            required
                            maxlength="255"
                        />
                        <p v-if="rosterForm.errors.task_name" class="text-xs text-red-500">{{ rosterForm.errors.task_name }}</p>
                    </div>

                    <!-- Member Select -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Pilih Anggota</label>
                        <select 
                            v-model="rosterForm.user_id"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none"
                            required
                        >
                            <option v-for="member in members" :key="member.id" :value="member.id">
                                {{ member.name }} ({{ member.role }})
                            </option>
                        </select>
                        <p v-if="rosterForm.errors.user_id" class="text-xs text-red-500">{{ rosterForm.errors.user_id }}</p>
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <Button type="button" variant="outline" @click="closeRosterModal" class="rounded-xl cursor-pointer">Batal</Button>
                        <Button type="submit" class="rounded-xl bg-sky-500 hover:bg-sky-600 cursor-pointer" :disabled="rosterForm.processing">
                            Tugaskan
                        </Button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 4. Event Modal Form -->
        <div v-if="isEventModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
            <div class="w-full max-w-md bg-white rounded-2xl border border-slate-100 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="p-5 border-b flex justify-between items-center bg-slate-50">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <CalendarDays class="size-5 text-sky-500" />
                        {{ isEditEventMode ? 'Edit Agenda Kegiatan' : 'Tambah Agenda Baru' }}
                    </h3>
                    <button @click="closeEventModal" class="p-1 rounded-lg hover:bg-slate-200 text-slate-400 transition cursor-pointer">
                        <X class="size-5" />
                    </button>
                </div>

                <form @submit.prevent="submitEvent" class="p-5 space-y-4">
                    <!-- Title -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Nama Agenda / Kegiatan</label>
                        <input
                            v-model="eventForm.title"
                            type="text"
                            placeholder="Contoh: Rapat Evaluasi Mingguan"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none"
                            required
                        />
                        <p v-if="eventForm.errors.title" class="text-xs text-red-500">{{ eventForm.errors.title }}</p>
                    </div>

                    <!-- Description -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Deskripsi / Detail</label>
                        <textarea
                            v-model="eventForm.description"
                            placeholder="Tulis rincian pembahasan rapat atau rencana proker..."
                            rows="3"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <!-- Start Time -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Waktu Mulai</label>
                        <input
                            v-model="eventForm.start_time"
                            type="datetime-local"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none"
                            required
                        />
                        <p v-if="eventForm.errors.start_time" class="text-xs text-red-500">{{ eventForm.errors.start_time }}</p>
                    </div>

                    <!-- End Time -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Waktu Selesai (Opsional)</label>
                        <input
                            v-model="eventForm.end_time"
                            type="datetime-local"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none"
                        />
                        <p v-if="eventForm.errors.end_time" class="text-xs text-red-500">{{ eventForm.errors.end_time }}</p>
                    </div>

                    <!-- Location -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700">Lokasi / Tempat</label>
                        <input
                            v-model="eventForm.location"
                            type="text"
                            placeholder="Contoh: Balai Desa / Posko KKN"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none"
                        />
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <Button type="button" variant="outline" @click="closeEventModal" class="rounded-xl cursor-pointer">Batal</Button>
                        <Button type="submit" class="rounded-xl bg-sky-500 hover:bg-sky-600 cursor-pointer" :disabled="eventForm.processing">
                            {{ isEditEventMode ? 'Perbarui' : 'Simpan' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
