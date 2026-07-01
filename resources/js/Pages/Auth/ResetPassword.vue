<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import FormInput from '@/Components/FormInput.vue';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Reset Password" />

        <form @submit.prevent="submit" class="space-y-4">
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

            <FormInput
                id="password"
                type="password"
                label="New Password"
                v-model="form.password"
                :error="form.errors.password"
                required
                autocomplete="new-password"
            />

            <FormInput
                id="password_confirmation"
                type="password"
                label="Confirm New Password"
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
                    <span v-else>Reset Password</span>
                </button>
            </div>
        </form>
    </AuthLayout>
</template>
