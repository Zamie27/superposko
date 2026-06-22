<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { 
    CreditCard, Info, LayoutGrid, Wallet, BookOpen, Box, 
    Contact, Archive, Vote, Image, Users, CheckCircle as CheckCircle2, 
    ShoppingBag, Settings, Clock, Server, ClipboardList, Calendar, Briefcase
} from '@lucide/vue';
import { computed } from 'vue';
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
                        title: 'Manajemen Dokumentasi',
                        href: '/admin/documentation-configs',
                        icon: Server,
                    },
                    {
                        title: 'Laporan Masalah',
                        href: '/admin/reports',
                        icon: Info,
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

    if (!isSubscribed) {
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
            <NavFooter :items="footerNavItems" />

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
