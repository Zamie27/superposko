<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Search, Server, Edit3, ArrowLeft, Check, X, ShieldAlert } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { useToast } from '@/composables/useToast';

interface HostUser {
    id: number;
    name: string;
    email: string;
    university: string | null;
    group_number: string | null;
    kkn_address: string | null;
    immich_api_key: string | null;
    immich_email: string | null;
    immich_password: string | null;
}

const props = defineProps<{
    hosts: {
        data: HostUser[];
        current_page: number;
        last_page: number;
        prev_page_url: string | null;
        next_page_url: string | null;
        links: any[];
    };
    filters: {
        search?: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard Admin', href: '/admin/dashboard' },
            { title: 'Manajemen Dokumentasi', href: '/admin/documentation-configs' },
        ],
    },
});

const toast = useToast();
const search = ref(props.filters.search || '');

const handleSearch = () => {
    router.get(
        '/admin/documentation-configs',
        { search: search.value },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

// Modal State
const isModalOpen = ref(false);
const editingHost = ref<HostUser | null>(null);
const isSubmitting = ref(false);

const immichForm = ref({
    immich_api_key: '',
    immich_email: '',
    immich_password: '',
});

const openEditModal = (host: HostUser) => {
    editingHost.value = host;
    immichForm.value = {
        immich_api_key: host.immich_api_key || '',
        immich_email: host.immich_email || '',
        immich_password: host.immich_password || '',
    };
    isModalOpen.value = true;
};

const closeEditModal = () => {
    isModalOpen.value = false;
    editingHost.value = null;
};

const saveConfig = async () => {
    if (!editingHost.value) return;

    isSubmitting.value = true;

    try {
        const response = await fetch(`/admin/documentation-configs/${editingHost.value.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify(immichForm.value),
        });

        const result = await response.json();

        if (result.success) {
            toast.success(result.message || 'Konfigurasi berhasil disimpan.');
            
            // Reload page state
            router.reload({ preserveScroll: true });
            closeEditModal();
        } else {
            toast.error(result.message || 'Gagal menyimpan konfigurasi.');
        }
    } catch (error) {
        toast.error('Terjadi kesalahan sistem saat menyimpan data.');
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<template>
    <Head title="Manajemen Dokumentasi - Admin" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6 max-w-7xl mx-auto font-sans">
        <div class="flex items-center gap-3">
            <Link href="/admin/dashboard" class="rounded-lg p-2 hover:bg-slate-100 text-slate-500 transition-colors">
                <ArrowLeft class="size-5" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Dokumentasi</h1>
                <p class="text-sm text-slate-500">Konfigurasikan API key dan kredensial Immich untuk masing-masing posko Host agar mereka dapat mengunggah dokumentasi.</p>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="flex gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="relative flex-1">
                <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                <input
                    v-model="search"
                    @keyup.enter="handleSearch"
                    type="text"
                    placeholder="Cari nama host, email, atau universitas..."
                    class="w-full rounded-xl border border-slate-200 pl-10 pr-4 py-2 text-sm focus:border-sky-500 focus:outline-none"
                />
            </div>
            <Button @click="handleSearch" class="bg-sky-500 hover:bg-sky-600 text-white rounded-xl px-5 py-2 font-bold cursor-pointer">
                Cari
            </Button>
        </div>

        <!-- Hosts Config Table -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                            <th class="p-4">Nama Posko / Host</th>
                            <th class="p-4">Informasi Posko</th>
                            <th class="p-4">Kredensial Immich Terpasang</th>
                            <th class="p-4 w-[120px] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <tr v-if="hosts.data.length === 0">
                            <td colspan="4" class="p-8 text-center text-slate-400">
                                Tidak ada user Host yang ditemukan.
                            </td>
                        </tr>
                        <tr v-for="host in hosts.data" :key="host.id" class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4">
                                <div class="font-medium text-slate-900">{{ host.name }}</div>
                                <div class="text-xs text-slate-400">{{ host.email }}</div>
                            </td>
                            <td class="p-4 text-xs text-slate-600">
                                <div><strong class="text-slate-700">Kampus:</strong> {{ host.university || '-' }}</div>
                                <div><strong class="text-slate-700">No. Posko:</strong> Kelompok {{ host.group_number || '-' }}</div>
                                <div class="truncate max-w-[200px]" :title="host.kkn_address || ''"><strong class="text-slate-700">Alamat:</strong> {{ host.kkn_address || '-' }}</div>
                            </td>
                            <td class="p-4 text-xs">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-semibold text-slate-600">API Key:</span>
                                        <span v-if="host.immich_api_key" class="text-emerald-600 flex items-center gap-1">
                                            <Check class="size-3.5" /> Terkonfigurasi (Masked)
                                        </span>
                                        <span v-else class="text-red-500 flex items-center gap-1">
                                            <X class="size-3.5" /> Belum Diatur
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-semibold text-slate-600">Email:</span>
                                        <span class="text-slate-500">{{ host.immich_email || '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <button
                                    @click="openEditModal(host)"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 hover:border-sky-500 hover:text-sky-600 text-xs font-semibold transition duration-200 cursor-pointer"
                                >
                                    <Edit3 class="size-3.5" /> Atur Kredensial
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="hosts.last_page > 1" class="flex items-center justify-between border-t border-slate-200 bg-white px-4 py-3 sm:px-6">
                <div class="flex flex-1 justify-between sm:hidden">
                    <Link
                        v-if="hosts.prev_page_url"
                        :href="hosts.prev_page_url"
                        class="relative inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    >
                        Sebelumnya
                    </Link>
                    <Link
                        v-if="hosts.next_page_url"
                        :href="hosts.next_page_url"
                        class="relative ml-3 inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    >
                        Selanjutnya
                    </Link>
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs text-slate-500">
                            Halaman <span class="font-semibold">{{ hosts.current_page }}</span> dari <span class="font-semibold">{{ hosts.last_page }}</span>
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-xs" aria-label="Pagination">
                            <Link
                                v-for="(link, idx) in hosts.links"
                                :key="idx"
                                :href="link.url"
                                v-html="link.label"
                                :disabled="!link.url"
                                :class="[
                                    'relative inline-flex items-center px-3 py-1.5 text-xs font-semibold focus:z-20',
                                    link.active ? 'z-10 bg-sky-500 text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500' : 'text-slate-900 ring-1 ring-inset ring-slate-200 hover:bg-slate-50',
                                    idx === 0 ? 'rounded-l-md' : '',
                                    idx === hosts.links.length - 1 ? 'rounded-r-md' : '',
                                    !link.url ? 'opacity-40 pointer-events-none' : ''
                                ]"
                            />
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal Overlay -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300">
            <div class="w-full max-w-lg bg-white rounded-2xl border border-slate-100 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="p-6 border-b flex justify-between items-center bg-slate-50">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <Server class="size-5 text-sky-500" /> Kredensial Immich - {{ editingHost?.name }}
                    </h3>
                    <button @click="closeEditModal" class="p-1 rounded-lg hover:bg-slate-200 text-slate-400 transition cursor-pointer">
                        <X class="size-5" />
                    </button>
                </div>

                <form @submit.prevent="saveConfig" class="p-6 space-y-4">
                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-100 text-xs text-amber-800 flex gap-2">
                        <ShieldAlert class="size-4 shrink-0" />
                        <div>Pastikan Anda telah membuat API Key dan User khusus untuk Posko ini di instance Immich Anda.</div>
                    </div>

                    <!-- API Key Input -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Immich API Key</label>
                        <input
                            v-model="immichForm.immich_api_key"
                            type="text"
                            placeholder="Masukkan API Key dari Immich"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm focus:border-sky-500 focus:outline-none"
                            required
                        />
                    </div>

                    <!-- Email Input -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Email User Immich</label>
                        <input
                            v-model="immichForm.immich_email"
                            type="email"
                            placeholder="email-posko@domain.com"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm focus:border-sky-500 focus:outline-none"
                            required
                        />
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-700">Password User Immich</label>
                        <input
                            v-model="immichForm.immich_password"
                            type="text"
                            placeholder="Password untuk login ke galeri posko"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm focus:border-sky-500 focus:outline-none"
                            required
                        />
                    </div>

                    <!-- Buttons -->
                    <div class="pt-4 border-t flex justify-end gap-2">
                        <Button type="button" variant="outline" @click="closeEditModal" class="rounded-xl px-4 cursor-pointer">
                            Batal
                        </Button>
                        <Button
                            type="submit"
                            :disabled="isSubmitting"
                            class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-5 py-2 rounded-xl flex items-center gap-2 cursor-pointer"
                        >
                            <Spinner v-if="isSubmitting" />
                            Simpan Kredensial
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
