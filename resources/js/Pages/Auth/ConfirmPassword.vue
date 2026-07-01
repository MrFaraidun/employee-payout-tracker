<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import FormInput from '@/Components/FormInput.vue';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Confirm Password" />

        <div class="mb-5 font-mono text-[11px] uppercase tracking-wide text-surface-400 leading-relaxed">
            This is a secure area of the application. Please confirm your password before continuing.
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <FormInput
                id="password"
                type="password"
                label="Password"
                v-model="form.password"
                :error="form.errors.password"
                required
                autocomplete="current-password"
                autofocus
            />

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full py-3 bg-[color:var(--primary)] hover:bg-transparent border border-transparent hover:border-[color:var(--primary)] text-[color:var(--primary-text)] hover:text-[color:var(--primary)] font-sans font-extrabold text-xs uppercase tracking-wider rounded-none transition-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed select-none shadow-none"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="font-mono">[PROCESSING...]</span>
                    <span v-else>Confirm</span>
                </button>
            </div>
        </form>
    </AuthLayout>
</template>
