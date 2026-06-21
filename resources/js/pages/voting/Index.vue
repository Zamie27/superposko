<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { 
    Plus, Vote, MessageSquare, ThumbsUp, Trash2, Calendar, 
    User, HelpCircle, Check, Clock, Reply, CheckCircle2, AlertCircle 
} from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { useConfirm } from '@/composables/useConfirm';
import { useToast } from '@/composables/useToast';

// ... interface and props definitions ...
// (rest remains unchanged)
// Let's set up the interval in the setup function:

let reloadInterval: any = null;
onMounted(() => {
    reloadInterval = setInterval(() => {
        router.reload({
            only: ['polls', 'aspirations'],
        });
    }, 4000); // refresh every 4 seconds
});

onUnmounted(() => {
    if (reloadInterval) {
        clearInterval(reloadInterval);
    }
});interface Option {
    id: number;
    option_text: string;
    votes_count: number;
}

interface Poll {
    id: number;
    title: string;
    description: string | null;
    expires_at: string | null;
    is_expired: boolean;
    created_by: string;
    total_votes: number;
    has_voted: boolean;
    voted_option_id: number | null;
    options: Option[];
}

interface Aspiration {
    id: number;
    title: string;
    content: string;
    status: 'pending' | 'review' | 'resolved';
    admin_response: string | null;
    is_anonymous: boolean;
    creator_name: string;
    likes_count: number;
    is_liked: boolean;
    created_at: string;
}

const props = defineProps<{
    polls: Poll[];
    aspirations: Aspiration[];
    canManage: boolean;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Voting & Aspirasi',
                href: '/voting',
            },
        ],
    },
});

const activeTab = ref<'polling' | 'aspirasi'>('polling');
const { confirm } = useConfirm();
const toast = useToast();

// Modal Poll State
const isPollModalOpen = ref(false);
const pollForm = useForm({
    title: '',
    description: '',
    expires_at: '',
    options: ['', ''],
});

const addOptionField = () => {
    if (pollForm.options.length < 10) {
        pollForm.options.push('');
    }
};

const removeOptionField = (index: number) => {
    if (pollForm.options.length > 2) {
        pollForm.options.splice(index, 1);
    }
};

const openCreatePollModal = () => {
    pollForm.reset();
    pollForm.clearErrors();
    pollForm.options = ['', ''];
    isPollModalOpen.value = true;
};

const submitPoll = () => {
    pollForm.post('/voting/poll', {
        onSuccess: () => {
            isPollModalOpen.value = false;
            toast.success('Voting baru berhasil dibuat.');
        },
    });
};

const deletePoll = async (id: number) => {
    const isConfirmed = await confirm({
        title: 'Hapus Voting?',
        message: 'Apakah Anda yakin ingin menghapus voting ini beserta seluruh hasilnya?',
        confirmText: 'Hapus',
        cancelText: 'Batal',
        variant: 'destructive',
    });

    if (isConfirmed) {
        router.delete(`/voting/poll/${id}`, {
            onSuccess: () => {
                toast.success('Voting berhasil dihapus.');
            },
        });
    }
};

// Vote Form State
const voteForm = useForm({
    poll_option_id: null as number | null,
});

const castVote = (pollId: number, optionId: number) => {
    voteForm.poll_option_id = optionId as any;
    voteForm.post(`/voting/poll/${pollId}/vote`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Suara Anda berhasil dikirim.');
        },
        onError: (errors) => {
            if (errors.message) {
                toast.error(errors.message);
            }
        }
    });
};

const cancelVote = (pollId: number) => {
    router.delete(`/voting/poll/${pollId}/vote`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Pilihan Anda berhasil dibatalkan.');
        },
    });
};

// Modal Aspiration State
const isAspirationModalOpen = ref(false);
const aspirationForm = useForm({
    title: '',
    content: '',
    is_anonymous: true,
});

const openAspirationModal = () => {
    aspirationForm.reset();
    aspirationForm.clearErrors();
    isAspirationModalOpen.value = true;
};

const submitAspiration = () => {
    aspirationForm.post('/voting/aspiration', {
        onSuccess: () => {
            isAspirationModalOpen.value = false;
            toast.success('Aspirasi berhasil dikirim.');
        },
    });
};

const toggleLikeAspiration = (id: number) => {
    router.post(`/voting/aspiration/${id}/like`, {}, {
        preserveScroll: true,
    });
};

// Modal Aspiration Respond State
const isRespondModalOpen = ref(false);
const respondingAspiration = ref<Aspiration | null>(null);
const respondForm = useForm({
    status: 'review' as 'pending' | 'review' | 'resolved',
    admin_response: '',
});

const openRespondModal = (aspiration: Aspiration) => {
    respondingAspiration.value = aspiration;
    respondForm.status = aspiration.status;
    respondForm.admin_response = aspiration.admin_response || '';
    respondForm.clearErrors();
    isRespondModalOpen.value = true;
};

const submitResponse = () => {
    if (!respondingAspiration.value) return;

    respondForm.put(`/voting/aspiration/${respondingAspiration.value.id}/respond`, {
        onSuccess: () => {
            isRespondModalOpen.value = false;
            respondingAspiration.value = null;
            toast.success('Tanggapan berhasil disimpan.');
        },
    });
};

const deleteAspiration = async (id: number) => {
    const isConfirmed = await confirm({
        title: 'Hapus Aspirasi?',
        message: 'Apakah Anda yakin ingin menghapus aspirasi ini dari daftar?',
        confirmText: 'Hapus',
        cancelText: 'Batal',
        variant: 'destructive',
    });

    if (isConfirmed) {
        router.delete(`/voting/aspiration/${id}`, {
            onSuccess: () => {
                toast.success('Aspirasi berhasil dihapus.');
            },
        });
    }
};

const calculatePercent = (votesCount: number, totalVotes: number) => {
    if (totalVotes === 0) return 0;
    return Math.round((votesCount / totalVotes) * 100);
};

const getStatusBadgeClass = (status: string) => {
    switch (status) {
        case 'resolved':
            return 'bg-green-500/10 text-green-500 border-green-500/20';
        case 'review':
            return 'bg-[#38BDF8]/10 text-[#38BDF8] border-[#38BDF8]/20';
        default:
            return 'bg-amber-500/10 text-amber-500 border-amber-500/20';
    }
};

const getStatusLabel = (status: string) => {
    switch (status) {
        case 'resolved':
            return 'Selesai / Terjawab';
        case 'review':
            return 'Sedang Ditinjau';
        default:
            return 'Menunggu';
    }
};
</script>

<template>
    <Head title="Voting & Aspirasi" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6 max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-card border rounded-2xl p-5 md:p-6 shadow-xs">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Voting & Aspirasi</h1>
                <p class="text-sm text-slate-500 mt-1">Gunakan pemungutan suara untuk keputusan kelompok, serta kirim saran & masukan secara aman.</p>
            </div>
            <div class="flex gap-2.5 w-full sm:w-auto">
                <Button 
                    v-if="activeTab === 'polling' && canManage" 
                    @click="openCreatePollModal" 
                    class="bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white w-full sm:w-auto flex items-center gap-1.5 rounded-xl font-bold cursor-pointer"
                >
                    <Plus class="size-4" /> Buat Polling
                </Button>
                <Button 
                    v-if="activeTab === 'aspirasi'" 
                    @click="openAspirationModal" 
                    class="bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white w-full sm:w-auto flex items-center gap-1.5 rounded-xl font-bold cursor-pointer"
                >
                    <Plus class="size-4" /> Kirim Aspirasi
                </Button>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="flex border-b border-slate-200">
            <button 
                @click="activeTab = 'polling'" 
                class="px-5 py-3 border-b-2 font-bold text-sm transition-all duration-200 cursor-pointer flex items-center gap-2"
                :class="activeTab === 'polling' ? 'border-[#38BDF8] text-[#38BDF8]' : 'border-transparent text-slate-500 hover:text-slate-700'"
            >
                <Vote class="size-4" /> Pemungutan Suara
            </button>
            <button 
                @click="activeTab = 'aspirasi'" 
                class="px-5 py-3 border-b-2 font-bold text-sm transition-all duration-200 cursor-pointer flex items-center gap-2"
                :class="activeTab === 'aspirasi' ? 'border-[#38BDF8] text-[#38BDF8]' : 'border-transparent text-slate-500 hover:text-slate-700'"
            >
                <MessageSquare class="size-4" /> Kotak Aspirasi
            </button>
        </div>

        <!-- 1. POLLING TAB CONTENT -->
        <div v-if="activeTab === 'polling'" class="space-y-6">
            <div v-if="polls.length === 0" class="flex flex-col items-center justify-center py-16 text-center bg-card border rounded-2xl p-6">
                <div class="p-4 bg-slate-50 rounded-full text-slate-400 mb-4">
                    <Vote class="size-10" />
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Polling</h3>
                <p class="text-sm text-slate-500 max-w-sm mt-1">Pemungutan suara yang aktif akan tampil di sini untuk membantu mengambil mufakat kelompok.</p>
                <Button v-if="canManage" @click="openCreatePollModal" variant="outline" class="mt-4 border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl flex items-center gap-1.5 cursor-pointer">
                    <Plus class="size-4" /> Buat Polling Pertama
                </Button>
            </div>

            <div v-else class="grid md:grid-cols-2 gap-6">
                <div 
                    v-for="poll in polls" 
                    :key="poll.id" 
                    class="bg-card border rounded-2xl p-5 md:p-6 shadow-sm flex flex-col justify-between relative group hover:border-[#38BDF8]/40 transition-colors"
                >
                    <!-- Header Info -->
                    <div>
                        <div class="flex justify-between items-start gap-4 mb-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-[10px] font-semibold tracking-wider text-slate-400 uppercase">
                                    Dibuat oleh {{ poll.created_by }}
                                </span>
                                <span 
                                    v-if="poll.is_expired" 
                                    class="inline-flex items-center gap-1 rounded-full bg-slate-100 text-slate-500 border px-2 py-0.5 text-[10px] font-semibold"
                                >
                                    <Clock class="size-3" /> Berakhir
                                </span>
                                <span 
                                    v-else 
                                    class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 px-2 py-0.5 text-[10px] font-semibold"
                                >
                                    <Clock class="size-3 animate-pulse" /> Aktif
                                </span>
                            </div>

                            <button 
                                v-if="canManage" 
                                @click="deletePoll(poll.id)" 
                                class="text-slate-400 hover:text-red-500 p-1.5 hover:bg-red-50 rounded-lg transition"
                                title="Hapus Voting"
                            >
                                <Trash2 class="size-4" />
                            </button>
                        </div>

                        <h3 class="text-base font-bold text-slate-900 leading-snug">{{ poll.title }}</h3>
                        <p v-if="poll.description" class="text-xs text-slate-500 mt-1 mb-4 leading-relaxed">{{ poll.description }}</p>
                    </div>

                    <!-- Expiration date display -->
                    <div v-if="poll.expires_at" class="flex items-center gap-1.5 text-[11px] text-slate-400 mt-2 mb-4">
                        <Calendar class="size-3.5" />
                        <span>Batas waktu: {{ new Date(poll.expires_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) }}</span>
                    </div>

                    <!-- Options List / Results -->
                    <div class="space-y-3 mt-2">
                        <!-- If user already voted or poll expired, show results -->
                        <template v-if="poll.has_voted || poll.is_expired">
                            <div v-for="option in poll.options" :key="option.id" class="space-y-1">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="font-medium text-slate-800 flex items-center gap-1.5">
                                        {{ option.option_text }}
                                        <span v-if="poll.voted_option_id === option.id" class="text-[#38BDF8] inline-flex items-center" title="Pilihan Anda">
                                            <Check class="size-4" />
                                        </span>
                                    </span>
                                    <span class="text-slate-500 font-semibold">
                                        {{ calculatePercent(option.votes_count, poll.total_votes) }}% ({{ option.votes_count }} suara)
                                    </span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden">
                                    <div 
                                        class="bg-[#38BDF8] h-2.5 rounded-full transition-all duration-700" 
                                        :style="`width: ${calculatePercent(option.votes_count, poll.total_votes)}%`"
                                        :class="{'opacity-70': poll.voted_option_id !== option.id && poll.has_voted}"
                                    ></div>
                                </div>
                            </div>
                            <div class="flex justify-between items-center mt-3">
                                <button 
                                    v-if="poll.has_voted && !poll.is_expired" 
                                    @click="cancelVote(poll.id)" 
                                    class="text-xs text-[#38BDF8] hover:text-[#38BDF8]/80 font-bold hover:underline cursor-pointer"
                                >
                                    Ubah / Batalkan Pilihan
                                </button>
                                <p class="text-[11px] text-slate-400 italic" :class="{'ml-auto': !poll.has_voted || poll.is_expired}">Total: {{ poll.total_votes }} Suara</p>
                            </div>
                        </template>

                        <!-- If active and user has NOT voted, show interactive options -->
                        <template v-else>
                            <div 
                                v-for="option in poll.options" 
                                :key="option.id" 
                                @click="castVote(poll.id, option.id)"
                                class="flex items-center justify-between p-3.5 border rounded-xl hover:border-[#38BDF8] hover:bg-[#38BDF8]/5 transition cursor-pointer select-none bg-slate-50/50 hover:shadow-xs group/opt"
                            >
                                <span class="text-xs font-semibold text-slate-800 group-hover/opt:text-[#38BDF8]">
                                    {{ option.option_text }}
                                </span>
                                <div class="size-4 border-2 border-slate-300 rounded-full flex items-center justify-center group-hover/opt:border-[#38BDF8]">
                                    <div class="size-2 bg-transparent group-hover/opt:bg-[#38BDF8] rounded-full"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. ASPIRASI TAB CONTENT -->
        <div v-if="activeTab === 'aspirasi'" class="space-y-6">
            <div v-if="aspirations.length === 0" class="flex flex-col items-center justify-center py-16 text-center bg-card border rounded-2xl p-6">
                <div class="p-4 bg-slate-50 rounded-full text-slate-400 mb-4">
                    <MessageSquare class="size-10" />
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Aspirasi</h3>
                <p class="text-sm text-slate-500 max-w-sm mt-1">Anggota posko dapat mengirim masukan, kritik, saran, atau ide secara publik maupun anonim.</p>
                <Button @click="openAspirationModal" variant="outline" class="mt-4 border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl flex items-center gap-1.5 cursor-pointer">
                    <Plus class="size-4" /> Kirim Aspirasi Pertama
                </Button>
            </div>

            <div v-else class="space-y-4">
                <div 
                    v-for="asp in aspirations" 
                    :key="asp.id" 
                    class="bg-card border rounded-2xl p-5 shadow-sm hover:border-slate-300 transition-colors flex flex-col justify-between gap-4"
                >
                    <!-- Top Section -->
                    <div class="flex justify-between items-start gap-4">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-400">
                                    <User class="size-3.5" /> {{ asp.creator_name }}
                                </span>
                                <span class="text-slate-300">•</span>
                                <span class="text-[10px] font-semibold text-slate-400">{{ asp.created_at }}</span>
                                <span 
                                    class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-bold capitalize"
                                    :class="getStatusBadgeClass(asp.status)"
                                >
                                    {{ getStatusLabel(asp.status) }}
                                </span>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 leading-snug mt-1.5">{{ asp.title }}</h3>
                        </div>

                        <!-- Trash Actions for admins -->
                        <div class="flex items-center gap-1.5">
                            <button 
                                v-if="canManage" 
                                @click="openRespondModal(asp)" 
                                class="text-slate-400 hover:text-[#38BDF8] p-1.5 hover:bg-[#38BDF8]/5 rounded-lg transition"
                                title="Respon Aspirasi"
                            >
                                <Reply class="size-4" />
                            </button>
                            <button 
                                v-if="canManage" 
                                @click="deleteAspiration(asp.id)" 
                                class="text-slate-400 hover:text-red-500 p-1.5 hover:bg-red-50 rounded-lg transition"
                                title="Hapus Aspirasi"
                            >
                                <Trash2 class="size-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ asp.content }}</p>

                    <!-- Admin Response Display if exists -->
                    <div v-if="asp.admin_response" class="bg-slate-50 dark:bg-slate-900/50 p-4 border rounded-xl flex items-start gap-3 mt-1">
                        <div class="p-2 bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 rounded-lg shrink-0">
                            <CheckCircle2 class="size-4" />
                        </div>
                        <div class="space-y-1">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Tanggapan Admin / Host Posko</span>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">{{ asp.admin_response }}</p>
                        </div>
                    </div>

                    <!-- Bottom Bar (Likes) -->
                    <div class="flex items-center gap-4 border-t pt-3.5 mt-1">
                        <button 
                            @click="toggleLikeAspiration(asp.id)" 
                            class="flex items-center gap-1.5 text-xs font-bold transition hover:scale-105 active:scale-95 cursor-pointer"
                            :class="asp.is_liked ? 'text-rose-500' : 'text-slate-400 hover:text-slate-600'"
                        >
                            <ThumbsUp class="size-4" :class="{'fill-rose-500 stroke-rose-500': asp.is_liked}" />
                            <span>Upvote ({{ asp.likes_count }})</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: CREATE POLL -->
        <Dialog v-model:open="isPollModalOpen">
            <DialogContent class="sm:max-w-[450px]">
                <DialogHeader>
                    <DialogTitle>Buat Polling Baru</DialogTitle>
                    <DialogDescription>Isi detail pertanyaan dan pilihan untuk melakukan voting kelompok.</DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitPoll" class="space-y-4 py-3">
                    <div class="grid gap-1.5">
                        <Label for="poll-title" class="font-bold">Pertanyaan Polling</Label>
                        <Input id="poll-title" v-model="pollForm.title" placeholder="Misal: Agenda Proker Prioritas Minggu Ini?" required />
                        <InputError :message="pollForm.errors.title" />
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="poll-desc" class="font-bold">Keterangan Tambahan (Opsional)</Label>
                        <textarea 
                            id="poll-desc" 
                            v-model="pollForm.description" 
                            placeholder="Beri penjelasan singkat mengenai opsi voting agar anggota paham..." 
                            rows="2"
                            class="flex min-h-[80px] w-full rounded-xl border border-slate-200 bg-transparent px-3 py-2 text-sm shadow-xs placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                        ></textarea>
                        <InputError :message="pollForm.errors.description" />
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="poll-expiry" class="font-bold">Batas Waktu Voting (Opsional)</Label>
                        <Input id="poll-expiry" type="datetime-local" v-model="pollForm.expires_at" />
                        <InputError :message="pollForm.errors.expires_at" />
                    </div>

                    <!-- Options list creation -->
                    <div class="space-y-2.5">
                        <Label class="font-bold block">Pilihan Jawaban (Min. 2)</Label>
                        <div v-for="(opt, idx) in pollForm.options" :key="idx" class="flex gap-2 items-center">
                            <Input 
                                v-model="pollForm.options[idx]" 
                                :placeholder="`Pilihan ke-${idx + 1}`" 
                                required
                            />
                            <button 
                                type="button" 
                                v-if="pollForm.options.length > 2"
                                @click="removeOptionField(idx)" 
                                class="text-slate-400 hover:text-red-500 p-2 border rounded-lg hover:bg-red-50 transition shrink-0"
                            >
                                <Trash2 class="size-4" />
                            </button>
                        </div>
                        <Button 
                            type="button" 
                            v-if="pollForm.options.length < 10" 
                            variant="outline" 
                            @click="addOptionField"
                            class="w-full text-xs py-2 border-dashed border-slate-200 text-slate-500 font-bold hover:bg-slate-50 rounded-xl cursor-pointer"
                        >
                            + Tambah Pilihan
                        </Button>
                        <InputError :message="pollForm.errors.options" />
                    </div>

                    <DialogFooter class="pt-4">
                        <Button type="button" variant="outline" @click="isPollModalOpen = false" class="rounded-xl font-bold cursor-pointer">Batal</Button>
                        <Button type="submit" :disabled="pollForm.processing" class="bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white rounded-xl font-bold cursor-pointer">Buat Polling</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- MODAL: SUBMIT ASPIRASI -->
        <Dialog v-model:open="isAspirationModalOpen">
            <DialogContent class="sm:max-w-[450px]">
                <DialogHeader>
                    <DialogTitle>Kirim Aspirasi & Masukan</DialogTitle>
                    <DialogDescription>Tulis ide, saran, masukan, atau kritik secara tertutup demi kemajuan kelompok.</DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitAspiration" class="space-y-4 py-3">
                    <div class="grid gap-1.5">
                        <Label for="asp-title" class="font-bold">Topik / Judul</Label>
                        <Input id="asp-title" v-model="aspirationForm.title" placeholder="Misal: Usulan jadwal piket masak" required />
                        <InputError :message="aspirationForm.errors.title" />
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="asp-content" class="font-bold">Detail Aspirasi</Label>
                        <textarea 
                            id="asp-content" 
                            v-model="aspirationForm.content" 
                            placeholder="Jelaskan secara rinci detail saran atau keluhan Anda agar dapat ditindaklanjuti..." 
                            rows="4" 
                            required
                            class="flex min-h-[120px] w-full rounded-xl border border-slate-200 bg-transparent px-3 py-2 text-sm shadow-xs placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                        ></textarea>
                        <InputError :message="aspirationForm.errors.content" />
                    </div>

                    <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl border border-slate-100 select-none">
                        <div class="space-y-0.5">
                            <span class="text-xs font-bold text-slate-800 block">Kirim secara Anonim</span>
                            <span class="text-[10px] text-slate-400">Identitas Anda tidak akan ditampilkan ke anggota lain.</span>
                        </div>
                        <input 
                            type="checkbox" 
                            v-model="aspirationForm.is_anonymous" 
                            class="size-4 accent-[#38BDF8] cursor-pointer"
                        />
                    </div>

                    <DialogFooter class="pt-4">
                        <Button type="button" variant="outline" @click="isAspirationModalOpen = false" class="rounded-xl font-bold cursor-pointer">Batal</Button>
                        <Button type="submit" :disabled="aspirationForm.processing" class="bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white rounded-xl font-bold cursor-pointer">Kirim Aspirasi</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- MODAL: RESPOND ASPIRASI -->
        <Dialog v-model:open="isRespondModalOpen">
            <DialogContent class="sm:max-w-[450px]">
                <DialogHeader>
                    <DialogTitle>Tanggapi Aspirasi</DialogTitle>
                    <DialogDescription>Berikan tanggapan admin / solusi atas masukan yang dikirimkan oleh anggota.</DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitResponse" class="space-y-4 py-3">
                    <div class="grid gap-1.5">
                        <Label for="asp-status" class="font-bold">Status Tindak Lanjut</Label>
                        <select 
                            id="asp-status" 
                            v-model="respondForm.status" 
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-[#38BDF8] focus:outline-none bg-white"
                        >
                            <option value="pending">Menunggu (Pending)</option>
                            <option value="review">Sedang Ditinjau (Review)</option>
                            <option value="resolved">Selesai / Terjawab (Resolved)</option>
                        </select>
                        <InputError :message="respondForm.errors.status" />
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="admin-response" class="font-bold">Jawaban / Tanggapan Resmi</Label>
                        <textarea 
                            id="admin-response" 
                            v-model="respondForm.admin_response" 
                            placeholder="Tulis tanggapan atau kesepakatan kelompok..." 
                            rows="4"
                            class="flex min-h-[120px] w-full rounded-xl border border-slate-200 bg-transparent px-3 py-2 text-sm shadow-xs placeholder:text-muted-foreground focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                        ></textarea>
                        <InputError :message="respondForm.errors.admin_response" />
                    </div>

                    <DialogFooter class="pt-4">
                        <Button type="button" variant="outline" @click="isRespondModalOpen = false" class="rounded-xl font-bold cursor-pointer">Batal</Button>
                        <Button type="submit" :disabled="respondForm.processing" class="bg-[#38BDF8] hover:bg-[#38BDF8]/90 text-white rounded-xl font-bold cursor-pointer">Simpan Tanggapan</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
