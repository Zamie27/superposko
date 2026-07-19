<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { ref, computed } from 'vue';
import { CheckCircle2, XCircle, Loader2 } from '@lucide/vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from "@/components/ui/dialog";

const props = defineProps<{
    members: any[];
    cashDues: any[];
    canEdit: boolean;
    startDate: string | null;
}>();

const totalWeeks = 10;
const weeks = Array.from({ length: totalWeeks }, (_, i) => i + 1);

const isModalOpen = ref(false);
const selectedMember = ref<any>(null);
const selectedWeek = ref<number>(0);

const form = useForm({
    user_id: '',
    week_number: 0,
    amount: '',
    payment_method: 'Cash',
    date: '',
});

const settingsForm = useForm({
    cash_dues_start_date: props.startDate || '',
});

const saveSettings = () => {
    settingsForm.post('/catatan-kas/settings', {
        preserveScroll: true,
    });
};

const formatDate = (date: Date) => {
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
};

const getWeekBounds = (weekNumber: number) => {
    if (!props.startDate) return null;
    
    const start = new Date(props.startDate);
    const dayOfWeek = start.getDay(); 
    const daysUntilSunday = dayOfWeek === 0 ? 0 : 7 - dayOfWeek;
    
    const week1End = new Date(start);
    week1End.setDate(start.getDate() + daysUntilSunday);
    
    if (weekNumber === 1) {
        return { start, end: week1End };
    }
    
    const weekStart = new Date(week1End);
    weekStart.setDate(week1End.getDate() + 1 + (weekNumber - 2) * 7);
    
    const weekEnd = new Date(weekStart);
    weekEnd.setDate(weekStart.getDate() + 6);
    
    return { start: weekStart, end: weekEnd };
};

const getWeekDateRange = (weekNumber: number) => {
    const bounds = getWeekBounds(weekNumber);
    if (!bounds) return '';
    return `${formatDate(bounds.start)} - ${formatDate(bounds.end)}`;
};

const openPaymentModal = (member: any, week: number) => {
    if (!props.canEdit) return;
    
    // Check if already paid
    if (getPayment(member.id, week)) return;

    selectedMember.value = member;
    selectedWeek.value = week;
    
    form.user_id = member.id;
    form.week_number = week;
    form.amount = '';
    form.payment_method = 'Cash';
    
    const bounds = getWeekBounds(week);
    if (bounds) {
        // adjust for timezone issues by doing local string extraction
        const localDate = new Date(bounds.start.getTime() - (bounds.start.getTimezoneOffset() * 60000)).toISOString().split('T')[0];
        form.date = localDate;
    } else {
        form.date = new Date().toISOString().split('T')[0];
    }
    
    isModalOpen.value = true;
};

const formattedAmount = computed(() => {
    if (!form.amount) return '';
    return Number(form.amount).toLocaleString('id-ID');
});

const onAmountInput = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const cleanValue = target.value.replace(/\D/g, '');
    form.amount = cleanValue ? parseInt(cleanValue, 10).toString() : '';
};

const submitPayment = () => {
    form.post('/catatan-kas', {
        preserveScroll: true,
        onSuccess: () => {
            isModalOpen.value = false;
            form.reset();
        }
    });
};

const getPayment = (userId: number, week: number) => {
    return props.cashDues.find(due => due.user_id === userId && due.week_number === week);
};

const formatRupiah = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Catatan Kas', href: '/catatan-kas' },
        ],
    },
});
</script>

<template>
    <Head title="Pencatatan Kas Mingguan" />

    <div class="relative flex flex-col gap-6 rounded-xl p-6 min-h-[400px] w-full max-w-full">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pencatatan Kas Mingguan</h1>
                <p class="text-sm text-slate-500 mt-1">Pantau pembayaran iuran uang kas per-minggu dari seluruh anggota kelompok.</p>
            </div>
        </div>

        <div v-if="!canEdit" class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg text-sm flex gap-2 items-start">
            <Info class="w-5 h-5 shrink-0" />
            <div>
                Anda login dengan role Anggota. Anda hanya dapat melihat laporan iuran kas ini. Untuk mencatatkan pembayaran, silakan hubungi Ketua, Wakil, Sekretaris, atau Bendahara.
            </div>
        </div>

        <div v-if="canEdit" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1 w-full sm:max-w-xs">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Mulai Kas (Minggu 1)</label>
                <input v-model="settingsForm.cash_dues_start_date" type="date" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 dark:bg-slate-900 focus:border-[#38BDF8] focus:ring-[#38BDF8]">
            </div>
            <Button @click="saveSettings" :disabled="settingsForm.processing" class="bg-slate-800 hover:bg-slate-700 text-white">
                <template v-if="settingsForm.processing">
                    <Loader2 class="w-4 h-4 mr-2 animate-spin" />
                    Menyimpan...
                </template>
                <template v-else>
                    Simpan Pengaturan
                </template>
            </Button>
        </div>

        <div class="w-full min-w-0 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-6 mt-2">
            <div class="overflow-x-auto w-full pb-4">
                <table class="w-full text-sm text-left border-collapse min-w-max">
                    <thead>
                        <tr>
                            <th class="border border-slate-300 dark:border-slate-700 bg-sky-500 text-white font-bold py-2 px-3 text-center w-10">No.</th>
                            <th class="border border-slate-300 dark:border-slate-700 bg-sky-500 text-white font-bold py-2 px-4 sticky left-0 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">Nama Anggota</th>
                            <th 
                                v-for="week in weeks" 
                                :key="week"
                                class="border border-slate-300 dark:border-slate-700 bg-sky-500 text-white font-bold py-2 px-2 text-center text-xs whitespace-nowrap"
                            >
                                <div>Minggu {{ week }}</div>
                                <div v-if="getWeekDateRange(week)" class="text-[9px] font-normal opacity-80 mt-0.5">
                                    {{ getWeekDateRange(week) }}
                                </div>
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
                            
                            <!-- Status Mingguan -->
                            <td 
                                v-for="week in weeks" 
                                :key="week"
                                class="border border-slate-300 dark:border-slate-700 p-0 text-center relative"
                            >
                                <template v-if="getPayment(member.id, week)">
                                    <div class="w-full h-full p-2 bg-green-50 text-green-700 flex flex-col items-center justify-center gap-1 min-h-[60px]">
                                        <CheckCircle2 class="w-4 h-4 text-green-600" />
                                        <span class="text-[10px] font-bold">{{ formatRupiah(getPayment(member.id, week).amount) }}</span>
                                    </div>
                                </template>
                                <template v-else>
                                    <div 
                                        @click="openPaymentModal(member, week)"
                                        class="w-full h-full p-2 flex flex-col items-center justify-center gap-1 min-h-[60px] transition-colors"
                                        :class="canEdit ? 'cursor-pointer hover:bg-slate-100 text-slate-400 hover:text-sky-600' : 'text-slate-300'"
                                    >
                                        <XCircle class="w-4 h-4 opacity-50" />
                                        <span class="text-[10px] opacity-70">Belum</span>
                                    </div>
                                </template>
                            </td>
                        </tr>
                        <tr v-if="members.length === 0">
                            <td :colspan="weeks.length + 2" class="border border-slate-300 dark:border-slate-700 py-4 px-4 text-center text-slate-500">
                                Tidak ada data anggota.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Modal -->
        <Dialog :open="isModalOpen" @update:open="isModalOpen = $event">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Catat Pembayaran Kas</DialogTitle>
                    <DialogDescription>
                        Catat iuran untuk <strong class="text-slate-900 dark:text-white">{{ selectedMember?.name }}</strong> pada <strong>Minggu ke-{{ selectedWeek }}</strong>.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitPayment" class="space-y-4 py-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal Bayar</label>
                        <input 
                            v-model="form.date" 
                            type="date" 
                            :min="getWeekBounds(selectedWeek)?.start ? new Date(getWeekBounds(selectedWeek)!.start.getTime() - (getWeekBounds(selectedWeek)!.start.getTimezoneOffset() * 60000)).toISOString().split('T')[0] : ''"
                            :max="getWeekBounds(selectedWeek)?.end ? new Date(getWeekBounds(selectedWeek)!.end.getTime() - (getWeekBounds(selectedWeek)!.end.getTimezoneOffset() * 60000)).toISOString().split('T')[0] : ''"
                            required
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-700 dark:bg-slate-900 focus:border-[#38BDF8] focus:ring-[#38BDF8]"
                        >
                        <InputError :message="form.errors.date" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nominal Iuran (Rp)</label>
                        <input 
                            :value="formattedAmount" 
                            @input="onAmountInput" 
                            type="text" 
                            required 
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-700 dark:bg-slate-900 focus:border-[#38BDF8] focus:ring-[#38BDF8]"
                        >
                        <InputError :message="form.errors.amount" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Simpan Ke Rekening</label>
                        <select v-model="form.payment_method" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 dark:bg-slate-900 focus:border-[#38BDF8] focus:ring-[#38BDF8]">
                            <option value="Cash">Cash (Uang Tunai)</option>
                            <option value="SeaBank">SeaBank</option>
                            <option value="DANA">DANA</option>
                        </select>
                        <InputError :message="form.errors.payment_method" class="mt-1" />
                    </div>

                    <DialogFooter class="pt-4">
                        <Button type="button" variant="outline" @click="isModalOpen = false">Batal</Button>
                        <Button type="submit" class="bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white" :disabled="form.processing">
                            <template v-if="form.processing">
                                <Loader2 class="w-4 h-4 mr-2 animate-spin" />
                                Menyimpan...
                            </template>
                            <template v-else>
                                Simpan Pembayaran
                            </template>
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
