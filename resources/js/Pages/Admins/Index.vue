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
import { useToast } from '@/Composables/useToast';
import { useFilters } from '@/Composables/useFilters';

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    organizations: {
        type: Object,
        required: true,
    },
    roles: {
        type: Array,
        required: true,
    },
    statuses: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({ search: '' }),
    },
});

const toast = useToast();
const page = usePage();
const currentUser = computed(() => page.props.auth.user);

const { filters } = useFilters({ search: props.filters.search });

// Modals reactive state
const showFormModal = ref(false);
const showDeleteDialog = ref(false);
const editingUser = ref(null);
const userToDelete = ref(null);

watch(showFormModal, (newVal) => {
    if (!newVal) {
        editingUser.value = null;
        form.reset();
        form.clearErrors();
    }
});

const form = useForm({
    organization_id: '',
    name: '',
    email: '',
    password: '',
    role: 'Accountant',
    status: 'Active',
});

// Computed properties for forms
const availableOrganizations = computed(() => {
    return props.organizations.data.map(org => ({
        value: org.id,
        label: org.name
    }));
});

const availableRoles = computed(() => {
    // Admins cannot create SuperAdmins
    if (currentUser.value.role !== 'SuperAdmin') {
        return props.roles.filter(r => r !== 'SuperAdmin');
    }
    return props.roles;
});

const openCreateModal = () => {
    editingUser.value = null;
    form.reset();
    form.clearErrors();
    
    // Auto-select organization if user is Admin
    if (currentUser.value.role !== 'SuperAdmin') {
        form.organization_id = currentUser.value.organization_id;
    }
    
    showFormModal.value = true;
};

const openEditModal = (user) => {
    editingUser.value = user;
    form.organization_id = user.organization_id || '';
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.role = user.role;
    form.status = user.status;
    form.clearErrors();
    showFormModal.value = true;
};

const submitForm = () => {
    if (editingUser.value) {
        form.put(route('users.update', editingUser.value.id), {
            onSuccess: () => {
                toast.success('User updated successfully.');
                showFormModal.value = false;
            },
        });
    } else {
        form.post(route('users.store'), {
            onSuccess: () => {
                toast.success('User registered successfully.');
                showFormModal.value = false;
                form.reset();
            },
        });
    }
};

const openDeleteModal = (user) => {
    userToDelete.value = user;
    showDeleteDialog.value = true;
};

const confirmDelete = () => {
    form.delete(route('users.destroy', userToDelete.value.id), {
        onSuccess: () => {
            toast.success('User account deleted successfully.');
            showDeleteDialog.value = false;
            userToDelete.value = null;
        },
    });
};
</script>

<template>
    <AppLayout>
        <template #header>Admins & Users Directory</template>
        <Head title="Admins & Users" />

        <div class="space-y-6">
            <!-- Search & Actions Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-[color:var(--bg-surface)] border border-[color:var(--border-main)] p-4 rounded-2xl shadow-sm">
                <SearchInput v-model="filters.search" placeholder="Search user accounts..." />
                
                <button
                    @click="openCreateModal"
                    class="px-5 py-2.5 bg-brand-primary text-white font-sans font-semibold text-xs tracking-wide rounded-xl hover:opacity-90 active:scale-[0.98] transition-all duration-200 cursor-pointer select-none shadow-sm"
                >
                    Create User Account
                </button>
            </div>

            <!-- Users Table -->
            <div v-if="users.data.length === 0">
                <EmptyState title="No users found" description="Invite admins or accountants to manage payouts." />
            </div>
            
            <div v-else class="space-y-4">
                <DataTable :headers="['Name', 'Email', 'Organization', 'Role', 'Status', 'Actions']" :items="users.data">
                    <template #row="{ item }">
                        <td class="px-5 py-3 font-semibold text-brand-text">{{ item.name }}</td>
                        <td class="px-5 py-3 text-brand-text-secondary font-mono text-xs">{{ item.email }}</td>
                        <td class="px-5 py-3 text-brand-text-muted font-mono text-xs">{{ item.organization?.name || 'SYSTEM (GLOBAL)' }}</td>
                        <td class="px-5 py-3"><StatusBadge :value="item.role" /></td>
                        <td class="px-5 py-3"><StatusBadge :value="item.status" /></td>
                        <td class="px-5 py-3">
                            <!-- Actions (Disable actions on self) -->
                            <div v-if="item.id !== currentUser.id" class="flex items-center gap-3 font-sans text-xs font-semibold tracking-wide">
                                <button @click="openEditModal(item)" class="text-brand-primary hover:underline transition-all duration-150 cursor-pointer">Edit</button>
                                <button @click="openDeleteModal(item)" class="text-red-650 dark:text-red-400 hover:underline transition-all duration-150 cursor-pointer">Delete</button>
                            </div>
                            <span v-else class="text-[10px] font-sans font-semibold text-brand-text-muted uppercase tracking-wider">Current Account</span>
                        </td>
                    </template>
                </DataTable>
                
                <div class="flex justify-end mt-4">
                    <Pagination :links="users.meta.links" />
                </div>
            </div>

            <!-- Creation / Edit Modal -->
            <Modal :show="showFormModal" max-width="md" @close="showFormModal = false">
                <form @submit.prevent="submitForm" class="p-6 sm:p-8 text-brand-text-secondary bg-transparent border-none">
                    <h2 class="text-xs font-sans font-bold uppercase tracking-wider text-brand-text border-b border-zinc-200 dark:border-zinc-800 pb-2 mb-5">
                        {{ editingUser ? 'Update User Account' : 'Invite User' }}
                    </h2>
                    
                    <div class="space-y-4">
                        <FormInput
                            id="name"
                            label="Full Name"
                            v-model="form.name"
                            :error="form.errors.name"
                            required
                            placeholder="e.g. John Doe"
                        />

                        <FormInput
                            id="email"
                            type="email"
                            label="Email Address"
                            v-model="form.email"
                            :error="form.errors.email"
                            required
                            placeholder="e.g. john@company.com"
                        />

                        <FormInput
                            id="password"
                            type="password"
                            :label="editingUser ? 'Password (Leave blank to keep same)' : 'Default Password'"
                            v-model="form.password"
                            :error="form.errors.password"
                            :required="!editingUser"
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
                            id="role"
                            label="Access Role"
                            v-model="form.role"
                            :options="availableRoles"
                            :error="form.errors.role"
                            required
                        />

                        <FormSelect
                            id="status"
                            label="Account Status"
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
                            <span v-else>{{ editingUser ? 'Save changes' : 'Create' }}</span>
                        </button>
                    </div>
                </form>
            </Modal>

            <!-- Delete Confirmation -->
            <ConfirmDialog
                :show="showDeleteDialog"
                title="Remove User Account"
                message="Are you sure you want to delete this user? Their login credentials and profile metadata will be permanently deleted."
                confirm-text="Delete Account"
                :processing="form.processing"
                @confirm="confirmDelete"
                @close="showDeleteDialog = false"
            />
        </div>
    </AppLayout>
</template>
