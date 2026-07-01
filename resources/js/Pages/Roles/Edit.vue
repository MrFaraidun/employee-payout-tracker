<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FormInput from '@/Components/FormInput.vue';
import PermissionMatrix from '@/Components/PermissionMatrix.vue';
import { useToast } from '@/Composables/useToast';

const props = defineProps({
    role: {
        type: Object,
        default: null,
    },
    permissions: {
        type: Array,
        required: true,
    },
});

const toast = useToast();

const form = useForm({
    name: props.role ? props.role.name : '',
    permissions: props.role ? [...props.role.permissions] : [],
});

const isSystemRole = computed(() => {
    return props.role && ['SuperAdmin', 'Admin', 'Accountant'].includes(props.role.name);
});

const submitForm = () => {
    if (props.role) {
        form.put(route('roles.update', props.role.id), {
            onSuccess: () => {
                toast.success('Role permissions updated successfully.');
            },
        });
    } else {
        form.post(route('roles.store'), {
            onSuccess: () => {
                toast.success('Role created successfully.');
            },
        });
    }
};
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-brand-text-muted">
                    <Link :href="route('roles.index')" class="hover:underline">Roles</Link>
                    <span class="text-zinc-300 dark:text-zinc-700">/</span>
                    <span class="text-brand-text-secondary">{{ role ? 'Edit' : 'Create' }}</span>
                </div>
                <h1 class="text-xl font-bold tracking-tight text-brand-text">Permission Matrix</h1>
            </div>
        </template>
        <Head :title="role ? `Edit Role: ${role.name}` : 'New Role'" />

        <div class="space-y-6">
            <!-- Settings Card (Double-Bezel Bento) -->
            <div class="p-1 bg-zinc-200/40 dark:bg-zinc-800/30 border border-zinc-200/80 dark:border-zinc-800/60 rounded-3xl shadow-sm">
                <div class="p-6 bg-brand-surface rounded-[calc(1.5rem-1px)] space-y-4">
                    <h3 class="text-xs font-sans font-bold uppercase tracking-wider text-brand-text-muted">Role Details</h3>
                    
                    <div class="max-w-md">
                        <FormInput
                            v-model="form.name"
                            label="Role Name"
                            placeholder="e.g. Audit Manager"
                            :error="form.errors.name"
                            :disabled="isSystemRole"
                            required
                        />
                    </div>
                </div>
            </div>

            <!-- Permission Matrix Component -->
            <PermissionMatrix
                v-model="form.permissions"
                :available-permissions="permissions"
            />

            <!-- Footer Save/Cancel Actions -->
            <div class="flex items-center gap-4">
                <button
                    type="button"
                    @click="submitForm"
                    :disabled="form.processing"
                    class="px-5 py-2.5 bg-brand-primary text-white font-sans font-semibold text-xs tracking-wide rounded-xl hover:opacity-90 active:scale-[0.98] transition-all duration-200 cursor-pointer disabled:opacity-50 select-none shadow-sm"
                >
                    {{ form.processing ? 'Saving...' : 'Save changes' }}
                </button>
                
                <Link
                    :href="route('roles.index')"
                    class="px-5 py-2.5 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900/50 text-brand-text-secondary font-sans font-semibold text-xs tracking-wide rounded-xl cursor-pointer select-none"
                >
                    Cancel
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
