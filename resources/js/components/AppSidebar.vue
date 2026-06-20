<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { CreditCard, Info, LayoutGrid, Wallet, BookOpen, Box, Truck, Calendar, Contact, Archive, Vote, Image } from '@lucide/vue';
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

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Test Payment',
        href: '/payment/test',
        icon: CreditCard,
    },
    {
        title: 'Kas & Keuangan',
        href: '/finance',
        icon: Wallet,
    },
    {
        title: 'Logbook & Proker',
        href: '/logbook',
        icon: BookOpen,
    },
    {
        title: 'Manajemen',
        href: '#',
        icon: Box,
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
    },
    {
        title: 'Repository Proker',
        href: '/repository',
        icon: Archive,
    },
    {
        title: 'Voting & Aspirasi',
        href: '/voting',
        icon: Vote,
    },
    {
        title: 'Dokumentasi',
        href: '/documentation',
        icon: Image,
    },
];

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
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
