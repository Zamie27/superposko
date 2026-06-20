<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { 
    CreditCard, Info, LayoutGrid, Wallet, BookOpen, Box, 
    Contact, Archive, Vote, Image, Users, CheckCircle as CheckCircle2, 
    ShoppingBag, Settings 
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
            href: '/host/dashboard',
            icon: LayoutGrid,
        },
    ];

    const preorderPromoActive = page.props.preorder_promo_active;

    // If not subscribed, always show the Preorder or Payment link
    if (!isSubscribed) {
        if (preorderPromoActive) {
            items.push({
                title: 'Preorder!',
                href: '/user/preorder',
                icon: ShoppingBag,
            });
        } else {
            items.push({
                title: 'Bayar Langganan',
                href: '/host/payment/test',
                icon: CreditCard,
            });
        }
    }

    // Host menu items (marked as locked if the user is not subscribed)
    if (isSubscribed || preorderPromoActive) {
        items.push({
            title: 'Test Payment',
            href: '/host/payment/test',
            icon: CreditCard,
            locked: !isSubscribed,
        });
    }

    items.push(
        {
            title: 'Kas & Keuangan',
            href: '/host/finance',
            icon: Wallet,
            locked: !isSubscribed,
        },
        {
            title: 'Logbook & Proker',
            href: '/host/logbook',
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
                    href: '/host/management/inventory',
                },
                {
                    title: 'Logistik',
                    href: '/host/management/logistic',
                },
                {
                    title: 'Piket & Agenda',
                    href: '/host/management/schedule',
                },
                {
                    title: 'Anggota',
                    href: '/host/management/members',
                },
            ],
        },
        {
            title: 'Buku Kontak',
            href: '/host/contacts',
            icon: Contact,
            locked: !isSubscribed,
        },
        {
            title: 'Repository Proker',
            href: '/host/repository',
            icon: Archive,
            locked: !isSubscribed,
        },
        {
            title: 'Voting & Aspirasi',
            href: '/host/voting',
            icon: Vote,
            locked: !isSubscribed,
        },
        {
            title: 'Dokumentasi',
            href: '/host/documentation',
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
