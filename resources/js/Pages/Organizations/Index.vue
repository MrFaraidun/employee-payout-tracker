<script setup>
import { ref, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
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
    organizations: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({ search: '' }),
    },
});

const toast = useToast();
const { filters } = useFilters({ search: props.filters.search });

// Modals reactive state
const showFormModal = ref(false);
const showDeleteDialog = ref(false);
const editingOrg = ref(null);
const orgToDelete = ref(null);

watch(showFormModal, (newVal) => {
    if (!newVal) {
        editingOrg.value = null;
        form.reset();
        form.clearErrors();
    }
});

const form = useForm({
    name: '',
    status: 'Active',
});

const openCreateModal = () => {
    editingOrg.value = null;
    form.reset();
    form.clearErrors();
    showFormModal.value = true;
};

const openEditModal = (org) => {
    editingOrg.value = org;
    form.name = org.name;
    form.status = org.status;
    form.clearErrors();
    showFormModal.value = true;
};

const submitForm = () => {
    if (editingOrg.value) {
        form.put(route('organizations.update', editingOrg.value.id), {
            onSuccess: () => {
                toast.success('Organization updated successfully.');
                showFormModal.value = false;
            },
        });
    } else {
        form.post(route('organizations.store'), {
            onSuccess: () => {
                toast.success('Organization created successfully.');
                showFormModal.value = false;
                form.reset();
            },
        });
    }
};

const openDeleteModal = (org) => {
    orgToDelete.value = org;
    showDeleteDialog.value = true;
};

const confirmDelete = () => {
    form.delete(route('organizations.destroy', orgToDelete.value.id), {
        onSuccess: () => {
            toast.success('Organization deleted successfully.');
            showDeleteDialog.value = false;
            orgToDelete.value = null;
        },
    });
};
</script>

<template>
    <AppLayout>
        <template #header>Organizations Directory</template>
        <Head title="Organizations" />

        <div class="space-y-6">
            <!-- Search & Actions Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-[color:var(--bg-surface)] border border-[color:var(--border-main)] p-4 rounded-2xl shadow-sm">
                <SearchInput v-model="filters.search" placeholder="Search organizations..." />
                
                <button
                    @click="openCreateModal"
                    class="px-5 py-2 bg-[color:var(--primary)] text-[color:var(--primary-text)] font-sans font-semibold text-xs tracking-wide rounded-xl hover:opacity-90 active:scale-[0.98] transition-all duration-200 cursor-pointer select-none shadow-sm"
                >
                    Add Organization
                </button>
            </div>

            <!-- Main Directory Table -->
            <div v-if="organizations.data.length === 0">
                <EmptyState title="No organizations registered" description="Get started by creating your first business tenant." />
            </div>
            
            <div v-else class="space-y-4">
                <DataTable :headers="['ID', 'Name', 'Status', 'Registered At', 'Actions']" :items="organizations.data">
                    <template #row="{ item }">
                        <td class="px-5 py-3 font-mono text-brand-text-muted text-xs">#{{ item.id }}</td>
                        <td class="px-5 py-3 font-semibold text-brand-text">{{ item.name }}</td>
                        <td class="px-5 py-3"><StatusBadge :value="item.status" /></td>
                        <td class="px-5 py-3 text-brand-text-secondary font-mono text-xs">{{ new Date(item.created_at).toLocaleDateString() }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3 font-sans text-xs font-semibold tracking-wide">
                                <button @click="openEditModal(item)" class="text-brand-primary hover:underline transition-all duration-150 cursor-pointer">Edit</button>
                                <button @click="openDeleteModal(item)" class="text-red-600 dark:text-red-400 hover:underline transition-all duration-150 cursor-pointer">Delete</button>
                            </div>
                        </td>
                    </template>
                </DataTable>
                
                <div class="flex justify-end mt-4">
                    <Pagination :links="organizations.meta.links" />
                </div>
            </div>

            <!-- Creation / Edit Modal -->
            <Modal :show="showFormModal" max-width="md" @close="showFormModal = false">
                <form @submit.prevent="submitForm" class="p-6 sm:p-8 text-brand-text-secondary bg-transparent border-none">
                    <h2 class="text-xs font-sans font-bold uppercase tracking-wider text-brand-text border-b border-zinc-200 dark:border-zinc-800 pb-2 mb-5">
                        {{ editingOrg ? 'Modify Organization' : 'Create Organization' }}
                    </h2>
                    
                    <div class="space-y-4">
                        <FormInput
                            id="name"
                            label="Organization Name"
                            v-model="form.name"
                            :error="form.errors.name"
                            required
                            placeholder="e.g. Acme Corporation"
                        />

                        <FormSelect
                            id="status"
                            label="System Status"
                            v-model="form.status"
                            :options="['Active', 'Inactive', 'Suspended']"
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
                            <span v-else>{{ editingOrg ? 'Save changes' : 'Create' }}</span>
                        </button>
                    </div>
                </form>
            </Modal>

            <!-- Delete Confirmation -->
            <ConfirmDialog
                :show="showDeleteDialog"
                title="Remove Organization"
                message="Are you sure you want to delete this organization? All linked users, employees, and payouts will be permanently deleted."
                confirm-text="Delete"
                :processing="form.processing"
                @confirm="confirmDelete"
                @close="showDeleteDialog = false"
            />
        </div>
    </AppLayout>
</template>
