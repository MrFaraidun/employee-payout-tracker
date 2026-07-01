<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const page = usePage();
const user = page.props.auth.user;

const activeTab = ref('profile'); // 'profile', 'password', 'delete'
</script>

<template>
    <Head title="Profile Settings" />

    <AppLayout>
        <template #header>User Profile Settings</template>

        <div class="max-w-[1200px] mx-auto py-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                
                <!-- Left Side: User Hero & Navigation -->
                <div class="md:col-span-4 space-y-6">
                    <!-- User Card (Double-Bezel Nested Architecture) -->
                    <div class="p-1 bg-zinc-200/40 dark:bg-zinc-800/30 rounded-3xl border border-zinc-200/80 dark:border-zinc-800/60 shadow-sm">
                        <div class="p-6 bg-brand-surface rounded-[calc(1.5rem-1px)] flex flex-col items-center text-center">
                            <!-- Avatar Circle with Glowing Border -->
                            <div class="w-20 h-20 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/25 flex items-center justify-center font-bold text-2xl uppercase shadow-md select-none relative group overflow-hidden mb-4 transition-all duration-300 hover:scale-[1.03]">
                                <span class="z-10">{{ user.name.slice(0, 2) }}</span>
                                <div class="absolute inset-0 bg-emerald-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>
                            
                            <h3 class="text-xs font-bold text-brand-text">{{ user.name }}</h3>
                            <span class="px-2.5 py-0.5 mt-2 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded-full text-[9px] font-sans font-bold uppercase tracking-wider">
                                {{ user.role }}
                            </span>
                            
                            <p class="text-[10px] text-brand-text-muted mt-3.5 font-mono truncate max-w-full border-t border-zinc-100 dark:border-zinc-800/60 pt-3 w-full">
                                {{ user.email }}
                            </p>
                        </div>
                    </div>

                    <!-- Navigation Tabs Menu -->
                    <div class="p-1 bg-zinc-200/40 dark:bg-zinc-800/30 rounded-3xl border border-zinc-200/80 dark:border-zinc-800/60 shadow-sm">
                        <div class="p-2 bg-brand-surface rounded-[calc(1.5rem-1px)] flex flex-col gap-1">
                            <!-- Profile Info Tab -->
                            <button
                                @click="activeTab = 'profile'"
                                class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-semibold tracking-wide text-left transition-all duration-200 cursor-pointer focus:outline-none"
                                :class="[
                                    activeTab === 'profile'
                                        ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                                        : 'text-brand-text-secondary hover:bg-zinc-50 dark:hover:bg-zinc-900/50 hover:text-brand-text'
                                ]"
                            >
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Profile Info</span>
                            </button>

                            <!-- Change Password Tab -->
                            <button
                                @click="activeTab = 'password'"
                                class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-semibold tracking-wide text-left transition-all duration-200 cursor-pointer focus:outline-none"
                                :class="[
                                    activeTab === 'password'
                                        ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                                        : 'text-brand-text-secondary hover:bg-zinc-50 dark:hover:bg-zinc-900/50 hover:text-brand-text'
                                ]"
                            >
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>Change Password</span>
                            </button>

                            <!-- Danger Zone Tab -->
                            <button
                                @click="activeTab = 'delete'"
                                class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-semibold tracking-wide text-left transition-all duration-200 cursor-pointer focus:outline-none"
                                :class="[
                                    activeTab === 'delete'
                                        ? 'bg-red-500/10 text-red-600 dark:text-red-400'
                                        : 'text-brand-text-secondary hover:bg-red-500/5 hover:text-red-600'
                                ]"
                            >
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Danger Zone</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Content Area (Double-Bezel Card with Transitions) -->
                <div class="md:col-span-8">
                    <Transition
                        mode="out-in"
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0 translate-y-3"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="opacity-100 translate-y-0"
                        leave-to-class="opacity-0 -translate-y-3"
                    >
                        <div :key="activeTab" class="p-1 bg-zinc-200/40 dark:bg-zinc-800/30 rounded-3xl border border-zinc-200/80 dark:border-zinc-800/60 shadow-sm">
                            <div class="p-6 sm:p-8 bg-brand-surface rounded-[calc(1.5rem-1px)]">
                                <!-- Update Profile Info -->
                                <UpdateProfileInformationForm
                                    v-if="activeTab === 'profile'"
                                    :must-verify-email="mustVerifyEmail"
                                    :status="status"
                                />

                                <!-- Update Password -->
                                <UpdatePasswordForm
                                    v-else-if="activeTab === 'password'"
                                />

                                <!-- Delete User Account -->
                                <DeleteUserForm
                                    v-else-if="activeTab === 'delete'"
                                />
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
