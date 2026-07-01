<script setup>
import Modal from '@/Components/Modal.vue';

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Are you sure?',
    },
    message: {
        type: String,
        default: 'This action is permanent and cannot be undone.',
    },
    confirmText: {
        type: String,
        default: 'Confirm',
    },
    cancelText: {
        type: String,
        default: 'Cancel',
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['confirm', 'close']);
</script>

<template>
    <Modal :show="show" max-width="md" @close="$emit('close')">
        <!-- Premium Rounded Card Container -->
        <div class="p-6 bg-brand-surface border border-zinc-200 dark:border-zinc-800 rounded-2xl">
            <h2 class="text-xs font-sans font-bold uppercase tracking-wider text-brand-text mb-2">
                {{ title }}
            </h2>
            
            <p class="text-xs text-brand-text-secondary font-sans leading-relaxed mb-6 mt-3">
                {{ message }}
            </p>
            
            <div class="flex items-center justify-end gap-3 font-sans text-xs font-semibold uppercase tracking-wider border-t border-zinc-200 dark:border-zinc-800 pt-4">
                <button
                    type="button"
                    @click="$emit('close')"
                    :disabled="processing"
                    class="px-4 py-2.5 bg-brand-input hover:bg-brand-text text-brand-text-secondary hover:text-white border border-zinc-200 dark:border-zinc-800 rounded-xl transition-all duration-150 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed select-none"
                >
                    {{ cancelText }}
                </button>
                
                <button
                    type="button"
                    @click="$emit('confirm')"
                    :disabled="processing"
                    class="px-4 py-2.5 bg-red-600 hover:opacity-90 text-white rounded-xl transition-all duration-150 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed select-none shadow-sm"
                >
                    <span v-if="processing" class="font-mono">[PROCESSING...]</span>
                    <span v-else>{{ confirmText }}</span>
                </button>
            </div>
        </div>
    </Modal>
</template>
