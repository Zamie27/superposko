<script setup lang="ts">
import { ref, computed } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { Headphones, X, Image as ImageIcon, Send } from '@lucide/vue';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';

const page = usePage();
const user = computed(() => page.props.auth?.user as any);

// Check if user is logged in
const isLoggedIn = computed(() => !!user.value);

const isOpen = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);
const imagePreview = ref<string | null>(null);

const form = useForm({
    email: '',
    type: 'bug',
    title: '',
    description: '',
    screenshot: null as File | null,
});

const openModal = () => {
    if (!isLoggedIn.value) return;

    form.email = user.value.email;
    form.type = 'bug';
    form.title = '';
    form.description = '';
    form.screenshot = null;
    imagePreview.value = null;
    isOpen.value = true;
};

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        form.screenshot = file;

        // Show preview
        const reader = new FileReader();
        reader.onload = (event) => {
            imagePreview.value = event.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const triggerFileInput = () => {
    fileInput.value?.click();
};

const removeImage = () => {
    form.screenshot = null;
    imagePreview.value = null;
    if (fileInput.value) fileInput.value.value = '';
};

const submitReport = () => {
    form.post('/laporan/buat', {
        preserveScroll: true,
        onSuccess: () => {
            isOpen.value = false;
            form.reset();
            imagePreview.value = null;
        },
    });
};
</script>

<template>
    <!-- Only show for logged in users -->
    <div v-if="isLoggedIn" class="fixed bottom-6 right-6 z-[999] font-sans">
        <!-- Floating Button -->
        <button
            @click="openModal"
            class="flex items-center justify-center size-14 rounded-full bg-sky-500 hover:bg-sky-600 text-white shadow-lg hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer border-0"
            title="Laporkan Masalah"
        >
            <Headphones class="size-6" />
        </button>

        <!-- Report Modal -->
        <Dialog v-model:open="isOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader class="flex flex-row items-center gap-3">
                    <div class="p-2 bg-sky-50 text-sky-500 rounded-full">
                        <Headphones class="size-5 shrink-0" />
                    </div>
                    <div>
                        <DialogTitle class="text-lg font-bold text-slate-900 dark:text-slate-100">Laporkan Masalah</DialogTitle>
                    </div>
                </DialogHeader>

                <form @submit.prevent="submitReport" class="space-y-4 py-2">
                    <!-- Jenis Pengaduan -->
                    <div class="grid gap-2">
                        <label for="bubble_type" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Jenis Pengaduan</label>
                        <select
                            id="bubble_type"
                            v-model="form.type"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3.5 py-2.5 text-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:border-sky-500 focus:outline-none"
                            required
                        >
                            <option value="complaint">Keluhan / Pertanyaan Umum</option>
                            <option value="bug">Masalah Aplikasi (Bug / Error)</option>
                            <option value="security">Masalah Keamanan (Akun Kompromi)</option>
                        </select>
                        <span v-if="form.errors.type" class="text-xs text-red-500">{{ form.errors.type }}</span>
                    </div>

                    <!-- Judul Laporan -->
                    <div class="grid gap-2">
                        <label for="bubble_title" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Judul Laporan</label>
                        <Input
                            id="bubble_title"
                            type="text"
                            v-model="form.title"
                            required
                            placeholder="Contoh: Error saat simpan data"
                        />
                        <span v-if="form.errors.title" class="text-xs text-red-500">{{ form.errors.title }}</span>
                    </div>

                    <!-- Rincian Laporan -->
                    <div class="grid gap-2">
                        <label for="desc" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Rincian Laporan / Keluhan</label>
                        <textarea
                            id="desc"
                            v-model="form.description"
                            rows="4"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-800 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:outline-none bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200"
                            required
                            placeholder="Jelaskan masalah atau keluhan Anda secara rinci..."
                        ></textarea>
                        <span v-if="form.errors.description" class="text-xs text-red-500">{{ form.errors.description }}</span>
                    </div>

                    <!-- File Upload / Screenshot -->
                    <div class="grid gap-2">
                        <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Lampirkan Gambar/Screenshot (Opsional)</label>

                        <!-- Invisible input -->
                        <input
                            ref="fileInput"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="handleFileChange"
                        />

                        <!-- Upload Trigger / Preview Box -->
                        <div v-if="!imagePreview" @click="triggerFileInput" class="border border-dashed border-slate-200 dark:border-slate-800 hover:border-sky-500 rounded-xl p-4 flex flex-col items-center justify-center gap-1.5 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-900/50 transition">
                            <ImageIcon class="size-5 text-slate-400" />
                            <span class="text-xs text-slate-500 font-medium">Klik untuk upload gambar</span>
                            <span class="text-[10px] text-slate-400">Format JPG, PNG, GIF (Maks. 5MB)</span>
                        </div>

                        <!-- Image Preview -->
                        <div v-else class="relative rounded-xl overflow-hidden border border-slate-200 max-w-xs">
                            <img :src="imagePreview" class="max-h-32 w-full object-cover" />
                            <button
                                type="button"
                                @click="removeImage"
                                class="absolute top-1.5 right-1.5 p-1 bg-red-500 hover:bg-red-600 text-white rounded-full transition shadow cursor-pointer border-0"
                            >
                                <X class="size-3" />
                            </button>
                        </div>
                        <span v-if="form.errors.screenshot" class="text-xs text-red-500">{{ form.errors.screenshot }}</span>
                    </div>

                    <DialogFooter class="flex gap-2 sm:justify-end pt-2 border-t">
                        <Button
                            type="button"
                            variant="outline"
                            @click="isOpen = false"
                            class="cursor-pointer"
                        >
                            Batal
                        </Button>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-sky-500 hover:bg-sky-600 text-white font-bold cursor-pointer"
                            :class="{
                                'bg-sky-500 hover:bg-sky-600 text-white': true
                            }"
                        >
                            <Spinner v-if="form.processing" />
                            <Send class="size-4" /> Kirim Laporan
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
