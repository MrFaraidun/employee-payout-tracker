<script setup>
import { computed } from 'vue';

const props = defineProps({
    value: {
        type: String,
        required: true,
    },
});

const styles = computed(() => {
    const val = String(props.value).toLowerCase();
    switch (val) {
        // Payout statuses & states
        case 'completed':
        case 'active':
            return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20';
        case 'pending':
        case 'processing':
            return 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20';
        case 'failed':
        case 'suspended':
        case 'inactive':
            return 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20';
        case 'archived':
            return 'bg-zinc-500/10 text-zinc-500 dark:text-zinc-400 border-zinc-500/20';
        // Roles
        case 'superadmin':
            return 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-500/20';
        case 'admin':
            return 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-500/20';
        case 'accountant':
            return 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20';
        default:
            return 'bg-zinc-100 dark:bg-zinc-800 text-[color:var(--text-secondary)] border-transparent';
    }
});
</script>

<template>
    <span
        class="inline-flex items-center px-2.5 py-0.5 text-[9px] font-sans font-semibold uppercase tracking-wider border rounded-full select-none"
        :class="styles"
    >
        <span 
            v-if="['active', 'completed', 'processing', 'pending'].includes(String(value).toLowerCase())"
            class="w-1 h-1 mr-1.5 rounded-full bg-current shrink-0"
        ></span>
        {{ value }}
    </span>
</template>
