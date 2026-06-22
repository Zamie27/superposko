<script setup lang="ts">
import { usePage, useForm } from '@inertiajs/vue3';
import { Bug, X, Camera, Send } from '@lucide/vue';
import { ref, computed } from 'vue';
import { Spinner } from '@/components/ui/spinner';

const page = usePage();
const user = computed(() => page.props.auth?.user as any);

const isOpen = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);
const imagePreviews = ref<string[]>([]);

const form = useForm({
    title: '',
    description: '',
    reporter_name: '',
    contact_info: '',
    screenshots: [] as File[],
});

// Pre-fill name if user is logged in
const toggleBubble = () => {
    if (isOpen.value) {
        isOpen.value = false;

        return;
    }

    form.reset();
    form.clearErrors();
    imagePreviews.value = [];

    if (user.value) {
        form.reporter_name = user.value.name || '';
        form.contact_info = user.value.email || '';
    }

    isOpen.value = true;
};

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;

    if (!target.files) {
return;
}

    const newFiles = Array.from(target.files);
    const totalAllowed = 5 - form.screenshots.length;
    const filesToAdd = newFiles.slice(0, totalAllowed);

    for (const file of filesToAdd) {
        form.screenshots.push(file);
        const reader = new FileReader();
        reader.onload = (event) => {
            imagePreviews.value.push(event.target?.result as string);
        };
        reader.readAsDataURL(file);
    }

    // Reset file input so same file can be re-selected
    if (fileInput.value) {
fileInput.value.value = '';
}
};

const removeImage = (index: number) => {
    form.screenshots.splice(index, 1);
    imagePreviews.value.splice(index, 1);
};

const triggerFileInput = () => {
    if (form.screenshots.length >= 5) {
return;
}

    fileInput.value?.click();
};

const submitBugReport = () => {
    form.post('/bug-report', {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            isOpen.value = false;
            form.reset();
            imagePreviews.value = [];
        },
    });
};
</script>

<template>
    <div class="fixed bottom-[5.5rem] right-6 z-[998] font-sans">
        <!-- Backdrop -->
        <div v-if="isOpen" @click="isOpen = false" class="fixed inset-0 z-[996] bg-black/20 backdrop-blur-[2px]"></div>

        <!-- Bug Report Modal Card -->
        <Transition
            enter-active-class="transition ease-out duration-250"
            enter-from-class="opacity-0 translate-y-6 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-6 scale-95"
        >
            <div
                v-if="isOpen"
                class="absolute bottom-18 right-0 w-[340px] sm:w-[380px] rounded-2xl bg-slate-900 border border-slate-700/50 shadow-2xl z-[997] overflow-hidden"
            >
                <!-- Header -->
                <div class="px-5 pt-5 pb-3 flex items-center gap-3">
                    <div class="p-2.5 bg-sky-500/15 rounded-xl">
                        <Bug class="size-5 text-sky-400" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white tracking-tight">LAPOR BUG</h3>
                        <p class="text-[10px] text-slate-400 font-medium uppercase tracking-widest">Bantu kami memperbaiki SuperPosko</p>
                    </div>
                </div>

                <!-- Form -->
                <form @submit.prevent="submitBugReport" class="px-5 pb-5 space-y-3.5 max-h-[65vh] overflow-y-auto">
                    <!-- Judul Bug -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Judul Bug</label>
                        <input
                            v-model="form.title"
                            type="text"
                            required
                            placeholder="Contoh: Grafik tidak muncul"
                            class="w-full rounded-xl border border-slate-700 bg-slate-800/70 px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500/30 transition"
                        />
                        <span v-if="form.errors.title" class="text-[10px] text-red-400">{{ form.errors.title }}</span>
                    </div>

                    <!-- Keterangan Bug -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Keterangan Bug</label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            required
                            placeholder="Jelaskan apa yang terjadi..."
                            class="w-full rounded-xl border border-slate-700 bg-slate-800/70 px-3.5 py-2.5 text-sm text-white placeholder-slate-500 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500/30 transition resize-none"
                        ></textarea>
                        <span v-if="form.errors.description" class="text-[10px] text-red-400">{{ form.errors.description }}</span>
                    </div>

                    <!-- Nama & Email/HP - Side by Side -->
                    <div class="grid grid-cols-2 gap-2.5">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Anda</label>
                            <input
                                v-model="form.reporter_name"
                                type="text"
                                required
                                placeholder="Nama"
                                class="w-full rounded-xl border border-slate-700 bg-slate-800/70 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500/30 transition"
                            />
                            <span v-if="form.errors.reporter_name" class="text-[10px] text-red-400">{{ form.errors.reporter_name }}</span>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email/No. HP</label>
                            <input
                                v-model="form.contact_info"
                                type="text"
                                placeholder="Untuk hadiah"
                                class="w-full rounded-xl border border-slate-700 bg-slate-800/70 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500/30 transition"
                            />
                            <span v-if="form.errors.contact_info" class="text-[10px] text-red-400">{{ form.errors.contact_info }}</span>
                        </div>
                    </div>

                    <!-- Screenshot Upload -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Screenshot Bug (Opsional, Max 5)</label>

                        <!-- Hidden file input -->
                        <input
                            ref="fileInput"
                            type="file"
                            accept="image/*"
                            multiple
                            class="hidden"
                            @change="handleFileChange"
                        />

                        <!-- Upload button -->
                        <button
                            type="button"
                            @click="triggerFileInput"
                            :disabled="form.screenshots.length >= 5"
                            class="w-full flex items-center gap-2.5 rounded-xl border border-slate-700 bg-slate-800/70 px-3.5 py-2.5 text-sm text-slate-400 hover:border-sky-500/50 hover:bg-slate-800 transition cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            <Camera class="size-4 text-sky-400" />
                            <span>Klik untuk upload (bisa banyak)</span>
                        </button>

                        <!-- Image previews -->
                        <div v-if="imagePreviews.length > 0" class="flex flex-wrap gap-2 mt-2">
                            <div
                                v-for="(preview, index) in imagePreviews"
                                :key="index"
                                class="relative group rounded-lg overflow-hidden w-14 h-14 border border-slate-700"
                            >
                                <img :src="preview" class="w-full h-full object-cover" />
                                <button
                                    type="button"
                                    @click="removeImage(index)"
                                    class="absolute inset-0 flex items-center justify-center bg-red-500/70 opacity-0 group-hover:opacity-100 transition cursor-pointer border-0"
                                >
                                    <X class="size-4 text-white" />
                                </button>
                            </div>
                        </div>
                        <span v-if="form.errors.screenshots" class="text-[10px] text-red-400">{{ form.errors.screenshots }}</span>
                    </div>

                    <!-- Info Text -->
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Jika ada hal lain yang ingin dilaporkan atau dikonsultasikan, silakan hubungi Instagram
                        <a href="https://instagram.com/kuukok.id" target="_blank" class="text-sky-400 hover:text-sky-300 font-semibold no-underline">@kuukok.id</a>
                        untuk informasi lebih lanjut.
                    </p>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full flex items-center justify-center gap-2 rounded-xl bg-sky-500 hover:bg-sky-600 active:bg-sky-700 py-3 text-sm font-bold text-white transition cursor-pointer border-0 disabled:opacity-60 disabled:cursor-not-allowed shadow-lg shadow-sky-500/20"
                    >
                        <Spinner v-if="form.processing" />
                        <Send v-else class="size-4" />
                        KIRIM LAPORAN
                    </button>
                </form>
            </div>
        </Transition>

        <!-- Floating Bug Button -->
        <button
            @click="toggleBubble"
            class="flex items-center justify-center size-14 rounded-full bg-sky-500 hover:bg-sky-600 text-white shadow-lg shadow-sky-500/30 hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer border-0 relative z-[998]"
            title="Laporkan Bug"
        >
            <X v-if="isOpen" class="size-6" />
            <Bug v-else class="size-6" />
        </button>
    </div>
</template>
