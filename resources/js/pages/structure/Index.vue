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

    return {
        dpl,
        ketua,
        wakil,
        sekretaris,
        bendahara,
    };
});

const groupedDivisions = computed(() => {
    const divs: Record<string, any[]> = {};
    props.members.forEach(m => {
        if (!['dpl', 'ketua', 'wakil', 'sekretaris', 'bendahara', 'user', 'admin'].includes(m.role)) {
            const label = getRoleLabel(m);
            if (!divs[label]) divs[label] = [];
            divs[label].push(m);
        }
    });
    return divs;
});

const orgChartRef = ref<HTMLElement | null>(null);
const isDownloading = ref(false);

const downloadImage = async () => {
    if (!orgChartRef.value) return;
    isDownloading.value = true;
    try {
        const canvas = await html2canvas(orgChartRef.value, {
            backgroundColor: document.documentElement.classList.contains('dark') ? '#0f172a' : '#f8fafc',
            scale: 2,
            useCORS: true,
        });
        const link = document.createElement('a');
        link.download = 'struktur-organisasi.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    } catch (error) {
        console.error('Gagal mengunduh gambar:', error);
        alert('Gagal mengunduh gambar struktur. Pastikan Anda telah menjalankan: npm install html2canvas');
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
            class="w-full overflow-x-auto py-12 bg-slate-50 dark:bg-slate-900/30 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-inner flex-1"
        >
            <div ref="orgChartRef" class="min-w-[900px] flex flex-col items-center pb-12 pt-8 px-12 bg-slate-50 dark:bg-slate-900 rounded-2xl">
                
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

                <!-- Level 3: Sekretaris, Wakil Ketua, Bendahara -->
                <div class="flex justify-center relative w-full pt-8">
                    <!-- Top connector drop line -->
                    <div class="absolute top-0 left-1/2 w-px h-8 bg-slate-300 dark:bg-slate-700 -ml-px -translate-y-full"></div>
                    
                    <div class="flex gap-8 relative w-full justify-center max-w-5xl">
                        <!-- Sekretaris -->
                        <div class="flex-1 flex flex-col items-center relative">
                            <div class="absolute top-0 w-1/2 right-0 h-px border-t-2 border-slate-300 dark:border-slate-700"></div>
                            <div class="absolute top-0 w-px h-8 bg-slate-300 dark:bg-slate-700"></div>
                            <div class="flex flex-col gap-4 mt-8 w-60 z-10">
                                <div v-for="member in groupedMembers.sekretaris" :key="member.id" class="w-full bg-blue-50 dark:bg-blue-900/30 border-2 border-blue-200 dark:border-blue-800 rounded-2xl p-4 shadow-sm text-center">
                                    <h3 class="font-bold text-slate-800 dark:text-slate-200 mb-1">{{ member.name }}</h3>
                                    <p class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest">{{ getRoleLabel(member) }}</p>
                                </div>
                                <div v-if="groupedMembers.sekretaris.length === 0" class="w-full bg-slate-100 dark:bg-slate-800/50 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl p-4 text-center text-slate-400 text-sm">
                                    (Belum ada Sekretaris)
                                </div>
                            </div>
                        </div>
                        
                        <!-- Wakil Ketua -->
                        <div class="flex-1 flex flex-col items-center relative">
                            <div class="absolute top-0 w-full h-px border-t-2 border-slate-300 dark:border-slate-700"></div>
                            <div class="absolute top-0 w-px h-8 bg-slate-300 dark:bg-slate-700"></div>
                            <div class="flex flex-col gap-4 mt-8 w-64 z-10 bg-slate-50 dark:bg-slate-900/30 rounded-3xl pb-2 shadow-[0_0_15px_10px_rgba(248,250,252,1)] dark:shadow-[0_0_15px_10px_rgba(15,23,42,0.3)]">
                                <div v-for="member in groupedMembers.wakil" :key="member.id" class="w-full bg-slate-600 dark:bg-slate-800 border border-slate-700 dark:border-slate-700 text-white rounded-2xl p-4 shadow-lg text-center">
                                    <h3 class="font-bold text-lg mb-1">{{ member.name }}</h3>
                                    <p class="text-xs font-semibold text-slate-300 uppercase tracking-widest">{{ getRoleLabel(member) }}</p>
                                </div>
                                <div v-if="groupedMembers.wakil.length === 0" class="w-full bg-slate-100 dark:bg-slate-800/50 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl p-4 text-center text-slate-400 text-sm">
                                    (Belum ada Wakil Ketua)
                                </div>
                            </div>
                        </div>

                        <!-- Bendahara -->
                        <div class="flex-1 flex flex-col items-center relative">
                            <div class="absolute top-0 w-1/2 left-0 h-px border-t-2 border-slate-300 dark:border-slate-700"></div>
                            <div class="absolute top-0 w-px h-8 bg-slate-300 dark:bg-slate-700"></div>
                            <div class="flex flex-col gap-4 mt-8 w-60 z-10">
                                <div v-for="member in groupedMembers.bendahara" :key="member.id" class="w-full bg-emerald-50 dark:bg-emerald-900/30 border-2 border-emerald-200 dark:border-emerald-800 rounded-2xl p-4 shadow-sm text-center">
                                    <h3 class="font-bold text-slate-800 dark:text-slate-200 mb-1">{{ member.name }}</h3>
                                    <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">{{ getRoleLabel(member) }}</p>
                                </div>
                                <div v-if="groupedMembers.bendahara.length === 0" class="w-full bg-slate-100 dark:bg-slate-800/50 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl p-4 text-center text-slate-400 text-sm">
                                    (Belum ada Bendahara)
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- The Continuous Center Line from top to bottom of Level 3 -->
                    <div class="absolute top-0 left-1/2 w-px h-[calc(100%+3rem)] bg-slate-300 dark:bg-slate-700 -ml-px z-0"></div>
                </div>

                <!-- Spacer to accommodate the continuous center line -->
                <div class="h-12 w-full"></div>

                <!-- Level 4 Container (Divisions) -->
                <div class="flex justify-center relative w-full pt-0">
                    <div class="flex gap-4 sm:gap-6 relative w-full justify-center max-w-6xl">
                        <template v-if="Object.keys(groupedDivisions).length > 0">
                            <div v-for="(members, divName, index) in groupedDivisions" :key="divName" class="flex-1 flex flex-col items-center relative max-w-[220px]">
                                <!-- Horizontal branch line -->
                                <div class="absolute top-0 h-px border-t-2 border-slate-300 dark:border-slate-700 w-full"
                                    :class="{
                                        'w-1/2 right-0 left-auto': index === 0 && Object.keys(groupedDivisions).length > 1,
                                        'w-1/2 left-0 right-auto': index === Object.keys(groupedDivisions).length - 1 && Object.keys(groupedDivisions).length > 1,
                                        'w-full': index > 0 && index < Object.keys(groupedDivisions).length - 1,
                                        'hidden': Object.keys(groupedDivisions).length === 1
                                    }"
                                ></div>
                                <!-- Vertical drop line -->
                                <div class="absolute top-0 w-px h-8 bg-slate-300 dark:bg-slate-700"></div>
                                
                                <div class="flex flex-col gap-4 w-full px-2 mt-8 z-10">
                                    <!-- Cards stacked vertically per division -->
                                    <div v-for="member in members" :key="member.id" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 shadow-sm text-center hover:shadow-md transition-shadow">
                                        <h3 class="font-bold text-sm text-slate-800 dark:text-slate-200 mb-1">{{ member.name }}</h3>
                                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">{{ getRoleLabel(member) }}</p>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div v-else class="text-sm text-slate-400 p-8 border-2 border-dashed border-slate-200 rounded-xl bg-white dark:bg-slate-800 z-10">
                            (Belum ada Divisi Lainnya)
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
