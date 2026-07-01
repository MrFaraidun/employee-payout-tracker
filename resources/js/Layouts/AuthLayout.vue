<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-brand-bg text-brand-text font-sans p-6 relative overflow-hidden select-none">
        
        <!-- System Logo & Branding Header -->
        <div class="mb-8 z-10 flex flex-col items-center gap-2.5 text-center">
            <Link href="/" class="group flex items-center gap-3">
                <div class="w-12 h-12 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center rounded-xl border border-emerald-500/20 transition-all duration-300 shadow-sm">
                    <span class="text-xl font-bold">$</span>
                </div>
            </Link>
            <h1 class="text-sm font-bold uppercase tracking-[0.2em] text-brand-text mt-3 font-sans">Payout.Log</h1>
            <p class="text-[9px] font-sans text-brand-text-secondary uppercase tracking-widest">Enterprise Ledger Infrastructure</p>
        </div>

        <!-- Premium Rounded Card Container -->
        <div class="w-full sm:max-w-[420px] p-6 sm:p-8 bg-brand-card-to border border-brand-glass rounded-2xl z-10 shadow-xl">
            <slot />
        </div>

        <!-- Mini Footer -->
        <div class="mt-8 z-10 text-[9px] font-sans uppercase tracking-wider text-brand-text-secondary">
            Secure Session Gateway
        </div>
    </div>
</template>
