<script setup>
defineProps({
    message: {
        type: String,
        required: false,
    },
});
</script>

<template>
    <div v-if="message" class="mt-1 text-[11px] font-mono text-rose-500 uppercase tracking-wide">
        {{ message }}
    </div>
</template>
