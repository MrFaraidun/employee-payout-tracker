<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="w-full bg-brand-input text-brand-text border border-zinc-200 dark:border-zinc-800 text-xs px-3.5 py-2.5 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 ease-out shadow-sm focus:border-emerald-500 placeholder-zinc-400 dark:placeholder-zinc-600"
        v-model="model"
        ref="input"
    />
</template>
