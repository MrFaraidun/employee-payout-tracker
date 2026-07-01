<script setup>
defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Search...',
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="relative w-full sm:max-w-xs group">
        <!-- Search icon -->
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-text-muted group-focus-within:text-brand-primary transition-all duration-200">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <!-- Input element -->
        <input
            type="text"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="placeholder"
            class="block w-full pl-9 pr-3.5 py-2 bg-brand-input text-brand-text border text-xs rounded-xl placeholder-zinc-400 dark:placeholder-zinc-600 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 ease-out shadow-sm border-zinc-200 dark:border-zinc-800 focus:border-emerald-500"
        />
    </div>
</template>
