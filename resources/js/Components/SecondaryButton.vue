<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center rounded-xl border border-zinc-200 dark:border-zinc-800 bg-brand-input px-5 py-2.5 text-xs font-sans font-semibold tracking-wide text-brand-text transition-all duration-200 ease-out hover:bg-zinc-50 dark:hover:bg-zinc-800 active:scale-[0.98] focus:outline-none disabled:opacity-50 cursor-pointer select-none shadow-sm"
    >
        <slot />
    </button>
</template>
