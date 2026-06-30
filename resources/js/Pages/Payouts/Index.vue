<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import { useToast } from 'vue-toast-notification';
import PayoutForm from '@/Components/Payouts/PayoutForm.vue';
import PayoutTable from '@/Components/Payouts/PayoutTable.vue';

const props = defineProps({
    payouts: { type: Array, required: true },
    employees: { type: Array, required: true },
});

const theme = ref('dark');
const showForm = ref(false);
const editingPayout = ref(null);
const $toast = useToast();

onMounted(() => {
    if (localStorage.theme === 'light') {
        theme.value = 'light';
        document.documentElement.classList.remove('dark');
    } else {
        theme.value = 'dark';
        document.documentElement.classList.add('dark');
    }
});

const toggleTheme = () => {
    theme.value = theme.value === 'dark' ? 'light' : 'dark';
    localStorage.theme = theme.value;
    document.documentElement.classList.toggle('dark', theme.value === 'dark');
};

const openNewPayoutForm = () => {
    editingPayout.value = null;
    showForm.value = true;
};

const startEdit = (payout) => {
    editingPayout.value = payout;
    showForm.value = true;
};

const showToast = (message, type = 'success') => {
    $toast.open({
        message,
        type: type === 'danger' ? 'error' : type,
        position: 'bottom-right',
        duration: 3000
    });
};

const handlePayoutSaved = (message) => {
    showToast(message, 'success');
    showForm.value = false;
    editingPayout.value = null;
};

const totalPaid = computed(() =>
    props.payouts.reduce((sum, p) => sum + Number(p.amount_iqd || 0), 0)
);
const completedCount = computed(() =>
    props.payouts.filter(p => p.status === 'completed').length
);
const pendingCount = computed(() =>
    props.payouts.filter(p => p.status === 'pending').length
);
const processingCount = computed(() =>
    props.payouts.filter(p => p.status === 'processing').length
);

const formatNumber = (n) => new Intl.NumberFormat('en-US').format(n);
</script>

<template>
    <Head title="Payouts — Dashboard" />

    <div class="min-h-screen bg-surface-50 dark:bg-surface-950 text-surface-900 dark:text-surface-100 font-sans antialiased transition-colors duration-300">

        <!-- Top Nav -->
        <nav class="sticky top-0 z-20 bg-white dark:bg-surface-900 border-b border-surface-200 dark:border-surface-800">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 bg-accent flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-sm font-semibold tracking-tight">Payouts</span>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        @click="openNewPayoutForm"
                        class="hidden sm:inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 bg-accent hover:bg-accent-dark text-white cursor-pointer transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-surface-900"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        New Payout
                    </button>

                    <button
                        @click="toggleTheme"
                        class="w-8 h-8 flex items-center justify-center text-surface-500 hover:text-surface-900 dark:hover:text-white hover:bg-surface-100 dark:hover:bg-surface-800 cursor-pointer transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-accent/40"
                        aria-label="Toggle theme"
                    >
                        <svg v-if="theme === 'light'" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m0 13.5V21M4.22 4.22l1.59 1.59m12.38 12.38l1.59 1.59M3 12h2.25m13.5 0H21M6.78 17.22l-1.59 1.59m12.38-12.38l-1.59 1.59M12 9a3 3 0 100 6 3 3 0 000-6z" />
                        </svg>
                    </button>
                </div>
            </div>
        </nav>

        <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8 space-y-6">



            <!-- Stats -->
            <section class="space-y-4">
                <!-- Hero Stat: Total Disbursed -->
                <div class="p-6 bg-white dark:bg-surface-900 border border-surface-200 dark:border-surface-800 border-l-2 border-l-accent">
                    <div class="flex items-end justify-between">
                        <div>
                            <p class="text-[11px] font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-1">Total Disbursed</p>
                            <p class="text-3xl font-bold font-mono tracking-tighter text-surface-900 dark:text-white">{{ formatNumber(totalPaid) }}<span class="text-sm font-medium text-surface-400 ml-2 tracking-normal">IQD</span></p>
                        </div>
                        <p class="text-xs text-surface-400 font-medium font-mono">{{ payouts.length }} transactions</p>
                    </div>
                </div>

                <!-- Secondary Stats -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="p-4 bg-white dark:bg-surface-900 border border-surface-200 dark:border-surface-800">
                        <p class="text-[10px] font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-1">Completed</p>
                        <p class="text-xl font-bold font-mono tracking-tight text-emerald-500">{{ completedCount }}</p>
                    </div>
                    <div class="p-4 bg-white dark:bg-surface-900 border border-surface-200 dark:border-surface-800">
                        <p class="text-[10px] font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-1">Processing</p>
                        <p class="text-xl font-bold font-mono tracking-tight text-amber-400">{{ processingCount }}</p>
                    </div>
                    <div class="p-4 bg-white dark:bg-surface-900 border border-surface-200 dark:border-surface-800">
                        <p class="text-[10px] font-medium text-surface-500 dark:text-surface-400 uppercase tracking-wider mb-1">Pending</p>
                        <p class="text-xl font-bold font-mono tracking-tight text-surface-400">{{ pendingCount }}</p>
                    </div>
                </div>
            </section>

            <!-- Mobile New Payout Button -->
            <button
                @click="openNewPayoutForm"
                class="sm:hidden w-full flex items-center justify-center gap-2 text-sm font-medium px-4 py-3 bg-accent hover:bg-accent-dark text-white cursor-pointer transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-accent/40"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                New Payout
            </button>

            <!-- Payout Table -->
            <PayoutTable :payouts="payouts" @edit-payout="startEdit" @toast="showToast" />
        </main>

        <!-- Modal Overlay -->
        <teleport to="body">
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="showForm"
                    class="fixed inset-0 z-50 flex items-center justify-center px-4"
                    @keydown.escape="showForm = false"
                >
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black/60" @click="showForm = false"></div>

                    <!-- Modal Panel -->
                    <transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95 translate-y-2"
                        enter-to-class="opacity-100 scale-100 translate-y-0"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100 translate-y-0"
                        leave-to-class="opacity-0 scale-95 translate-y-2"
                        appear
                    >
                        <div v-if="showForm" class="relative w-full max-w-md z-10">
                            <!-- Close Button -->
                            <button
                                @click="showForm = false"
                                class="absolute -top-10 right-0 text-surface-400 hover:text-white text-xs font-medium flex items-center gap-1 cursor-pointer transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                ESC
                            </button>
                            <PayoutForm :employees="employees" :payout="editingPayout" @payout-saved="handlePayoutSaved" />
                        </div>
                    </transition>
                </div>
            </transition>
        </teleport>

    </div>
</template>
