<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import SearchInput from '@/Components/SearchInput.vue';
import EmptyState from '@/Components/EmptyState.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import { useToast } from '@/Composables/useToast';

const props = defineProps({
    roles: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const toast = useToast();
const searchQuery = ref('');
const showDeleteDialog = ref(false);
const roleToDelete = ref(null);

const isSuperAdmin = computed(() => page.props.auth.user?.role === 'SuperAdmin');
const isAdmin = computed(() => page.props.auth.user?.role === 'Admin');
const hasCreatePermission = computed(() => page.props.auth.permissions.includes('create roles') || isSuperAdmin.value || isAdmin.value);
const hasEditPermission = computed(() => page.props.auth.permissions.includes('update roles') || isSuperAdmin.value || isAdmin.value);
const hasDeletePermission = computed(() => page.props.auth.permissions.includes('delete roles') || isSuperAdmin.value || isAdmin.value);

console.log('Roles/Index page props auth:', page.props.auth);
console.log('isSuperAdmin:', isSuperAdmin.value, 'isAdmin:', isAdmin.value);
console.log('hasEditPermission:', hasEditPermission.value, 'hasDeletePermission:', hasDeletePermission.value);

const filteredRoles = computed(() => {
    if (!searchQuery.value) return props.roles;
    const query = searchQuery.value.toLowerCase();
    return props.roles.filter(role => role.name.toLowerCase().includes(query));
});

const isSystemRole = (name) => {
    return ['SuperAdmin', 'Admin', 'Accountant'].includes(name);
};

const openDeleteModal = (role) => {
    roleToDelete.value = role;
    showDeleteDialog.value = true;
};

const confirmDelete = () => {
    router.delete(route('roles.destroy', roleToDelete.value.id), {
        onSuccess: () => {
            toast.success('Role deleted successfully.');
            showDeleteDialog.value = false;
            roleToDelete.value = null;
        },
    });
};
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-brand-text-muted">
                    <span>Roles</span>
                    <span class="text-zinc-300 dark:text-zinc-700">/</span>
                    <span class="text-brand-text-secondary">List</span>
                </div>
                <h1 class="text-xl font-bold tracking-tight text-brand-text">Roles Management</h1>
            </div>
        </template>
        <Head title="Roles & Access" />

        <div class="space-y-6">
            <!-- Header Search & Create Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-brand-surface border border-zinc-200 dark:border-zinc-800 p-4 rounded-2xl shadow-sm">
                <div class="w-full sm:max-w-xs">
                    <SearchInput v-model="searchQuery" placeholder="Search roles..." />
                </div>
                
                <Link
                    v-if="hasCreatePermission"
                    :href="route('roles.create')"
                    class="px-5 py-2.5 bg-brand-primary text-white font-sans font-semibold text-xs tracking-wide rounded-xl hover:opacity-90 active:scale-[0.98] transition-all duration-200 cursor-pointer select-none whitespace-nowrap shadow-sm text-center self-start sm:self-auto"
                >
                    New Role
                </Link>
            </div>

            <!-- Roles Table -->
            <div v-if="filteredRoles.length === 0">
                <EmptyState title="No roles found" description="Create a custom role or refine your search query." />
            </div>

            <div v-else class="space-y-4">
                <DataTable :headers="['Role Name', 'Users Count', 'Granted Permissions', 'Updated At', 'Actions']" :items="filteredRoles">
                    <template #row="{ item }">
                        <td class="px-5 py-4">
                            <div class="flex flex-col">
                                <span class="font-semibold text-brand-text text-sm">{{ item.name }}</span>
                                <span
                                    v-if="isSystemRole(item.name)"
                                    class="text-[9px] font-extrabold uppercase tracking-wider px-1.5 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-brand-text-muted mt-1 w-max"
                                >
                                    System Role
                                </span>
                                <span
                                    v-else
                                    class="text-[9px] font-extrabold uppercase tracking-wider px-1.5 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 mt-1 w-max"
                                >
                                    Custom Role
                                </span>
                            </div>
                        </td>
                        
                        <td class="px-5 py-4 text-xs font-semibold text-brand-text-secondary">
                            {{ item.users_count }}
                        </td>
                        
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wide bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg">
                                {{ item.permissions_count }} Permissions
                            </span>
                        </td>
                        
                        <td class="px-5 py-4 text-xs font-mono text-brand-text-muted">
                            {{ item.updated_at }}
                        </td>
                        
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-4 text-brand-text-muted">
                                <!-- Edit Role -->
                                <Link
                                    v-if="hasEditPermission"
                                    :href="route('roles.edit', item.id)"
                                    class="hover:text-emerald-500 transition-colors duration-150"
                                    title="Edit Permission Matrix"
                                >
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </Link>

                                <!-- Delete Role -->
                                <button
                                    v-if="hasDeletePermission"
                                    @click="openDeleteModal(item)"
                                    class="hover:text-red-500 transition-colors duration-150 cursor-pointer focus:outline-none"
                                    title="Delete Role"
                                >
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </template>
                </DataTable>
            </div>
        </div>

        <!-- Confirm Delete Dialog -->
        <ConfirmDialog
            v-model="showDeleteDialog"
            title="Delete Custom Role"
            :message="`Are you sure you want to delete the role '${roleToDelete?.name}'? Users assigned to this role will lose their granted permissions.`"
            confirm-text="Delete Role"
            @confirm="confirmDelete"
        />
    </AppLayout>
</template>
