<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Bell, Mail, Send, ShieldCheck, HelpCircle, AlertCircle } from '@lucide/vue';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { useToast } from '@/composables/useToast';

const props = defineProps<{
    stats: {
        totalUsers: number;
        totalHosts: number;
        totalTrials: number;
        totalRegularUsers: number;
        totalPushSubscriptions: number;
        activePushSubscriptions: number;
    };
    vapidPublicKey: string;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard Admin', href: '/admin/dashboard' },
            { title: 'Manajemen Notifikasi', href: '/admin/notifications' },
        ],
    },
});

const toast = useToast();

// Push Form State
const pushForm = ref({
    title: '',
    body: '',
    url: '',
    target: 'all',
});
const isPushProcessing = ref(false);

// Email Form State
const emailForm = ref({
    subject: '',
    body: '',
    target: 'all',
});
const isEmailProcessing = ref(false);

const getCookie = (name: string): string => {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) {
        return decodeURIComponent(parts.pop()?.split(';').shift() || '');
    }
    return '';
};

// Send Push
const handleSendPush = async () => {
    if (!pushForm.value.title || !pushForm.value.body) {
        toast.error('Judul dan isi pesan push wajib diisi!');
        return;
    }

    isPushProcessing.value = true;

    try {
        const response = await fetch('/admin/notifications/send-push', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
            body: JSON.stringify(pushForm.value),
        });

        const data = await response.json();
        if (response.ok && data.success) {
            toast.success(data.message || 'Push Notification berhasil dikirim.');
            pushForm.value.title = '';
            pushForm.value.body = '';
            pushForm.value.url = '';
        } else {
            toast.error(data.message || 'Gagal mengirim push notification.');
        }
    } catch {
        toast.error('Gagal menghubungi server untuk mengirim push notification.');
    } finally {
        isPushProcessing.value = false;
    }
};

// Send Email
const handleSendEmail = async () => {
    if (!emailForm.value.subject || !emailForm.value.body) {
        toast.error('Subjek dan isi email wajib diisi!');
        return;
    }

    isEmailProcessing.value = true;

    try {
        const response = await fetch('/admin/notifications/send-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                'Accept': 'application/json',
            },
            body: JSON.stringify(emailForm.value),
        });

        const data = await response.json();
        if (response.ok && data.success) {
            toast.success(data.message || 'Email broadcast berhasil dikirim ke antrean.');
            emailForm.value.subject = '';
            emailForm.value.body = '';
        } else {
            toast.error(data.message || 'Gagal mengirim email broadcast.');
        }
    } catch {
        toast.error('Gagal menghubungi server untuk mengirim email broadcast.');
    } finally {
        isEmailProcessing.value = false;
    }
};
</script>

<template>
    <Head title="Manajemen Notifikasi & Email - Admin" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-6xl mx-auto font-sans">
        <div class="flex items-center gap-3">
            <Link href="/admin/dashboard" class="rounded-lg p-2 hover:bg-slate-100 text-slate-500 transition-colors">
                <ArrowLeft class="size-5" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pusat Notifikasi & Broadcast</h1>
                <p class="text-sm text-slate-500">Kirim Web Push Notification instan ke browser pengguna atau kirim Email Massal ke seluruh posko.</p>
            </div>
        </div>

        <!-- Notification Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Browser Tersubskripsi Push</span>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ stats.activePushSubscriptions }}</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Siap menerima push notification</p>
                </div>
                <div class="p-3 bg-amber-50 text-amber-500 rounded-2xl">
                    <Bell class="size-5" />
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Email Terdaftar</span>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ stats.totalUsers }}</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">User non-admin aktif</p>
                </div>
                <div class="p-3 bg-sky-50 text-sky-500 rounded-2xl">
                    <Mail class="size-5" />
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Status Layanan Push</span>
                    <h3 class="text-sm font-bold text-emerald-600 mt-2 flex items-center gap-1">
                        <ShieldCheck class="size-4 shrink-0" /> VAPID Terkonfigurasi
                    </h3>
                    <p class="text-[10px] text-slate-400 mt-1.5 truncate max-w-[200px]" :title="vapidPublicKey">Key: {{ vapidPublicKey ? 'OK' : 'BELUM DI-SET' }}</p>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-500 rounded-2xl">
                    <ShieldCheck class="size-5" />
                </div>
            </div>
        </div>

        <!-- Forms Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Web Push Notification Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                        <Bell class="size-5 text-amber-500" />
                        <div>
                            <h3 class="font-bold text-slate-900">Web Push Notification</h3>
                            <p class="text-xs text-slate-400">Notifikasi real-time yang muncul langsung di perangkat browser.</p>
                        </div>
                    </div>

                    <div class="space-y-3 mt-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">Target Pengguna</label>
                            <select
                                v-model="pushForm.target"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                            >
                                <option value="all">Semua Pengguna ({{ stats.totalUsers }} user)</option>
                                <option value="host">Host Langganan Aktif ({{ stats.totalHosts }} user)</option>
                                <option value="trial">Pengguna Trial ({{ stats.totalTrials }} user)</option>
                                <option value="user">Pengguna Biasa ({{ stats.totalRegularUsers }} user)</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">Judul Notifikasi</label>
                            <input
                                v-model="pushForm.title"
                                type="text"
                                placeholder="Contoh: Pemeliharaan Sistem / Pengumuman Baru"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                            />
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">Isi Pesan Notifikasi</label>
                            <textarea
                                v-model="pushForm.body"
                                rows="3"
                                placeholder="Tuliskan isi pesan notifikasi di sini..."
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                            ></textarea>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">Tautan Tujuan (Link URL)</label>
                            <input
                                v-model="pushForm.url"
                                type="text"
                                placeholder="Contoh: /dashboard atau https://superposko.web.id"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                            />
                            <p class="text-[10px] text-slate-400">Pengguna akan diarahkan ke link ini saat notifikasi diklik.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 mt-6 flex justify-end">
                    <Button :disabled="isPushProcessing" @click="handleSendPush" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold flex items-center gap-2">
                        <Spinner v-if="isPushProcessing" />
                        <Send v-else class="size-4" /> Kirim Push Notifikasi
                    </Button>
                </div>
            </div>

            <!-- Email Announcement Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                        <Mail class="size-5 text-sky-500" />
                        <div>
                            <h3 class="font-bold text-slate-900">Email Pengumuman Global</h3>
                            <p class="text-xs text-slate-400">Kirim email pengumuman massal jika server bermasalah atau informasi penting.</p>
                        </div>
                    </div>

                    <div class="space-y-3 mt-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">Target Penerima</label>
                            <select
                                v-model="emailForm.target"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                            >
                                <option value="all">Semua Pengguna ({{ stats.totalUsers }} user)</option>
                                <option value="host">Host Langganan Aktif ({{ stats.totalHosts }} user)</option>
                                <option value="trial">Pengguna Trial ({{ stats.totalTrials }} user)</option>
                                <option value="user">Pengguna Biasa ({{ stats.totalRegularUsers }} user)</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">Subjek Email</label>
                            <input
                                v-model="emailForm.subject"
                                type="text"
                                placeholder="Contoh: Pemberitahuan: Kendala Teknis Pada Server"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                            />
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-700">Isi Email (Mendukung Multi-line)</label>
                            <textarea
                                v-model="emailForm.body"
                                rows="6"
                                placeholder="Halo, kami mengumumkan bahwa..."
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 mt-6 flex justify-end">
                    <Button :disabled="isEmailProcessing" @click="handleSendEmail" class="bg-sky-500 hover:bg-sky-600 text-white font-semibold flex items-center gap-2">
                        <Spinner v-if="isEmailProcessing" />
                        <Send v-else class="size-4" /> Kirim Email Broadcast
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
