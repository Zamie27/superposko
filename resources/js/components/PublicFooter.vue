<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    footerAbout: string;
    footerEmail: string;
    footerPhone: string;
    footerCopyright: string;
}>();

const formattedPhone = computed(() => {
    if (!props.footerPhone) return '';
    let clean = props.footerPhone.replace(/[^0-9]/g, '');
    if (clean.startsWith('62')) {
        clean = clean.substring(2);
    } else if (clean.startsWith('0')) {
        clean = clean.substring(1);
    }
    const part1 = clean.substring(0, 3);
    const part2 = clean.substring(3, 7);
    const part3 = clean.substring(7);
    let result = '+62';
    if (part1) result += ' ' + part1;
    if (part2) result += '-' + part2;
    if (part3) result += '-' + part3;
    return result;
});
</script>

<template>
    <footer class="bg-slate-900 py-16 text-slate-400 border-t border-slate-800/50 mt-auto">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-12 md:grid-cols-4">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 text-white">
                        <img src="/icon_superposko.png" alt="SuperPosko Icon" class="h-10 w-auto" />
                    </div>
                    <p class="mt-4 max-w-md text-sm leading-relaxed">
                        {{ footerAbout }}
                    </p>
                </div>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-white">Pintasan</h4>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        <li><Link href="/" class="hover:text-[#38BDF8] transition-colors">Home Page</Link></li>
                        <li><Link href="/about" class="hover:text-[#38BDF8] transition-colors">Tentang Kami</Link></li>
                        <li><Link href="/panduan" class="hover:text-[#38BDF8] transition-colors">Panduan</Link></li>
                        <li><Link href="/privacy" class="hover:text-[#38BDF8] transition-colors">Kebijakan Privasi</Link></li>
                        <li><Link href="/terms" class="hover:text-[#38BDF8] transition-colors">Syarat & Ketentuan</Link></li>
                        <li><Link href="/contact" class="hover:text-[#38BDF8] transition-colors">Kontak Support</Link></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-white">Hubungi Kami</h4>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        <li>Email: {{ footerEmail }}</li>
                        <li>Telepon: {{ formattedPhone }}</li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 border-t border-slate-800 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs">
                <p>
                    &copy; 2026 
                    <a href="https://kuukok.my.id" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors underline font-medium">{{ footerCopyright }}</a> 
                    - Solusi digital profesional. Hak Cipta Dilindungi.
                </p>
                <p>Dibuat dengan dedikasi untuk memajukan pengabdian masyarakat mahasiswa Indonesia.</p>
            </div>
        </div>
    </footer>
</template>
