<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block text-[10px] font-sans font-semibold uppercase tracking-wider text-brand-text-muted mb-1.5">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
