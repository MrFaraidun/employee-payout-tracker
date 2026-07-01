<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message" class="mt-1">
        <p class="text-xs font-sans font-medium text-[color:var(--danger)]">
            {{ message }}
        </p>
    </div>
</template>
