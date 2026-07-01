<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';

defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    label: {
        type: String,
        required: true,
    },
    error: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
    rows: {
        type: Number,
        default: 3,
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="flex flex-col gap-1.5 w-full group">
        <!-- Label with refined style -->
        <label class="text-[10px] font-sans font-semibold uppercase tracking-wider text-brand-text-muted group-focus-within:text-brand-primary">
            {{ label }}
            <span v-if="required" class="text-red-500 ml-0.5">*</span>
        </label>
        
        <!-- Textarea -->
        <textarea
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="placeholder"
            :rows="rows"
            class="w-full bg-brand-input text-brand-text border text-xs px-3.5 py-2.5 rounded-xl placeholder-zinc-400 dark:placeholder-zinc-600 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 ease-out shadow-sm resize-none"
            :class="error ? 'border-red-500 focus:border-red-500 focus:ring-red-500/10' : 'border-zinc-200 dark:border-zinc-800 focus:border-emerald-500'"
        ></textarea>
        <ErrorMessage :message="error" />
    </div>
</template>
