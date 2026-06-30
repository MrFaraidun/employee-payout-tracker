<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import StatusBadge from './StatusBadge.vue';

const props = defineProps({
    payouts: { type: Array, required: true }
});

const emit = defineEmits(['edit-payout', 'toast']);

const search = ref('');
const sortKey = ref('created_at');
const sortDir = ref('desc');

const activeDropdown = ref(null);
const payoutToDelete = ref(null);

const toggleDropdown = (id) => {
    if (activeDropdown.value === id) {
        activeDropdown.value = null;
    } else {
        activeDropdown.value = id;
    }
};

const updateStatus = (id, status) => {
    router.patch(route('payouts.update-status', id), { status }, {
        preserveScroll: true,
        onSuccess: () => {
            activeDropdown.value = null;
            emit('toast', 'Status updated successfully!');
        }
    });
};

const triggerDelete = (payout) => {
    payoutToDelete.value = payout;
};

const cancelDelete = () => {
    payoutToDelete.value = null;
};

const executeDelete = () => {
    if (payoutToDelete.value) {
        router.delete(route('payouts.destroy', payoutToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                payoutToDelete.value = null;
                emit('toast', 'Payout deleted successfully!');
            }
        });
    }
};

const toggleSort = (key) => {
    if (key === 'actions') return;
    if (sortKey.value === key) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = key;
        sortDir.value = 'asc';
    }
};

const filtered = computed(() => {
    const q = search.value.toLowerCase().trim();
    let list = [...props.payouts];
    if (q) {
        list = list.filter(p =>
            (p.employee?.name || '').toLowerCase().includes(q) ||
            (p.task || '').toLowerCase().includes(q)
        );
    }
    list.sort((a, b) => {
        let valA, valB;
        switch (sortKey.value) {
            case 'created_at':
                valA = new Date(a.created_at || 0).getTime();
                valB = new Date(b.created_at || 0).getTime();
                break;
            case 'employee':
                valA = (a.employee?.name || '').toLowerCase();
                valB = (b.employee?.name || '').toLowerCase();
                break;
            case 'task':
                valA = (a.task || '').toLowerCase();
                valB = (b.task || '').toLowerCase();
                break;
            case 'amount_iqd':
                valA = Number(a.amount_iqd || 0);
                valB = Number(b.amount_iqd || 0);
                break;
            case 'status':
                valA = a.status || '';
                valB = b.status || '';
                break;
            default:
                return 0;
        }
        if (valA < valB) return sortDir.value === 'asc' ? -1 : 1;
        if (valA > valB) return sortDir.value === 'asc' ? 1 : -1;
        return 0;
    });
    return list;
});

const formatAmount = (amount) => new Intl.NumberFormat('en-US').format(amount) + ' IQD';

const formatDate = (dateString) => {
    if (!dateString) return '—';
    return new Date(dateString).toLocaleDateString('en-US', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};

const columns = [
    { key: 'created_at', label: 'Date', align: '' },
    { key: 'employee', label: 'Employee', align: '' },
    { key: 'task', label: 'Task', align: '', grow: true },
    { key: 'amount_iqd', label: 'Amount', align: 'text-right' },
    { key: 'status', label: 'Status', align: 'text-right' },
    { key: 'actions', label: '', align: 'text-right' },
];
</script>

<template>
    <div class="bg-white dark:bg-surface-900 border border-surface-200 dark:border-surface-800 overflow-visible relative">
        
        <!-- Transparent click-outside handler for dropdowns -->
        <div v-if="activeDropdown" class="fixed inset-0 z-10" @click="activeDropdown = null"></div>

        <!-- Header -->
        <div class="px-5 py-4 border-b border-surface-200 dark:border-surface-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-sm font-semibold text-surface-900 dark:text-white">Payout Records</h2>
                <p class="text-xs text-surface-500 dark:text-surface-400 mt-0.5">All transactions in the ledger.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-surface-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search..."
                        class="w-44 bg-surface-50 dark:bg-surface-950 border border-surface-200 dark:border-surface-700 pl-8 pr-3 py-1.5 text-xs text-surface-900 dark:text-surface-100 placeholder:text-surface-400 focus:border-accent focus:ring-1 focus:ring-accent/30 transition-colors duration-150"
                    />
                </div>
                <div class="text-xs text-surface-500 dark:text-surface-400 font-medium tabular-nums whitespace-nowrap">
                    <span class="font-mono font-semibold text-surface-900 dark:text-white">{{ filtered.length }}</span> records
                </div>
            </div>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left table-auto">
                <thead>
                    <tr class="border-b border-surface-200 dark:border-surface-800 bg-surface-50 dark:bg-surface-950/50">
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            @click="toggleSort(col.key)"
                            class="px-5 py-3 text-[10px] font-semibold uppercase tracking-wider select-none transition-colors duration-100"
                            :class="[
                                col.align,
                                col.grow ? 'w-full' : '',
                                col.key !== 'actions' ? 'cursor-pointer hover:text-surface-900 dark:hover:text-surface-200' : '',
                                sortKey === col.key ? 'text-surface-900 dark:text-surface-200' : 'text-surface-400'
                            ]"
                        >
                            <span class="inline-flex items-center gap-1">
                                {{ col.label }}
                                <svg
                                    v-if="col.key !== 'actions'"
                                    class="w-3 h-3 shrink-0 transition-transform duration-150"
                                    :class="[
                                        sortKey === col.key && sortDir === 'desc' ? 'rotate-180' : '',
                                        sortKey === col.key ? 'opacity-100' : 'opacity-30'
                                    ]"
                                    fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"
                                ><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" /></svg>
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100 dark:divide-surface-800/60">
                    <tr v-if="filtered.length === 0">
                        <td colspan="6" class="px-5 py-16 text-center text-sm text-surface-400">
                            {{ search ? 'No matching payouts found.' : 'No payouts yet. Click "New Payout" to get started.' }}
                        </td>
                    </tr>
                    <tr
                        v-for="(payout, index) in filtered"
                        :key="payout.id"
                        class="hover:bg-surface-100/50 dark:hover:bg-surface-800/40 transition-colors duration-100"
                        :class="index % 2 === 1 ? 'bg-surface-50/50 dark:bg-surface-950/30' : ''"
                    >
                        <td class="px-5 py-3 text-xs font-mono text-surface-500 whitespace-nowrap">
                            {{ formatDate(payout.created_at) }}
                        </td>
                        <td class="px-5 py-3 text-sm font-medium text-surface-900 dark:text-surface-100 whitespace-nowrap">
                            {{ payout.employee ? payout.employee.name : 'Unknown' }}
                        </td>
                        <td class="px-5 py-3 text-sm text-surface-600 dark:text-surface-400 truncate max-w-xs">
                            {{ payout.task }}
                        </td>
                        <td class="px-5 py-3 text-sm font-mono font-semibold text-surface-900 dark:text-surface-100 text-right whitespace-nowrap">
                            {{ formatAmount(payout.amount_iqd) }}
                        </td>
                        <td class="px-5 py-3 text-right whitespace-nowrap relative">
                            <div class="inline-flex items-center gap-2 justify-end">
                                <StatusBadge :status="payout.status" class="cursor-pointer hover:opacity-80" @click.stop="toggleDropdown(payout.id)" title="Click to choose status" />
                                
                                <!-- Quick Status Switch Action Buttons -->
                                <div class="inline-flex items-center gap-1">
                                    <!-- Advance Pending -> Processing -->
                                    <button
                                        v-if="payout.status === 'pending'"
                                        @click.stop="updateStatus(payout.id, 'processing')"
                                        class="p-0.5 bg-amber-400/10 hover:bg-amber-400/20 text-amber-500 hover:text-amber-600 transition-colors duration-100"
                                        title="Start Processing"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" /></svg>
                                    </button>
                                    
                                    <!-- Advance Processing/Pending -> Completed -->
                                    <button
                                        v-if="payout.status === 'pending' || payout.status === 'processing'"
                                        @click.stop="updateStatus(payout.id, 'completed')"
                                        class="p-0.5 bg-emerald-400/10 hover:bg-emerald-400/20 text-emerald-500 hover:text-emerald-600 transition-colors duration-100"
                                        title="Complete Payout"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Dropdown Menu for Status -->
                            <div
                                v-if="activeDropdown === payout.id"
                                class="absolute right-5 top-9 z-20 w-32 bg-white dark:bg-surface-900 border border-surface-200 dark:border-surface-800 text-left"
                            >
                                <button
                                    @click="updateStatus(payout.id, 'pending')"
                                    class="w-full px-3 py-2 text-xs text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors duration-100 flex items-center gap-1.5"
                                >
                                    <span class="w-1.5 h-1.5 bg-surface-400"></span>
                                    Pending
                                </button>
                                <button
                                    @click="updateStatus(payout.id, 'processing')"
                                    class="w-full px-3 py-2 text-xs text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors duration-100 flex items-center gap-1.5"
                                >
                                    <span class="w-1.5 h-1.5 bg-amber-400"></span>
                                    Processing
                                </button>
                                <button
                                    @click="updateStatus(payout.id, 'completed')"
                                    class="w-full px-3 py-2 text-xs text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors duration-100 flex items-center gap-1.5"
                                >
                                    <span class="w-1.5 h-1.5 bg-emerald-400"></span>
                                    Completed
                                </button>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-3">
                                <!-- Full Edit Action Button -->
                                <button
                                    @click.stop="$emit('edit-payout', payout)"
                                    class="p-1 text-surface-400 hover:text-surface-900 dark:hover:text-white transition-colors duration-100 focus:outline-none"
                                    title="Edit Payout Details"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                                </button>

                                <!-- Quick Delete Button -->
                                <button
                                    @click.stop="triggerDelete(payout)"
                                    class="p-1 text-surface-400 hover:text-rose-500 transition-colors duration-100 focus:outline-none"
                                    title="Delete Payout"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-surface-100 dark:divide-surface-800/60">
            <div v-if="filtered.length === 0" class="p-8 text-center text-sm text-surface-400">
                {{ search ? 'No matching payouts found.' : 'No payouts yet. Tap "New Payout" to get started.' }}
            </div>
            <div
                v-for="payout in filtered"
                :key="payout.id"
                class="p-4 space-y-3"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono text-surface-500">{{ formatDate(payout.created_at) }}</span>
                    <StatusBadge :status="payout.status" class="cursor-pointer" @click.stop="toggleDropdown(payout.id)" />
                </div>
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-surface-900 dark:text-white truncate">{{ payout.employee ? payout.employee.name : 'Unknown' }}</p>
                        <p class="text-xs text-surface-500 dark:text-surface-400 truncate mt-0.5">{{ payout.task }}</p>
                    </div>
                    <span class="text-sm font-mono font-semibold text-surface-900 dark:text-white whitespace-nowrap ml-4">
                        {{ formatAmount(payout.amount_iqd) }}
                    </span>
                </div>
                
                <!-- Actions Row for Mobile -->
                <div class="pt-2 border-t border-surface-100 dark:border-surface-800/40 flex items-center justify-between gap-4 relative">
                    <!-- Quick Status Actions for Mobile -->
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] text-surface-400 uppercase tracking-wider font-semibold">Change Status:</span>
                        <div class="inline-flex items-center gap-1">
                            <button
                                v-if="payout.status === 'pending'"
                                @click.stop="updateStatus(payout.id, 'processing')"
                                class="px-2 py-1 text-[10px] font-semibold bg-amber-400/10 hover:bg-amber-400/20 text-amber-500 transition-colors"
                            >
                                Process
                            </button>
                            <button
                                v-if="payout.status === 'pending' || payout.status === 'processing'"
                                @click.stop="updateStatus(payout.id, 'completed')"
                                class="px-2 py-1 text-[10px] font-semibold bg-emerald-400/10 hover:bg-emerald-400/20 text-emerald-500 transition-colors"
                            >
                                Complete
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Edit button -->
                        <button
                            @click.stop="$emit('edit-payout', payout)"
                            class="text-xs font-medium text-surface-500 hover:text-surface-900 dark:hover:text-white flex items-center gap-1 focus:outline-none"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
                            Edit
                        </button>

                        <!-- Delete button -->
                        <button
                            @click.stop="triggerDelete(payout)"
                            class="text-xs font-medium focus:outline-none flex items-center gap-1 text-surface-500 hover:text-rose-500"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                            Delete
                        </button>
                    </div>

                    <!-- Mobile Status Dropdown (from clicking badge) -->
                    <div
                        v-if="activeDropdown === payout.id"
                        class="absolute left-0 bottom-6 z-20 w-32 bg-white dark:bg-surface-900 border border-surface-200 dark:border-surface-800 text-left"
                    >
                        <button
                            @click="updateStatus(payout.id, 'pending')"
                            class="w-full px-3 py-2 text-xs text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors duration-100 flex items-center gap-1.5"
                        >
                            <span class="w-1.5 h-1.5 bg-surface-400"></span>
                            Pending
                        </button>
                        <button
                            @click="updateStatus(payout.id, 'processing')"
                            class="w-full px-3 py-2 text-xs text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors duration-100 flex items-center gap-1.5"
                        >
                            <span class="w-1.5 h-1.5 bg-amber-400"></span>
                            Processing
                        </button>
                        <button
                            @click="updateStatus(payout.id, 'completed')"
                            class="w-full px-3 py-2 text-xs text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors duration-100 flex items-center gap-1.5"
                        >
                            <span class="w-1.5 h-1.5 bg-emerald-400"></span>
                            Completed
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal Overlay -->
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
                    v-if="payoutToDelete"
                    class="fixed inset-0 z-50 flex items-center justify-center px-4"
                    @keydown.escape="cancelDelete"
                >
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black/60" @click="cancelDelete"></div>

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
                        <div v-if="payoutToDelete" class="relative w-full max-w-sm z-10 bg-white dark:bg-surface-900 border border-surface-200 dark:border-surface-800 p-6">
                            <div class="mb-4">
                                <h3 class="text-sm font-semibold text-surface-900 dark:text-white">Delete Payout Record</h3>
                                <p class="text-xs text-surface-500 dark:text-surface-400 mt-2">
                                    Are you sure you want to delete this payout to <strong class="text-surface-900 dark:text-white">{{ payoutToDelete.employee?.name || 'Unknown' }}</strong>? This action cannot be undone.
                                </p>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex items-center justify-end gap-3 mt-6">
                                <button
                                    @click="cancelDelete"
                                    class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-surface-700 dark:text-surface-300 hover:text-surface-900 dark:hover:text-white border border-surface-200 dark:border-surface-700 hover:border-surface-400 dark:hover:border-surface-500 bg-transparent transition-colors duration-150 cursor-pointer"
                                >
                                    No, Cancel
                                </button>
                                <button
                                    @click="executeDelete"
                                    class="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-white bg-rose-600 hover:bg-rose-700 transition-colors duration-150 cursor-pointer"
                                >
                                    Yes, Delete
                                </button>
                            </div>
                        </div>
                    </transition>
                </div>
            </transition>
        </teleport>
    </div>
</template>
