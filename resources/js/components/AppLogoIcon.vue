<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <img
        src="/icon_superposko.png"
        alt="SuperPosko"
        :class="className"
        v-bind="$attrs"
    />
</template>
