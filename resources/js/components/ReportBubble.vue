<script setup lang="ts">
import { usePage, useForm } from '@inertiajs/vue3';
import { Headphones, X, Image as ImageIcon, Send, AlertCircle, MessageCircle } from '@lucide/vue';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';

const page = usePage();
const user = computed(() => page.props.auth?.user as any);

// Check if user is logged in
const isLoggedIn = computed(() => !!user.value);

const isOpen = ref(false);
const showOptions = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);
const imagePreview = ref<string | null>(null);

const footerPhone = computed(() => (page.props.footer_phone as string) || '6285171739232');

const whatsappUrl = computed(() => {
    const cleanPhone = footerPhone.value.replace(/[^0-9]/g, '');

    return `https://wa.me/${cleanPhone}`;
});

const form = useForm({
    email: '',
    type: 'bug',
    title: '',
    description: '',
    screenshot: null as File | null,
});

const openModal = () => {
    if (!isLoggedIn.value) {
return;
}

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

    if (fileInput.value) {
fileInput.value.value = '';
}
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
        <!-- Backdrop to close options -->
        <div v-if="showOptions" @click="showOptions = false" class="fixed inset-0 z-[997] bg-transparent"></div>

        <!-- Options Menu -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 translate-y-4 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-4 scale-95"
        >
            <div v-if="showOptions" class="absolute bottom-18 right-0 w-64 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl p-2.5 z-[998] flex flex-col gap-1">
                <button
                    @click="() => { showOptions = false; openModal(); }"
                    class="w-full text-left px-3 py-2.5 text-sm font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/50 flex items-center gap-3 transition cursor-pointer border-0 bg-transparent"
                >
                    <span class="p-2 rounded-lg bg-sky-50 dark:bg-sky-950/50 text-sky-500 dark:text-sky-400">
                        <AlertCircle class="size-4.5" />
                    </span>
                    <div>
                        <div class="font-bold text-slate-800 dark:text-slate-200">Laporkan Masalah</div>
                        <div class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">Kirim keluhan/bug aplikasi</div>
                    </div>
                </button>
                <a
                    :href="whatsappUrl"
                    target="_blank"
                    @click="showOptions = false"
                    class="w-full text-left px-3 py-2.5 text-sm font-semibold rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/50 flex items-center gap-3 transition cursor-pointer no-underline bg-transparent"
                >
                    <span class="p-2 rounded-lg bg-green-50 dark:bg-green-950/50 text-green-500 dark:text-green-400">
                        <MessageCircle class="size-4.5" />
                    </span>
                    <div>
                        <div class="font-bold text-slate-800 dark:text-slate-200">Pusat Bantuan</div>
                        <div class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">Hubungi via WhatsApp</div>
                    </div>
                </a>
            </div>
        </Transition>

        <!-- Floating Button -->
        <button
            @click="showOptions = !showOptions"
            class="flex items-center justify-center size-14 rounded-full bg-sky-500 hover:bg-sky-600 text-white shadow-lg hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer border-0 relative z-[999]"
            title="Layanan Bantuan"
        >
            <X v-if="showOptions" class="size-6" />
            <Headphones v-else class="size-6" />
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
