<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    options: {
        type: Array,
        required: true,
    },
    placeholder: {
        type: String,
        default: 'Select option...',
    },
    error: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const dropdownRef = ref(null);

const parsedOptions = computed(() => {
    return props.options.map(option => {
        if (typeof option === 'object' && option !== null) {
            return {
                value: option.value !== undefined ? option.value : option,
                label: option.label !== undefined ? option.label : option
            };
        }
        return { value: option, label: option };
    });
});

const selectedLabel = computed(() => {
    const selected = parsedOptions.value.find(opt => String(opt.value) === String(props.modelValue));
    return selected ? selected.label : props.placeholder;
});

const selectOption = (value) => {
    emit('update:modelValue', value);
    isOpen.value = false;
};

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div ref="dropdownRef" class="relative w-full">
        <!-- Trigger Button -->
        <button
            type="button"
            @click="isOpen = !isOpen"
            class="w-full bg-brand-input text-brand-text border text-left text-xs px-3.5 py-2.5 pr-10 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all duration-200 ease-out shadow-sm cursor-pointer flex items-center justify-between select-none"
            :class="error ? 'border-red-500 focus:ring-red-500/10' : 'border-zinc-200 dark:border-zinc-800'"
        >
            <span :class="{ 'text-zinc-400 dark:text-zinc-600': modelValue === '' }">
                {{ selectedLabel }}
            </span>
            
            <svg
                class="w-4 h-4 text-brand-text-muted transition-transform duration-200 ease-out shrink-0"
                :class="{ 'rotate-180': isOpen }"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Dropdown Menu List -->
        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-show="isOpen"
                class="absolute left-0 mt-1.5 w-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl dark:shadow-black/50 z-50 py-1.5 max-h-60 overflow-y-auto font-sans focus:outline-none"
            >
                <div
                    v-for="opt in parsedOptions"
                    :key="opt.value"
                    @click="selectOption(opt.value)"
                    class="px-3.5 py-2 text-xs text-brand-text-secondary cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/40 hover:text-brand-text transition-all duration-150 rounded-lg mx-1 flex items-center justify-between"
                    :class="[
                        String(opt.value) === String(modelValue)
                            ? 'bg-emerald-50/50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold hover:bg-emerald-50 dark:hover:bg-emerald-500/15'
                            : ''
                    ]"
                >
                    <span>{{ opt.label }}</span>
                    <svg
                        v-if="String(opt.value) === String(modelValue)"
                        class="w-4 h-4 text-emerald-500 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </Transition>
    </div>
</template>
