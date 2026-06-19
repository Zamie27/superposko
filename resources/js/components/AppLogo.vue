<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <!-- Full logo shown when expanded -->
    <img
        src="/logo_superposko.png"
        alt="SuperPosko"
        class="h-8 w-auto object-contain block group-data-[state=collapsed]:hidden"
    />

    <!-- Icon logo shown when collapsed -->
    <div
        class="hidden group-data-[state=collapsed]:flex aspect-square size-8 items-center justify-center rounded-md"
    >
        <AppLogoIcon class="size-8 object-contain" />
    </div>
</template>
