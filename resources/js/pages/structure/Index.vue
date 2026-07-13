<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Download } from '@lucide/vue';
import html2canvas from 'html2canvas';

const props = defineProps<{
    members: any[];
}>();

const getRoleLabel = (member: any) => {
    if (member.role === 'lainnya') {
        return member.custom_role?.name ?? 'Role Kustom';
    }
    const roles: Record<string, string> = {
        'ketua': 'Ketua',
        'wakil': 'Wakil Ketua',
        'sekretaris': 'Sekretaris',
        'bendahara': 'Bendahara',
        'logistik': 'Logistik',
        'pdd': 'PDD',
        'humas': 'Humas',
        'acara': 'Acara',
        'dpl': 'DPL'
    };
    return roles[member.role] ?? member.role;
};

const groupedMembers = computed(() => {
    const dpl = props.members.filter(m => m.role === 'dpl');
    const ketua = props.members.filter(m => m.role === 'ketua');
    const wakil = props.members.filter(m => m.role === 'wakil');
    const sekretaris = props.members.filter(m => m.role === 'sekretaris');
    const bendahara = props.members.filter(m => m.role === 'bendahara');
    const divisions = props.members.filter(m => !['dpl', 'ketua', 'wakil', 'sekretaris', 'bendahara', 'user', 'admin'].includes(m.role));

    return {
        dpl,
        ketua,
        wakil,
        sekretaris,
        bendahara,
        divisions
    };
});

const orgChartRef = ref<HTMLElement | null>(null);
const isDownloading = ref(false);

const downloadImage = async () => {
    if (!orgChartRef.value) return;
    isDownloading.value = true;
    try {
        const canvas = await html2canvas(orgChartRef.value, {
            backgroundColor: document.documentElement.classList.contains('dark') ? '#0f172a' : '#f8fafc',
            scale: 2, // High resolution
            useCORS: true,
        });
        const link = document.createElement('a');
        link.download = 'struktur-organisasi.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    } catch (error) {
        console.error('Gagal mengunduh gambar:', error);
        alert('Gagal mengunduh gambar struktur.');
    } finally {
        isDownloading.value = false;
    }
};
</script>

<template>
    <Head title="Struktur Organisasi" />

    <div class="relative flex flex-col gap-6 overflow-x-auto rounded-xl p-6 min-h-[100vh]">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-2 gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Struktur Organisasi</h1>
                <p class="text-sm text-slate-500 mt-1">Hierarki kepengurusan posko KKN Anda.</p>
            </div>
            <button 
                @click="downloadImage" 
                :disabled="isDownloading"
                class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50"
            >
                <Download class="w-4 h-4" />
                <span>{{ isDownloading ? 'Memproses...' : 'Download Gambar' }}</span>
            </button>
        </div>

        <!-- Org Chart Container -->
        <div 
            ref="orgChartRef" 
            class="w-full overflow-x-auto py-12 bg-slate-50 dark:bg-slate-900/30 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-inner flex-1"
        >
            <div class="min-w-[900px] flex flex-col items-center">
                
                <!-- DPL Level -->
                <div v-if="groupedMembers.dpl.length" class="flex flex-col items-center">
                    <div class="flex gap-4">
                        <div v-for="member in groupedMembers.dpl" :key="member.id" class="w-64 bg-indigo-600 border border-indigo-700 text-white rounded-2xl p-4 shadow-[0_8px_30px_rgb(0,0,0,0.12)] text-center z-10 relative">
                            <h3 class="font-bold text-lg mb-1">{{ member.name }}</h3>
                            <p class="text-xs font-semibold text-indigo-200 uppercase tracking-widest">{{ getRoleLabel(member) }}</p>
                        </div>
                    </div>
                    <!-- Connector line -->
                    <div class="w-px h-10 bg-slate-300 dark:bg-slate-700"></div>
                </div>

                <!-- Ketua Level -->
                <div v-if="groupedMembers.ketua.length" class="flex flex-col items-center">
                    <div class="flex gap-4">
                        <div v-for="member in groupedMembers.ketua" :key="member.id" class="w-64 bg-slate-800 dark:bg-slate-950 border border-slate-900 text-white rounded-2xl p-4 shadow-xl text-center z-10 relative">
                            <h3 class="font-bold text-lg mb-1">{{ member.name }}</h3>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest">{{ getRoleLabel(member) }}</p>
                        </div>
                    </div>
                    <!-- Connector line -->
                    <div class="w-px h-10 bg-slate-300 dark:bg-slate-700"></div>
                </div>

                <!-- Wakil Ketua Level -->
                <div v-if="groupedMembers.wakil.length" class="flex flex-col items-center">
                    <div class="flex gap-4">
                        <div v-for="member in groupedMembers.wakil" :key="member.id" class="w-64 bg-slate-600 dark:bg-slate-800 border border-slate-700 dark:border-slate-700 text-white rounded-2xl p-4 shadow-lg text-center z-10 relative">
                            <h3 class="font-bold text-lg mb-1">{{ member.name }}</h3>
                            <p class="text-xs font-semibold text-slate-300 uppercase tracking-widest">{{ getRoleLabel(member) }}</p>
                        </div>
                    </div>
                    <!-- Connector line -->
                    <div class="w-px h-10 bg-slate-300 dark:bg-slate-700"></div>
                </div>

                <!-- Horizontal Connector line before Sekretaris/Bendahara -->
                <div class="w-[600px] border-t-2 border-slate-300 dark:border-slate-700 h-10 flex justify-between relative mt-[-2px]">
                    <!-- Left drop line (Sekretaris) -->
                    <div class="w-px h-10 bg-slate-300 dark:bg-slate-700 absolute left-0"></div>
                    <!-- Middle drop line (Connecting to Divisi) -->
                    <div class="w-px h-10 bg-slate-300 dark:bg-slate-700 absolute left-1/2 -ml-px"></div>
                    <!-- Right drop line (Bendahara) -->
                    <div class="w-px h-10 bg-slate-300 dark:bg-slate-700 absolute right-0"></div>
                </div>

                <!-- Sekretaris & Bendahara Level -->
                <div class="w-full flex justify-center px-4 relative mt-[-1px] h-32">
                    <div class="w-[600px] flex justify-between relative h-full">
                        
                        <!-- Sekretaris -->
                        <div class="flex flex-col items-center w-56 -ml-28 absolute left-0">
                            <div v-for="member in groupedMembers.sekretaris" :key="member.id" class="w-full bg-blue-50 dark:bg-blue-900/30 border-2 border-blue-200 dark:border-blue-800 rounded-2xl p-4 shadow-sm text-center mb-4 z-10 relative">
                                <h3 class="font-bold text-slate-800 dark:text-slate-200 mb-1">{{ member.name }}</h3>
                                <p class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest">{{ getRoleLabel(member) }}</p>
                            </div>
                            <div v-if="groupedMembers.sekretaris.length === 0" class="w-full bg-slate-100 dark:bg-slate-800/50 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl p-4 text-center text-slate-400 text-sm z-10 relative">
                                (Belum ada Sekretaris)
                            </div>
                        </div>

                        <!-- Middle line continuing down to Divisi -->
                        <div class="w-px h-full bg-slate-300 dark:bg-slate-700 absolute left-1/2 -ml-px z-0"></div>

                        <!-- Bendahara -->
                        <div class="flex flex-col items-center w-56 -mr-28 absolute right-0">
                            <div v-for="member in groupedMembers.bendahara" :key="member.id" class="w-full bg-emerald-50 dark:bg-emerald-900/30 border-2 border-emerald-200 dark:border-emerald-800 rounded-2xl p-4 shadow-sm text-center mb-4 z-10 relative">
                                <h3 class="font-bold text-slate-800 dark:text-slate-200 mb-1">{{ member.name }}</h3>
                                <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">{{ getRoleLabel(member) }}</p>
                            </div>
                            <div v-if="groupedMembers.bendahara.length === 0" class="w-full bg-slate-100 dark:bg-slate-800/50 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl p-4 text-center text-slate-400 text-sm z-10 relative">
                                (Belum ada Bendahara)
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Connector line before Divisi -->
                <div class="w-px h-10 bg-slate-300 dark:bg-slate-700"></div>
                <div class="w-[60px] border-t-2 border-slate-300 dark:border-slate-700 mt-[-2px]"></div>

                <!-- Divisi-Divisi Level -->
                <div class="w-full max-w-4xl flex justify-center mt-8">
                    <div v-if="groupedMembers.divisions.length" class="grid grid-cols-2 md:grid-cols-3 gap-6 w-full justify-items-center">
                        <div v-for="member in groupedMembers.divisions" :key="member.id" class="w-56 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 shadow-md text-center hover:shadow-lg transition-shadow">
                            <h3 class="font-bold text-sm text-slate-800 dark:text-slate-200 mb-1">{{ member.name }}</h3>
                            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">{{ getRoleLabel(member) }}</p>
                        </div>
                    </div>
                    <div v-else class="w-72 bg-slate-100 dark:bg-slate-800/50 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl p-4 text-center text-slate-400 text-sm">
                        (Belum ada Divisi Lainnya)
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
