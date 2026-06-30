<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true
    }
});

const config = computed(() => {
    switch (props.status) {
        case 'completed':
            return {
                label: 'Completed',
                dot: 'bg-emerald-400',
                text: 'text-emerald-400',
                bg: 'bg-emerald-400/10',
            };
        case 'processing':
            return {
                label: 'Processing',
                dot: 'bg-amber-400 animate-pulse',
                text: 'text-amber-400',
                bg: 'bg-amber-400/10',
            };
        case 'pending':
        default:
            return {
                label: 'Pending',
                dot: 'bg-surface-400',
                text: 'text-surface-400',
                bg: 'bg-surface-400/10',
            };
    }
});
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 text-[10px] font-medium tracking-wide px-2 py-0.5 select-none"
        :class="[config.bg, config.text]"
    >
        <span class="w-1 h-1" :class="config.dot"></span>
        {{ config.label }}
    </span>
</template>
