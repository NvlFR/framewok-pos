<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { useSidebar } from '@/components/ui/sidebar';

const { state } = useSidebar();
const page = usePage();
const brand = page.props.axiom.brand;
</script>

<template>
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-md bg-primary text-white"
    >
        <AppLogoIcon class="size-5" />
    </div>
    <div v-show="state !== 'collapsed'" class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold line-clamp-1"
            >{{ brand.name }}</span
        >
        <span class="truncate text-xs font-normal text-muted-foreground">{{ brand.slogan }}</span>
    </div>
</template>
