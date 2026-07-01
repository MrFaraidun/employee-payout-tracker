<script setup>
defineProps({
    title: {
        type: String,
        default: "No records found",
    },
    description: {
        type: String,
        default:
            "Try adjusting your search or filters to find what you are looking for.",
    },
});
</script>

<template>
    <div
        class="flex flex-col items-center justify-center p-8 text-center bg-white border border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl dark:bg-zinc-900/30"
    >
        <svg
            class="w-8 h-8 mb-3 text-brand-text-muted"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="1.5"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 4v16m8-8H4"
            />
        </svg>
        <h3
            class="mb-1 font-sans text-xs font-bold tracking-wider uppercase text-brand-text"
        >
            {{ title }}
        </h3>
        <p
            class="text-[10px] font-sans text-brand-text-secondary max-w-xs uppercase leading-relaxed"
        >
            {{ description }}
        </p>
    </div>
</template>
