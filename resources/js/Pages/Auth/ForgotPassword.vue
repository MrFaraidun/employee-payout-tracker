<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import FormInput from '@/Components/FormInput.vue';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout>
        <Head title="Reset Password" />

        <div class="mb-5 font-mono text-[11px] uppercase tracking-wide text-surface-400 leading-relaxed">
            Forgot your password? Enter your email address and we will email you a password reset link.
        </div>

        <div v-if="status" class="mb-4 font-mono text-[11px] uppercase tracking-wider text-emerald-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <FormInput
                id="email"
                type="email"
                label="Email Address"
                v-model="form.email"
                :error="form.errors.email"
                required
                autofocus
                autocomplete="username"
            />

            <div class="pt-2 flex items-center justify-between gap-4">
                <Link
                    :href="route('login')"
                    class="text-[10px] font-sans font-bold uppercase tracking-wider text-[color:var(--text-muted)] hover:text-[color:var(--text-primary)] transition-none"
                >
                    Back to login
                </Link>
                <button
                    type="submit"
                    class="py-3 px-4 bg-[color:var(--primary)] hover:bg-transparent border border-transparent hover:border-[color:var(--primary)] text-[color:var(--primary-text)] hover:text-[color:var(--primary)] font-sans font-extrabold text-xs uppercase tracking-wider rounded-none transition-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed select-none shadow-none"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="font-mono">[PROCESSING...]</span>
                    <span v-else>Send Reset Link</span>
                </button>
            </div>
        </form>
    </AuthLayout>
</template>
