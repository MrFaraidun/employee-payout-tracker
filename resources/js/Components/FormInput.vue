<script setup>
import { ref } from 'vue';
import ErrorMessage from '@/Components/ErrorMessage.vue';

defineProps({
    modelValue: {
        type: [String, Number],
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
    type: {
        type: String,
        default: 'text',
    },
    placeholder: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);

const showPassword = ref(false);
</script>

<template>
    <div class="flex flex-col gap-1.5 w-full group">
        <!-- Label with refined style -->
        <label class="text-[10px] font-sans font-semibold uppercase tracking-wider text-brand-text-muted group-focus-within:text-brand-primary">
            {{ label }}
            <span v-if="required" class="text-red-500 ml-0.5">*</span>
        </label>
        
        <!-- Input wrapper -->
        <div class="relative w-full">
            <!-- Input element -->
            <input
                :type="type === 'password' ? (showPassword ? 'text' : 'password') : type"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                :placeholder="placeholder"
                class="w-full bg-brand-input text-brand-text border text-xs pl-3.5 py-2.5 rounded-xl placeholder-zinc-400 dark:placeholder-zinc-600 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 ease-out shadow-sm"
                :class="[
                    error ? 'border-red-500 focus:border-red-500 focus:ring-red-500/10' : 'border-zinc-200 dark:border-zinc-800 focus:border-emerald-500',
                    type === 'password' ? 'pr-10' : 'pr-3.5'
                ]"
            />

            <!-- Toggle Password Visibility Button -->
            <button
                v-if="type === 'password'"
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-zinc-400 hover:text-brand-text transition-colors duration-150 cursor-pointer focus:outline-none"
            >
                <!-- Eye Icon (Show) -->
                <svg v-if="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <!-- Eye Off Icon (Hide) -->
                <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
            </button>
        </div>
        
        <ErrorMessage :message="error" />
    </div>
</template>
