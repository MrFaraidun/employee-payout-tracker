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
        default: () => ({ search: '', status: '', department: '' }),
    },
});

const toast = useToast();
const page = usePage();
const currentUser = computed(() => page.props.auth.user);

const { filters } = useFilters({
    search: props.filters.search,
    status: props.filters.status,
    department: props.filters.department,
});

const activeFiltersCount = computed(() => {
    let count = 0;
    if (filters.status) count++;
    if (filters.department) count++;
    return count;
});
const statusFilterOptions = computed(() => {
    return [
        { value: '', label: 'All Statuses' },
        ...props.statuses
    ];
});
const filtersExpanded = ref(activeFiltersCount.value > 0);

const toggleFilters = () => {
    filtersExpanded.value = !filtersExpanded.value;
};

const clearAllFilters = () => {
    filters.status = '';
    filters.department = '';
    filters.search = '';
};

// Modals reactive state
const showFormModal = ref(false);
const showDeleteDialog = ref(false);
const editingEmployee = ref(null);
const employeeToDelete = ref(null);

watch(showFormModal, (newVal) => {
    if (!newVal) {
        editingEmployee.value = null;
        form.reset();
        form.clearErrors();
    }
});

const form = useForm({
    organization_id: '',
    name: '',
    employee_code: '',
    department: '',
    position: '',
    status: 'Active',
});

const availableOrganizations = computed(() => {
    return props.organizations.data.map(org => ({
        value: org.id,
        label: org.name
    }));
});

const openCreateModal = () => {
    editingEmployee.value = null;
    form.reset();
    form.clearErrors();

    // Auto-select organization if user is Admin
    if (currentUser.value.role !== 'SuperAdmin') {
        form.organization_id = currentUser.value.organization_id;
    }

    showFormModal.value = true;
};

const openEditModal = (emp) => {
    editingEmployee.value = emp;
    form.organization_id = emp.organization_id;
    form.name = emp.name;
    form.employee_code = emp.employee_code;
    form.department = emp.department;
    form.position = emp.position;
    form.status = emp.status;
    form.clearErrors();
    showFormModal.value = true;
};

const submitForm = () => {
    if (editingEmployee.value) {
        form.put(route('employees.update', editingEmployee.value.id), {
            onSuccess: () => {
                toast.success('Employee updated successfully.');
                showFormModal.value = false;
            },
        });
    } else {
        form.post(route('employees.store'), {
            onSuccess: () => {
                toast.success('Employee onboarded successfully.');
                showFormModal.value = false;
                form.reset();
            },
        });
    }
};

const openDeleteModal = (emp) => {
    employeeToDelete.value = emp;
    showDeleteDialog.value = true;
};

const confirmDelete = () => {
    form.delete(route('employees.destroy', employeeToDelete.value.id), {
        onSuccess: () => {
            toast.success('Employee record removed successfully.');
            showDeleteDialog.value = false;
            employeeToDelete.value = null;
        },
    });
};
</script>

<template>
    <AppLayout>
        <template #header>Employees Directory</template>
        <Head title="Employees" />

        <div class="space-y-6">
            <!-- Search & Actions Bar -->
            <div class="flex flex-col gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-brand-surface border border-zinc-200 dark:border-zinc-800 p-4 rounded-2xl shadow-sm">
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <SearchInput v-model="filters.search" placeholder="Search code or name..." />
                        
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
                        v-if="currentUser.role !== 'Accountant'"
                        @click="openCreateModal"
                        class="px-5 py-2.5 bg-brand-primary text-white font-sans font-semibold text-xs tracking-wide rounded-xl hover:opacity-90 active:scale-[0.98] transition-all duration-200 cursor-pointer select-none shadow-sm self-start sm:self-auto"
                    >
                        Onboard Employee
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
                        <div class="p-5 bg-brand-surface rounded-[calc(1.5rem-1px)] grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Status Filter -->
                            <div class="flex flex-col gap-1.5 text-left">
                                <label class="text-[9px] font-sans font-bold uppercase tracking-wider text-brand-text-muted">Filter by Status</label>
                                <CustomDropdown
                                    v-model="filters.status"
                                    :options="statusFilterOptions"
                                    placeholder="All Statuses"
                                />
                            </div>

                            <!-- Department Filter -->
                            <div class="flex flex-col gap-1.5 text-left">
                                <label class="text-[9px] font-sans font-bold uppercase tracking-wider text-brand-text-muted">Filter by Department</label>
                                <input
                                    type="text"
                                    v-model="filters.department"
                                    placeholder="e.g. Engineering"
                                    class="w-full bg-brand-input text-brand-text border border-zinc-200 dark:border-zinc-800 text-xs px-3.5 py-2.5 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 placeholder-zinc-400 dark:placeholder-zinc-600 transition-all duration-200 ease-out"
                                />
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>

            <!-- Employees Table -->
            <div v-if="employees.data.length === 0">
                <EmptyState title="No employees onboarded" description="Onboard employees to start logging their monthly contract payouts." />
            </div>
            
            <div v-else class="space-y-4">
                <DataTable :headers="['Code', 'Name', 'Department', 'Position', 'Organization', 'Status', 'Actions']" :items="employees.data">
                    <template #row="{ item }">
                        <td class="px-5 py-3 font-mono text-brand-primary font-semibold text-xs">{{ item.employee_code }}</td>
                        <td class="px-5 py-3 font-semibold text-brand-text">{{ item.name }}</td>
                        <td class="px-5 py-3 text-brand-text-secondary">{{ item.department }}</td>
                        <td class="px-5 py-3 text-brand-text-secondary">{{ item.position }}</td>
                        <td class="px-5 py-3 text-brand-text-muted font-mono text-[11px]">{{ item.organization?.name || 'N/A' }}</td>
                        <td class="px-5 py-3"><StatusBadge :value="item.status" /></td>
                        <td class="px-5 py-3">
                            <div v-if="currentUser.role !== 'Accountant'" class="flex items-center gap-3 font-sans text-xs font-semibold tracking-wide">
                                <button @click="openEditModal(item)" class="text-brand-primary hover:underline transition-all duration-150 cursor-pointer">Edit</button>
                                <button @click="openDeleteModal(item)" class="text-red-650 dark:text-red-400 hover:underline transition-all duration-150 cursor-pointer">Archive</button>
                            </div>
                            <span v-else class="text-[10px] font-sans font-semibold text-brand-text-muted uppercase tracking-wide">View Only</span>
                        </td>
                    </template>
                </DataTable>
                
                <div class="flex justify-end mt-4">
                    <Pagination :links="employees.meta.links" />
                </div>
            </div>

            <!-- Creation / Edit Modal -->
            <Modal :show="showFormModal" max-width="md" @close="showFormModal = false">
                <form @submit.prevent="submitForm" class="p-6 sm:p-8 text-[color:var(--text-secondary)] bg-transparent border-none">
                    <h2 class="text-xs font-sans font-bold uppercase tracking-wider text-[color:var(--text-primary)] border-b border-[color:var(--border-main)] pb-2 mb-5">
                        {{ editingEmployee ? 'Update Employee Details' : 'Onboard Employee' }}
                    </h2>
                    
                    <div class="space-y-4">
                        <FormInput
                            id="name"
                            label="Employee Name"
                            v-model="form.name"
                            :error="form.errors.name"
                            required
                            placeholder="e.g. Aland Karwan"
                        />

                        <FormInput
                            id="employee_code"
                            label="Employee Code (Unique)"
                            v-model="form.employee_code"
                            :error="form.errors.employee_code"
                            required
                            placeholder="e.g. EMP-103"
                        />

                        <FormInput
                            id="department"
                            label="Department"
                            v-model="form.department"
                            :error="form.errors.department"
                            required
                            placeholder="e.g. Engineering"
                        />

                        <FormInput
                            id="position"
                            label="Job Position"
                            v-model="form.position"
                            :error="form.errors.position"
                            required
                            placeholder="e.g. Frontend Developer"
                        />

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
                            id="status"
                            label="Employment Status"
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
                            <span v-else>{{ editingEmployee ? 'Save changes' : 'Onboard' }}</span>
                        </button>
                    </div>
                </form>
            </Modal>

            <!-- Delete Confirmation -->
            <ConfirmDialog
                :show="showDeleteDialog"
                title="Archive Employee Record"
                message="Are you sure you want to delete this employee's metadata record? Associated historical payout lists will remain locked in database records."
                confirm-text="Archive Record"
                :processing="form.processing"
                @confirm="confirmDelete"
                @close="showDeleteDialog = false"
            />
        </div>
    </AppLayout>
</template>
