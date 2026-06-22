<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { 
    CreditCard, Info, LayoutGrid, Wallet, BookOpen, Box, 
    Contact, Archive, Vote, Image, Users, CheckCircle as CheckCircle2, 
    ShoppingBag, Settings, Clock, Server, ClipboardList, Calendar, Briefcase, Download,
    Bell, Bug, FileText
} from '@lucide/vue';
import { computed, ref, onMounted, onUnmounted } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const page = usePage();
const { state } = useSidebar();

const formatBytes = (bytes: number, decimals = 1) => {
    if (!+bytes) {
        return '0 B';
    }

    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
};

interface NavGroup {
    title: string;
    items: NavItem[];
}

const navGroups = computed<NavGroup[]>(() => {
    const user = page.props.auth?.user as any;

    if (!user) {
        return [];
    }

    if (user.role === 'admin') {
        return [
            {
                title: 'Utama',
                items: [
                    {
                        title: 'Dashboard Admin',
                        href: '/admin/dashboard',
                        icon: LayoutGrid,
                    },
                    {
                        title: 'Log Aktifitas',
                        href: '/admin/activity-logs',
                        icon: Clock,
                    },
                    {
                        title: 'Pengaturan Website',
                        href: '/admin/settings',
                        icon: Settings,
                    },
                ],
            },
            {
                title: 'Manajemen Transaksi',
                items: [
                    {
                        title: 'Manajemen Harga',
                        href: '/admin/prices',
                        icon: CreditCard,
                    },
                    {
                        title: 'Manajemen Langganan',
                        href: '/admin/subscriptions',
                        icon: CheckCircle2,
                    },
                    {
                        title: 'Manajemen Preorder',
                        href: '/admin/preorders',
                        icon: ShoppingBag,
                    },
                    {
                        title: 'Manajemen Trial',
                        href: '/admin/trials',
                        icon: Clock,
                    },
                    {
                        title: 'Test Payment',
                        href: '/admin/payment/test',
                        icon: CreditCard,
                    },
                ],
            },
            {
                title: 'Manajemen Posko & Laporan',
                items: [
                    {
                        title: 'Manajemen User',
                        href: '/admin/users',
                        icon: Users,
                    },
                    {
                        title: 'Pusat Notifikasi',
                        href: '/admin/notifications',
                        icon: Bell,
                    },
                    {
                        title: 'Manajemen Dokumentasi',
                        href: '/admin/documentation-configs',
                        icon: Server,
                    },
                    {
                        title: 'Laporan Masalah',
                        href: '/admin/reports',
                        icon: Info,
                    },
                    {
                        title: 'Laporan Bug',
                        href: '/admin/bug-reports',
                        icon: Bug,
                    },
                ],
            },
        ];
    }

    const isSubscribed = user.is_subscribed;
    const preorderPromoActive = page.props.preorder_promo_active;

    const mainItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        },
    ];

    if (!isSubscribed || user.role === 'trial') {
        if (preorderPromoActive) {
            mainItems.push({
                title: 'Preorder!',
                href: '/preorder',
                icon: ShoppingBag,
            });
        } else {
            mainItems.push({
                title: 'Beli Langganan',
                href: '/payment',
                icon: CreditCard,
            });
        }
    }

    mainItems.push(
        {
            title: 'Kas & Keuangan',
            href: '/finance',
            icon: Wallet,
            locked: !isSubscribed,
        },
        {
            title: 'Logbook & Proker',
            href: '/logbook',
            icon: BookOpen,
            locked: !isSubscribed,
        }
    );

    return [
        {
            title: 'Menu Utama',
            items: mainItems,
        },
        {
            title: 'Manajemen Posko',
            items: [
                {
                    title: 'Inventaris',
                    href: '/management/inventory',
                    icon: Box,
                    locked: !isSubscribed,
                },
                {
                    title: 'Logistik',
                    href: '/management/logistic',
                    icon: ClipboardList,
                    locked: !isSubscribed,
                },
                {
                    title: 'Barang Pribadi',
                    href: '/personal-belongings',
                    icon: Briefcase,
                    locked: !isSubscribed,
                },
                {
                    title: 'Piket & Agenda',
                    href: '/management/schedule',
                    icon: Calendar,
                    locked: !isSubscribed,
                },
                {
                    title: 'Anggota',
                    href: '/management/members',
                    icon: Users,
                    locked: !isSubscribed || user.role === 'trial',
                },
                {
                    title: 'Log Aktifitas',
                    href: '/management/activity-logs',
                    icon: Clock,
                    locked: !isSubscribed,
                },
            ],
        },
        {
            title: 'Hubungan Masyarakat & Media',
            items: [
                {
                    title: 'Buku Kontak',
                    href: '/contacts',
                    icon: Contact,
                    locked: !isSubscribed,
                },
                {
                    title: 'Repository Proker',
                    href: '/repository',
                    icon: Archive,
                    locked: !isSubscribed,
                },
                {
                    title: 'Voting & Aspirasi',
                    href: '/voting',
                    icon: Vote,
                    locked: !isSubscribed,
                },
                {
                    title: 'Dokumentasi',
                    href: '/documentation',
                    icon: Image,
                    locked: !isSubscribed || user.role === 'trial',
                },
            ],
        },
    ];
});

const footerNavItems: NavItem[] = [
    {
        title: 'About Kuukok',
        href: 'https://kuukok.my.id',
        icon: Info,
    },
];

// PWA Installation prompt logic
const deferredPrompt = ref<any>(null);
const showInstallBtn = ref(false);

const handleBeforeInstallPrompt = (e: Event) => {
    e.preventDefault();
    deferredPrompt.value = e;
    showInstallBtn.value = true;
};

const handleAppInstalled = () => {
    deferredPrompt.value = null;
    showInstallBtn.value = false;
};

onMounted(() => {
    window.addEventListener('beforeinstallprompt', handleBeforeInstallPrompt);
    window.addEventListener('appinstalled', handleAppInstalled);

    if (window.matchMedia('(display-mode: standalone)').matches) {
        showInstallBtn.value = false;
    }
});

onUnmounted(() => {
    window.removeEventListener('beforeinstallprompt', handleBeforeInstallPrompt);
    window.removeEventListener('appinstalled', handleAppInstalled);
});

const installPwa = () => {
    if (!deferredPrompt.value) {
return;
}

    deferredPrompt.value.prompt();
    deferredPrompt.value.userChoice.then((choiceResult: { outcome: string }) => {
        if (choiceResult.outcome === 'accepted') {
            showInstallBtn.value = false;
        }

        deferredPrompt.value = null;
    });
};
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard().url">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain 
                v-for="group in navGroups" 
                :key="group.title" 
                :title="group.title" 
                :items="group.items" 
            />
        </SidebarContent>

        <SidebarFooter>
            <SidebarMenu v-if="showInstallBtn" class="px-2">
                <SidebarMenuItem>
                    <SidebarMenuButton
                        @click="installPwa"
                        class="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100 cursor-pointer"
                    >
                        <Download class="size-4" />
                        <span>Instal Aplikasi</span>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>

            <SidebarMenu class="px-2 mb-1">
                <SidebarMenuItem>
                    <SidebarMenuButton
                        as-child
                        class="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100 cursor-pointer"
                    >
                        <a href="https://pdf.kuukok.my.id/" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2">
                            <FileText class="size-4 text-neutral-500 shrink-0" />
                            <span>Tools Dokumen</span>
                        </a>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>

            <NavFooter :items="footerNavItems" class="pt-0" />

            <!-- Immich Storage Info -->
            <div v-if="page.props.immich && state !== 'collapsed'" class="mx-3 mb-2 p-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800 rounded-xl shadow-xs">
                <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 mb-0.5">Ruang penyimpanan</p>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-1.5">
                    {{ formatBytes(page.props.immich.quotaUsageInBytes) }} dari {{ formatBytes(page.props.immich.quotaSizeInBytes) }} digunakan
                </p>
                <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-[#38BDF8] h-1.5 rounded-full transition-all duration-500" :style="`width: ${Math.min((page.props.immich.quotaUsageInBytes / page.props.immich.quotaSizeInBytes) * 100, 100)}%`"></div>
                </div>
            </div>

            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
