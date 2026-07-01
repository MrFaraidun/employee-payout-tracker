<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section class="max-w-2xl">
        <header class="mb-6">
            <h2 class="text-sm font-bold text-brand-text mb-1">
                Profile Information
            </h2>
            <p class="text-xs text-brand-text-muted">
                Update your account's profile details and email address.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="space-y-5"
        >
            <!-- Mock Avatar Upload Panel (Visual Only) -->
            <div class="flex items-center gap-5 p-4 bg-zinc-50/50 dark:bg-zinc-900/30 border border-zinc-150 dark:border-zinc-800/80 rounded-2xl mb-6">
                <div class="w-14 h-14 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/25 flex items-center justify-center font-bold text-lg uppercase shadow-sm select-none">
                    {{ user.name.slice(0, 2) }}
                </div>
                <div class="flex flex-col gap-1 text-left">
                    <span class="text-xs font-bold text-brand-text">Profile Picture</span>
                    <span class="text-[10px] text-brand-text-muted font-mono leading-none">Avatar generated automatically from user initials.</span>
                </div>
            </div>

            <!-- Name Input Field -->
            <div>
                <InputLabel for="name" value="Full Name" class="mb-1.5" />
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-text-muted">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </span>
                    <TextInput
                        id="name"
                        type="text"
                        class="pl-10 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="e.g. Aland Karwan"
                    />
                </div>
                <InputError class="mt-1.5" :message="form.errors.name" />
            </div>

            <!-- Email Input Field -->
            <div>
                <InputLabel for="email" value="Email Address" class="mb-1.5" />
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-text-muted">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </span>
                    <TextInput
                        id="email"
                        type="email"
                        class="pl-10 block w-full"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        placeholder="e.g. name@example.com"
                    />
                </div>
                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <!-- Unverified Email Alert Banner -->
            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl flex flex-col gap-2">
                <div class="flex items-center gap-2 text-amber-600 dark:text-amber-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="text-xs font-bold uppercase tracking-wider">Email Unverified</span>
                </div>
                <p class="text-xs text-brand-text-secondary leading-relaxed">
                    Your email address has not yet been verified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="text-emerald-600 dark:text-emerald-400 hover:underline font-semibold focus:outline-none transition duration-150 ml-1"
                    >
                        Resend Verification Email
                    </Link>
                </p>
                <div
                    v-show="status === 'verification-link-sent'"
                    class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-semibold"
                >
                    ✓ A new verification link has been sent to your email address.
                </div>
            </div>

            <!-- Action Save Button -->
            <div class="flex items-center gap-4 pt-2">
                <PrimaryButton :disabled="form.processing" class="min-w-[100px]">
                    <span v-if="form.processing" class="font-mono text-[10px]">[PROCESSING...]</span>
                    <span v-else>Save Info</span>
                </PrimaryButton>

                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 translate-x-2"
                    enter-to-class="opacity-100 translate-x-0"
                    leave-active-class="transition duration-150 ease-in"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Saved successfully.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
