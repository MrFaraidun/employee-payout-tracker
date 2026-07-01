<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import FormInput from '@/Components/FormInput.vue';

const form = useForm({
    name: '',
    organization_name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Register" />

        <form @submit.prevent="submit" class="space-y-4">
            <FormInput
                id="name"
                label="Full Name"
                v-model="form.name"
                :error="form.errors.name"
                required
                autofocus
                autocomplete="name"
            />

            <FormInput
                id="organization_name"
                label="Organization / Company Name"
                v-model="form.organization_name"
                :error="form.errors.organization_name"
                required
            />

            <FormInput
                id="email"
                type="email"
                label="Email Address"
                v-model="form.email"
                :error="form.errors.email"
                required
                autocomplete="username"
            />

            <FormInput
                id="password"
                type="password"
                label="Password"
                v-model="form.password"
                :error="form.errors.password"
                required
                autocomplete="new-password"
            />

            <FormInput
                id="password_confirmation"
                type="password"
                label="Confirm Password"
                v-model="form.password_confirmation"
                :error="form.errors.password_confirmation"
                required
                autocomplete="new-password"
            />

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full py-3 bg-[color:var(--primary)] hover:bg-transparent border border-transparent hover:border-[color:var(--primary)] text-[color:var(--primary-text)] hover:text-[color:var(--primary)] font-sans font-extrabold text-xs uppercase tracking-wider rounded-none transition-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed select-none shadow-none"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="font-mono">[PROCESSING...]</span>
                    <span v-else>Register</span>
                </button>
            </div>

            <div class="text-center">
                <Link
                    :href="route('login')"
                    class="text-[10px] font-sans font-bold uppercase tracking-wider text-[color:var(--text-muted)] hover:text-[color:var(--text-primary)] transition-none"
                >
                    Already registered? Log In
                </Link>
            </div>
        </form>
    </AuthLayout>
</template>
