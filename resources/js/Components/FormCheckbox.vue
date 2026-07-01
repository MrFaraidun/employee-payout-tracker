<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';

defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    label: {
        type: String,
        required: true,
    },
    error: {
        type: String,
        default: '',
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="flex flex-col gap-1 w-full group">
        <label class="flex items-center gap-2.5 cursor-pointer select-none py-1">
            <div class="relative flex items-center justify-center">
                <input
                    type="checkbox"
                    :checked="modelValue"
                    @change="$emit('update:modelValue', $event.target.checked)"
                    class="sr-only peer"
                />
                <!-- Custom checkbox mock -->
                <div class="w-4 h-4 bg-brand-input border border-zinc-200 dark:border-zinc-800 rounded-md flex items-center justify-center peer-checked:bg-brand-primary peer-checked:border-brand-primary transition-all duration-200 ease-out shadow-sm">
                    <svg class="w-2.5 h-2.5 text-white dark:text-zinc-950 transform scale-0 peer-checked:scale-100 transition-all duration-200 ease-out" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <span class="text-xs font-sans font-medium text-brand-text-secondary group-hover:text-brand-text transition-all duration-200 ease-out">{{ label }}</span>
        </label>
        <ErrorMessage :message="error" />
    </div>
</template>
