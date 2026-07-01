<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import FormInput from '@/Components/FormInput.vue';
import FormCheckbox from '@/Components/FormCheckbox.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Log In" />

        <div v-if="status" class="mb-5 text-xs font-sans uppercase tracking-wider text-brand-primary text-center">
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
                placeholder="name@company.com"
            />

            <FormInput
                id="password"
                type="password"
                label="Password"
                v-model="form.password"
                :error="form.errors.password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
            />

            <div class="flex items-center justify-between">
                <FormCheckbox label="Remember Me" v-model:modelValue="form.remember" />

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-[10px] font-sans font-bold uppercase tracking-wider text-slate-400 hover:text-white underline decoration-slate-700 hover:decoration-white transition-colors"
                >
                    Forgot Password?
                </Link>
            </div>

            <div class="pt-1">
                <button
                    type="submit"
                    class="w-full py-3 bg-[color:var(--primary)] hover:bg-transparent border border-transparent hover:border-[color:var(--primary)] text-[color:var(--primary-text)] hover:text-[color:var(--primary)] font-sans font-extrabold text-xs uppercase tracking-wider rounded-none transition-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed select-none shadow-none"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="font-mono">[PROCESSING...]</span>
                    <span v-else>Log In</span>
                </button>
            </div>
            
            <div class="mt-5 text-center">
                <Link
                    :href="route('register')"
                    class="text-[10px] font-sans font-bold uppercase tracking-wider text-[color:var(--text-muted)] hover:text-[color:var(--text-primary)] transition-none"
                >
                    Don't have an account? Register
                </Link>
            </div>
        </form>
    </AuthLayout>
</template>
