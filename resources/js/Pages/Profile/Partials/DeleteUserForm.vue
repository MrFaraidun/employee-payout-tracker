<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="max-w-2xl space-y-6">
        <header>
            <h2 class="text-sm font-bold text-brand-text mb-1">
                Delete Account
            </h2>
            <p class="text-xs text-brand-text-muted">
                Permanently delete your account and all associated data.
            </p>
        </header>

        <!-- Warning Callout Box -->
        <div class="p-5 bg-red-500/5 dark:bg-red-500/10 border border-red-500/20 rounded-2xl flex gap-4 items-start">
            <div class="p-2 bg-red-500/10 text-red-650 dark:text-red-400 rounded-xl shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="space-y-2 text-left">
                <h4 class="text-xs font-bold text-red-650 dark:text-red-400 uppercase tracking-wide">Caution: Irreversible Action</h4>
                <p class="text-xs text-brand-text-secondary leading-relaxed">
                    Once your account is deleted, all of its resources, active configurations, and system access rights will be permanently wiped. You will not be able to log in or recover any information.
                </p>
            </div>
        </div>

        <div class="pt-2">
            <DangerButton @click="confirmUserDeletion">Delete Account</DangerButton>
        </div>

        <!-- Deletion Confirmation Modal -->
        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-4 pb-2 border-b border-zinc-100 dark:border-zinc-800/80">
                    <div class="p-1.5 bg-red-500/10 text-red-600 rounded-lg shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h2 class="text-xs font-sans font-bold uppercase tracking-wider text-brand-text">
                        Confirm Account Deletion
                    </h2>
                </div>

                <p class="text-xs text-brand-text-secondary leading-relaxed mb-5">
                    Are you absolutely sure you want to delete your account? Please enter your account password below to confirm you would like to permanently remove all record assets.
                </p>

                <div class="space-y-1.5">
                    <InputLabel for="password" value="Your Password" />
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-text-muted">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <TextInput
                            id="password"
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="pl-10 block w-full"
                            placeholder="Enter password to confirm"
                            @keyup.enter="deleteUser"
                        />
                    </div>
                    <InputError :message="form.errors.password" class="mt-1.5" />
                </div>

                <div class="mt-8 flex justify-end gap-3 font-sans text-xs font-semibold uppercase tracking-wider border-t border-zinc-150 dark:border-zinc-800/80 pt-4">
                    <SecondaryButton @click="closeModal" class="px-5 py-2.5">
                        Cancel
                    </SecondaryButton>

                    <DangerButton
                        :disabled="form.processing"
                        @click="deleteUser"
                        class="px-5 py-2.5"
                    >
                        <span v-if="form.processing" class="font-mono text-[10px]">[PROCESSING...]</span>
                        <span v-else>Delete Account</span>
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
