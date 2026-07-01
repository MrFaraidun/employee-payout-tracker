<script setup>
import { ref, computed, watch } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchInput from '@/Components/SearchInput.vue';
import EmptyState from '@/Components/EmptyState.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import Modal from '@/Components/Modal.vue';
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import CustomDropdown from '@/Components/CustomDropdown.vue';
import { useToast } from '@/Composables/useToast';
import { useFilters } from '@/Composables/useFilters';

const props = defineProps({
    payouts: {
        type: Object,
        required: true,
    },
    employees: {
        type: Object,
        required: true,
    },
    organizations: {
        type: Object,
        required: true,
    },
    statuses: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({ search: '', status: '', employee_id: '', date_from: '', date_to: '' }),
    },
});

const toast = useToast();
const page = usePage();
const currentUser = computed(() => page.props.auth.user);

const { filters } = useFilters({
    search: props.filters.search,
    status: props.filters.status,
    employee_id: props.filters.employee_id,
    date_from: props.filters.date_from,
    date_to: props.filters.date_to,
});

const activeFiltersCount = computed(() => {
    let count = 0;
    if (filters.status) count++;
    if (filters.employee_id) count++;
    if (filters.date_from || filters.date_to) count++;
    return count;
});

const filtersExpanded = ref(activeFiltersCount.value > 0);

const toggleFilters = () => {
    filtersExpanded.value = !filtersExpanded.value;
};

const clearAllFilters = () => {
    filters.status = '';
    filters.employee_id = '';
    filters.date_from = '';
    filters.date_to = '';
    filters.search = '';
};

// Modals reactive state
const showFormModal = ref(false);
const showDeleteDialog = ref(false);
const editingPayout = ref(null);
const payoutToDelete = ref(null);

watch(showFormModal, (newVal) => {
    if (!newVal) {
        editingPayout.value = null;
        form.reset();
        form.clearErrors();
    }
});

const form = useForm({
    organization_id: '',
    employee_id: '',
    task: '',
    amount_iqd: '',
    status: 'Pending',
});

const availableOrganizations = computed(() => {
    return props.organizations.data.map(org => ({
        value: org.id,
        label: org.name
    }));
});

// Load employees filtered by chosen organization (for SuperAdmins)
const availableEmployees = computed(() => {
    const orgId = currentUser.value.role === 'SuperAdmin'
        ? form.organization_id
        : currentUser.value.organization_id;

    if (!orgId) return [];

    return props.employees.data
        .filter(emp => emp.organization_id === Number(orgId))
        .map(emp => ({
            value: emp.id,
            label: `${emp.name} (${emp.employee_code})`
        }));
});

// List of all employees for the filter dropdown
const filterEmployeesList = computed(() => {
    return props.employees.data.map(emp => ({
        value: emp.id,
        label: emp.name
    }));
});

const statusFilterOptions = computed(() => {
    return [
        { value: '', label: 'All Statuses' },
        ...props.statuses
    ];
});

const employeeFilterOptions = computed(() => {
    return [
        { value: '', label: 'All Employees' },
        ...filterEmployeesList.value
    ];
});

const openCreateModal = () => {
    editingPayout.value = null;
    form.reset();
    form.clearErrors();

    // Auto-select organization if user is Admin/Accountant
    if (currentUser.value.role !== 'SuperAdmin') {
        form.organization_id = currentUser.value.organization_id;
    }

    showFormModal.value = true;
};

const openEditModal = (payout) => {
    editingPayout.value = payout;
    form.organization_id = payout.organization_id;
    form.employee_id = payout.employee_id;
    form.task = payout.task;
    form.amount_iqd = payout.amount_iqd;
    form.status = payout.status;
    form.clearErrors();
    showFormModal.value = true;
};

const submitForm = () => {
    if (editingPayout.value) {
        form.put(route('payouts.update', editingPayout.value.id), {
            onSuccess: () => {
                toast.success('Payout log updated successfully.');
                showFormModal.value = false;
            },
        });
    } else {
        form.post(route('payouts.store'), {
            onSuccess: () => {
                toast.success('Payout logged successfully.');
                showFormModal.value = false;
                form.reset();
            },
        });
    }
};

const openDeleteModal = (payout) => {
    payoutToDelete.value = payout;
    showDeleteDialog.value = true;
};

const confirmDelete = () => {
    form.delete(route('payouts.destroy', payoutToDelete.value.id), {
        onSuccess: () => {
            toast.success('Payout log entry removed successfully.');
            showDeleteDialog.value = false;
            payoutToDelete.value = null;
        },
    });
};

const formatIQD = (amount) => {
    return new Intl.NumberFormat('en-US').format(amount) + ' IQD';
};
</script>

<template>
    <AppLayout>
        <template #header>Payout Transactions Log</template>
        <Head title="Payouts Log" />

        <div class="space-y-6">
            <!-- Filters & Actions Bar -->
            <div class="flex flex-col gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-brand-surface border border-zinc-200 dark:border-zinc-800 p-4 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <SearchInput v-model="filters.search" placeholder="Search tasks..." />
                        
                        <button
                            @click="toggleFilters"
                            class="flex items-center gap-2 px-4 py-2.5 bg-brand-input text-xs font-semibold text-brand-text-secondary border border-zinc-200 dark:border-zinc-800 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-900/50 hover:text-brand-text transition-all duration-150 cursor-pointer select-none focus:outline-none"
                            :class="[filtersExpanded ? 'border-emerald-500/35 text-emerald-600 dark:text-emerald-400' : '']"
                        >
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 8.293A1 1 0 013 7.586V4z" />
                            </svg>
                            <span>Filters</span>
                            <span v-if="activeFiltersCount > 0" class="flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[9px] font-extrabold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full">
                                {{ activeFiltersCount }}
                            </span>
                        </button>

                        <button
                            v-if="activeFiltersCount > 0"
                            @click="clearAllFilters"
                            class="text-[10px] font-sans font-extrabold uppercase tracking-wider text-red-600 dark:text-red-400 hover:underline transition-all duration-150 cursor-pointer"
                        >
                            Clear All
                        </button>
                    </div>

                    <button
                        @click="openCreateModal"
                        class="px-5 py-2.5 bg-brand-primary text-white font-sans font-semibold text-xs tracking-wide rounded-xl hover:opacity-90 active:scale-[0.98] transition-all duration-200 cursor-pointer select-none whitespace-nowrap shadow-sm self-start sm:self-auto"
                    >
                        Log Payout Record
                    </button>
                </div>

                <!-- Expandable Filters Panel (Nested Double-Bezel Card) -->
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 -translate-y-3"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 -translate-y-3"
                >
                    <div v-show="filtersExpanded" class="p-1 bg-zinc-200/40 dark:bg-zinc-800/30 border border-zinc-200/80 dark:border-zinc-800/60 rounded-3xl shadow-sm">
                        <div class="p-5 bg-brand-surface rounded-[calc(1.5rem-1px)] grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Status Filter -->
                            <div class="flex flex-col gap-1.5 text-left">
                                <label class="text-[9px] font-sans font-bold uppercase tracking-wider text-brand-text-muted">Filter by Status</label>
                                <CustomDropdown
                                    v-model="filters.status"
                                    :options="statusFilterOptions"
                                    placeholder="All Statuses"
                                />
                            </div>

                            <!-- Employee Filter -->
                            <div class="flex flex-col gap-1.5 text-left">
                                <label class="text-[9px] font-sans font-bold uppercase tracking-wider text-brand-text-muted">Filter by Employee</label>
                                <CustomDropdown
                                    v-model="filters.employee_id"
                                    :options="employeeFilterOptions"
                                    placeholder="All Employees"
                                />
                            </div>

                            <!-- Date Range Filter -->
                            <div class="flex flex-col gap-1.5 text-left">
                                <label class="text-[9px] font-sans font-bold uppercase tracking-wider text-brand-text-muted">Date Range (Period)</label>
                                <div class="flex items-center gap-2.5 text-xs font-sans bg-brand-input border border-zinc-200 dark:border-zinc-800 px-3.5 py-1 rounded-xl">
                                    <input
                                        type="date"
                                        v-model="filters.date_from"
                                        class="bg-transparent text-brand-text border-none p-1.5 focus:outline-none focus:ring-0 text-xs rounded-lg cursor-pointer flex-1"
                                    />
                                    <span class="text-brand-text-muted font-bold select-none text-xs">→</span>
                                    <input
                                        type="date"
                                        v-model="filters.date_to"
                                        class="bg-transparent text-brand-text border-none p-1.5 focus:outline-none focus:ring-0 text-xs rounded-lg cursor-pointer flex-1"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>

            <!-- Payouts Table -->
            <div v-if="payouts.data.length === 0">
                <EmptyState title="No payouts recorded" description="Register a payout to track employee project logs and contract deliveries." />
            </div>
            
            <div v-else class="space-y-4">
                <DataTable :headers="['ID', 'Employee', 'Task Description', 'Amount', 'Status', 'Logged By', 'Actions']" :items="payouts.data">
                    <template #row="{ item }">
                        <td class="px-5 py-3 font-mono text-brand-text-muted text-xs">#{{ item.id }}</td>
                        <td class="px-5 py-3 font-semibold text-brand-text">{{ item.employee?.name || 'N/A' }}</td>
                        <td class="px-5 py-3 text-brand-text-secondary max-w-xs truncate">{{ item.task }}</td>
                        <td class="px-5 py-3 font-mono text-emerald-600 dark:text-emerald-400 font-semibold text-xs">{{ formatIQD(item.amount_iqd) }}</td>
                        <td class="px-5 py-3"><StatusBadge :value="item.status" /></td>
                        <td class="px-5 py-3 text-brand-text-muted font-mono text-[10px] uppercase">{{ item.creator?.name || 'SYSTEM' }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3 font-sans text-xs font-semibold tracking-wide">
                                <button @click="openEditModal(item)" class="text-brand-primary hover:underline transition-all duration-150 cursor-pointer">Edit</button>
                                <!-- Only Admins / SuperAdmins can delete payout records -->
                                <button
                                    v-if="currentUser.role !== 'Accountant'"
                                    @click="openDeleteModal(item)"
                                    class="text-red-600 dark:text-red-400 hover:underline transition-all duration-150 cursor-pointer"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </template>
                </DataTable>
                
                <div class="flex justify-end mt-4">
                    <Pagination :links="payouts.meta.links" />
                </div>
            </div>

            <!-- Creation / Edit Modal -->
            <Modal :show="showFormModal" max-width="md" @close="showFormModal = false">
                <form @submit.prevent="submitForm" class="p-6 sm:p-8 text-[color:var(--text-secondary)] bg-transparent border-none">
                    <h2 class="text-xs font-sans font-bold uppercase tracking-wider text-[color:var(--text-primary)] border-b border-[color:var(--border-main)] pb-2 mb-5">
                        {{ editingPayout ? 'Update Payout Details' : 'Log Payout Record' }}
                    </h2>
                    
                    <div class="space-y-4">
                        <!-- Show Organization Select Box ONLY for SuperAdmins -->
                        <FormSelect
                            v-if="currentUser.role === 'SuperAdmin'"
                            id="organization_id"
                            label="Assign Organization"
                            v-model="form.organization_id"
                            :options="availableOrganizations"
                            :error="form.errors.organization_id"
                            required
                        />

                        <FormSelect
                            id="employee_id"
                            label="Select Employee"
                            v-model="form.employee_id"
                            :options="availableEmployees"
                            :error="form.errors.employee_id"
                            required
                        />

                        <FormInput
                            id="task"
                            label="Task Description"
                            v-model="form.task"
                            :error="form.errors.task"
                            required
                            placeholder="e.g. Design UI/UX layouts for the website"
                        />

                        <FormInput
                            id="amount_iqd"
                            type="number"
                            label="Payout Amount (IQD)"
                            v-model="form.amount_iqd"
                            :error="form.errors.amount_iqd"
                            required
                            placeholder="e.g. 150000"
                        />

                        <FormSelect
                            id="status"
                            label="Transaction Status"
                            v-model="form.status"
                            :options="statuses"
                            :error="form.errors.status"
                            required
                        />
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3 font-sans text-xs uppercase tracking-wider border-t border-[color:var(--border-main)] pt-4">
                        <button
                            type="button"
                            @click="showFormModal = false"
                            class="px-5 py-2.5 bg-[color:var(--bg-input)] hover:bg-zinc-150 dark:hover:bg-zinc-800 text-[color:var(--text-secondary)] border border-[color:var(--border-main)] rounded-xl transition-all duration-150 cursor-pointer select-none"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-5 py-2.5 bg-[color:var(--primary)] hover:opacity-90 text-[color:var(--primary-text)] font-semibold rounded-xl transition-all duration-150 active:scale-[0.98] cursor-pointer disabled:opacity-50 select-none shadow-sm"
                        >
                            <span v-if="form.processing" class="font-mono">[PROCESSING...]</span>
                            <span v-else>{{ editingPayout ? 'Save changes' : 'Log Payout' }}</span>
                        </button>
                    </div>
                </form>
            </Modal>

            <!-- Delete Confirmation -->
            <ConfirmDialog
                :show="showDeleteDialog"
                title="Remove Payout Record"
                message="Are you sure you want to delete this payout log? This action will immediately remove the entry from auditing reports."
                confirm-text="Delete Record"
                :processing="form.processing"
                @confirm="confirmDelete"
                @close="showDeleteDialog = false"
            />
        </div>
    </AppLayout>
</template>
