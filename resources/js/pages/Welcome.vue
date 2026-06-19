<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login } from '@/routes';
import { register } from '@/routes';
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]"
    >
        <header
            class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl"
        >
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                    <Link
                        :href="register()"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>
        <div
            class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0"
        >
            <main
                class="flex w-full max-w-[335px] flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row"
            >
                <div
                    class="flex-1 rounded-br-lg rounded-bl-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:rounded-tl-lg lg:rounded-br-none lg:p-20 dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                >
                    <h1 class="mb-2 text-xl font-semibold text-neutral-900 dark:text-neutral-50">Selamat Datang di SuperPosko</h1>
                    <p class="mb-6 text-[14px] leading-relaxed text-neutral-500 dark:text-neutral-400">
                        Platform SaaS modular untuk mempermudah, merapikan, dan mendigitalisasi operasional serta administrasi kelompok KKN (Kuliah Kerja Nyata) secara transparan dan terstruktur.
                    </p>
                    <ul class="mb-6 flex flex-col gap-4">
                        <li class="flex items-start gap-3">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-600 dark:bg-sky-950 dark:text-sky-400 font-bold text-[11px]">1</span>
                            <div>
                                <h2 class="font-medium text-neutral-900 dark:text-neutral-50">E-Bendahara (Kas & Nota)</h2>
                                <p class="text-neutral-500 dark:text-neutral-400 text-xs">Pencatatan kas masuk/keluar secara transparan dengan bukti nota digital.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-600 dark:bg-sky-950 dark:text-sky-400 font-bold text-[11px]">2</span>
                            <div>
                                <h2 class="font-medium text-neutral-900 dark:text-neutral-50">Digital Logbook & Proker</h2>
                                <p class="text-neutral-500 dark:text-neutral-400 text-xs">Laporan harian mahasiswa dan manajemen visual program kerja posko.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-600 dark:bg-sky-950 dark:text-sky-400 font-bold text-[11px]">3</span>
                            <div>
                                <h2 class="font-medium text-neutral-900 dark:text-neutral-50">Logistik & Piket Harian</h2>
                                <p class="text-neutral-500 dark:text-neutral-400 text-xs">Pelacakan stok barang pinjaman warga serta pembagian jadwal piket.</p>
                            </div>
                        </li>
                    </ul>
                    <ul class="flex gap-3 text-sm leading-normal">
                        <li>
                            <Link
                                v-if="$page.props.auth.user"
                                :href="dashboard()"
                                class="inline-block rounded-md bg-sky-500 hover:bg-sky-600 px-5 py-2 text-sm leading-normal font-semibold text-white transition-colors duration-200 dark:bg-sky-600 dark:hover:bg-sky-700"
                            >
                                Buka Dashboard
                            </Link>
                            <Link
                                v-else
                                :href="register()"
                                class="inline-block rounded-md bg-sky-500 hover:bg-sky-600 px-5 py-2 text-sm leading-normal font-semibold text-white transition-colors duration-200 dark:bg-sky-600 dark:hover:bg-sky-700"
                            >
                                Daftar Kelompok KKN
                            </Link>
                        </li>
                    </ul>
                </div>
                <div
                    class="relative -mb-px aspect-[335/364] w-full shrink-0 overflow-hidden rounded-t-lg bg-sky-50 lg:mb-0 lg:-ml-px lg:aspect-auto lg:w-[438px] lg:rounded-t-none lg:rounded-r-lg dark:bg-slate-950"
                >
                    <!-- SuperPosko Logo -->
                    <div class="flex h-full w-full items-center justify-center p-8 bg-gradient-to-br from-sky-400/20 to-sky-600/30 dark:from-sky-950/40 dark:to-slate-900/60">
                        <img
                            class="w-4/5 max-w-[280px] object-contain drop-shadow-md select-none transition-transform duration-500 hover:scale-105 dark:invert dark:hue-rotate-180"
                            src="/logo_superposko.png"
                            alt="SuperPosko Logo"
                        />
                    </div>
                    <div
                        class="absolute inset-0 rounded-t-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:rounded-t-none lg:rounded-r-lg dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                    ></div>
                </div>
            </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
