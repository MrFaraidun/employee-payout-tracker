<script setup>
import ErrorMessage from '@/Components/ErrorMessage.vue';
import CustomDropdown from '@/Components/CustomDropdown.vue';

defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    label: {
        type: String,
        required: true,
    },
    options: {
        type: Array,
        required: true,
    },
    error: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
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
        
        <CustomDropdown
            :model-value="modelValue"
            @update:model-value="$emit('update:modelValue', $event)"
            :options="options"
            :error="!!error"
            placeholder="Select option..."
        />
        
        <ErrorMessage :message="error" />
    </div>
</template>
