<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div v-if="links && links.length > 3" class="flex items-center gap-1.5 font-sans text-xs select-none">
        <template v-for="(link, key) in links" :key="key">
            <!-- Disabled/Inactive navigation states -->
            <div
                v-if="link.url === null"
                class="px-3.5 py-2 bg-transparent border border-zinc-200 dark:border-zinc-800 text-brand-text-muted opacity-30 rounded-xl cursor-not-allowed"
                v-html="link.label"
            ></div>
            
            <!-- Active links with tactile hover states -->
            <Link
                v-else
                :href="link.url"
                class="px-3.5 py-2 border rounded-xl transition-all duration-200 ease-out"
                :class="link.active 
                    ? 'bg-brand-primary border-brand-primary text-white font-bold shadow-sm' 
                    : 'bg-brand-input border-zinc-200 dark:border-zinc-800 text-brand-text-secondary hover:border-brand-primary hover:text-brand-primary'"
            >
                <span v-html="link.label"></span>
            </Link>
        </template>
    </div>
</template>
