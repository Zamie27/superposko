<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { 
    CreditCard, Info, LayoutGrid, Wallet, BookOpen, Box, 
    Contact, Archive, Vote, Image, Users, CheckCircle as CheckCircle2, 
    ShoppingBag, Settings, Clock, Server 
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
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const page = usePage();

const filteredNavItems = computed<NavItem[]>(() => {
    const user = page.props.auth?.user as any;
    if (!user) {
        return [];
    }

    if (user.role === 'admin') {
        return [
            {
                title: 'Dashboard Admin',
                href: '/admin/dashboard',
                icon: LayoutGrid,
            },
            {
                title: 'Manajemen User',
                href: '/admin/users',
                icon: Users,
            },
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
            {
                title: 'Laporan Masalah',
                href: '/admin/reports',
                icon: Info,
            },
            {
                title: 'Manajemen Dokumentasi',
                href: '/admin/documentation-configs',
                icon: Server,
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
        ];
    }

    const isSubscribed = user.is_subscribed;

    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        },
    ];

    const preorderPromoActive = page.props.preorder_promo_active;

    // If not subscribed, always show the Preorder or Payment link
    if (!isSubscribed) {
        if (preorderPromoActive) {
            items.push({
                title: 'Preorder!',
                href: '/preorder',
                icon: ShoppingBag,
            });
        } else {
            items.push({
                title: 'Beli Langganan',
                href: '/payment',
                icon: CreditCard,
            });
        }
    }

    items.push(
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
        },
        {
            title: 'Manajemen',
            href: '#',
            icon: Box,
            locked: !isSubscribed,
            items: [
                {
                    title: 'Inventaris',
                    href: '/management/inventory',
                },
                {
                    title: 'Logistik',
                    href: '/management/logistic',
                },
                {
                    title: 'Piket & Agenda',
                    href: '/management/schedule',
                },
                {
                    title: 'Anggota',
                    href: '/management/members',
                },
            ],
        },
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
            locked: !isSubscribed,
        }
    );

    return items;
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
            <NavMain :items="filteredNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
