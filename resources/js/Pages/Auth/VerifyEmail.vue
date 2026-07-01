<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const props = defineProps({
    status: {
        type: String,
    },
});

const page = usePage();
const user = page.props.auth.user;

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <AuthLayout>
        <Head title="Verify Email" />

        <div class="mb-5 font-sans text-xs text-slate-400 leading-relaxed">
            Thanks for signing up! Please verify your email address by clicking on the link we just sent to:
            
            <!-- Displaying the target email address clearly -->
            <div class="mt-3 text-slate-200 font-sans font-bold text-xs bg-white/[0.02] border border-brand-glass rounded-lg px-3 py-2 text-center select-all select-text cursor-text">
                {{ user.email }}
            </div>
        </div>

        <div
            class="mb-5 font-sans text-xs font-bold text-brand-primary"
            v-if="verificationLinkSent"
        >
            A new verification link has been sent to the email address above.
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-4 font-sans text-xs">
            <button
                type="submit"
                class="w-full py-2.5 bg-[color:var(--primary)] hover:bg-transparent border border-transparent hover:border-[color:var(--primary)] text-[color:var(--primary-text)] hover:text-[color:var(--primary)] font-sans font-extrabold text-xs uppercase tracking-wider rounded-none transition-none cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed select-none shadow-none"
                :disabled="form.processing"
            >
                <span v-if="form.processing" class="font-mono">[PROCESSING...]</span>
                <span v-else>Resend Verification Email</span>
            </button>

            <div class="text-center mt-3">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-[10px] font-sans font-bold uppercase tracking-wider text-[color:var(--text-muted)] hover:text-[color:var(--text-primary)] transition-none cursor-pointer"
                >
                    Log Out
                </Link>
            </div>
        </form>
    </AuthLayout>
</template>
