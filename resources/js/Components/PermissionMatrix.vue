<script setup>
import { ref, computed } from 'vue';
import { useToast } from '@/Composables/useToast';

const props = defineProps({
    modelValue: {
        type: Array,
        required: true,
    },
    availablePermissions: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['update:modelValue']);

const toast = useToast();
const currentTab = ref('platform');
const searchQuery = ref('');

// Define Tab Group structure mapping to Spatie permission names
const tabGroups = [
    {
        id: 'platform',
        name: 'Platform',
        description: 'Panel access, dashboards, reporting, and audit visibility.',
        permissions: [
            {
                label: 'Organizations & Settings',
                viewPerm: 'view organizations',
                createPerm: 'create organizations',
                updatePerm: 'update organizations',
                deletePerm: 'delete organizations'
            }
        ]
    },
    {
        id: 'users',
        name: 'Users & Access',
        description: 'Manage administrator accounts, workspace roles, and team permissions.',
        permissions: [
            {
                label: 'Admins & User Accounts',
                viewPerm: 'view admins',
                createPerm: 'create admins',
                updatePerm: 'update admins',
                deletePerm: 'delete admins'
            },
            {
                label: 'Roles & Permissions',
                viewPerm: 'view roles',
                createPerm: 'create roles',
                updatePerm: 'update roles',
                deletePerm: 'delete roles'
            }
        ]
    },
    {
        id: 'operations',
        name: 'Operations',
        description: 'Core directories, employee profiles, monthly logs, and payment transactions.',
        permissions: [
            {
                label: 'Employee Directory',
                viewPerm: 'view employees',
                createPerm: 'create employees',
                updatePerm: 'update employees',
                deletePerm: 'delete employees'
            },
            {
                label: 'Payout Logs & Ledgers',
                viewPerm: 'view payouts',
                createPerm: 'create payouts',
                updatePerm: 'update payouts',
                deletePerm: 'delete payouts'
            }
        ]
    }
];

const getTabCount = (tabId) => {
    const tab = tabGroups.find(t => t.id === tabId);
    return tab ? tab.permissions.length : 0;
};

const activeTab = computed(() => {
    return tabGroups.find(t => t.id === currentTab.value);
});

// Computed list of permissions to show, filtered by search query
const filteredPermissions = computed(() => {
    if (!searchQuery.value) return activeTab.value.permissions;
    const query = searchQuery.value.toLowerCase();
    return activeTab.value.permissions.filter(p => {
        return p.label.toLowerCase().includes(query);
    });
});

const togglePermission = (permName) => {
    const nextPerms = [...props.modelValue];
    if (nextPerms.includes(permName)) {
        emit('update:modelValue', nextPerms.filter(p => p !== permName));
    } else {
        nextPerms.push(permName);
        emit('update:modelValue', nextPerms);
    }
};

const selectAllPermissions = () => {
    const allNames = props.availablePermissions.map(p => p.name);
    emit('update:modelValue', allNames);
    toast.success('Selected all permissions.');
};

const clearAllPermissions = () => {
    emit('update:modelValue', []);
    toast.success('Cleared all permissions.');
};

const selectAllTabPermissions = () => {
    const nextPerms = [...props.modelValue];
    activeTab.value.permissions.forEach(p => {
        [p.viewPerm, p.createPerm, p.updatePerm, p.deletePerm].forEach(name => {
            if (name && !nextPerms.includes(name)) {
                nextPerms.push(name);
            }
        });
    });
    emit('update:modelValue', nextPerms);
    toast.success(`Selected all permissions in ${activeTab.value.name}.`);
};

const clearTabPermissions = () => {
    const tabPerms = [];
    activeTab.value.permissions.forEach(p => {
        tabPerms.push(p.viewPerm, p.createPerm, p.updatePerm, p.deletePerm);
    });
    emit('update:modelValue', props.modelValue.filter(p => !tabPerms.includes(p)));
    toast.success(`Cleared permissions in ${activeTab.value.name}.`);
};
</script>

<template>
    <!-- Matrix Card Container (Double-Bezel Bento) -->
    <div class="p-1 border shadow-sm bg-zinc-200/40 dark:bg-zinc-800/30 border-zinc-200/80 dark:border-zinc-800/60 rounded-3xl">
        <div class="bg-brand-surface rounded-[calc(1.5rem-1px)] overflow-hidden">
            <!-- Search & Global Matrix Actions -->
            <div class="flex flex-col justify-between gap-4 p-4 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/20 dark:bg-zinc-900/10 md:flex-row md:items-center">
                <div class="relative w-full max-w-xs group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-text-muted group-focus-within:text-brand-primary transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Search by label or action..."
                        class="block w-full pl-9 pr-3.5 py-2.5 bg-brand-input text-brand-text border border-zinc-200 dark:border-zinc-800 text-xs rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all duration-200 ease-out"
                    />
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <button
                        type="button"
                        @click="selectAllPermissions"
                        class="px-4 py-2.5 bg-brand-primary text-white font-sans font-semibold text-xs tracking-wide rounded-xl hover:opacity-90 active:scale-[0.98] transition-all duration-200 cursor-pointer select-none shadow-sm"
                    >
                        Select all permissions
                    </button>

                    <button
                        type="button"
                        @click="clearAllPermissions"
                        class="px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900/50 text-brand-text-secondary font-sans font-semibold text-xs rounded-xl cursor-pointer select-none transition-all duration-150"
                    >
                        Clear all permissions
                    </button>
                </div>
            </div>

            <!-- Tab Menu Header -->
            <div class="flex gap-6 px-4 overflow-x-auto border-b border-zinc-200 dark:border-zinc-800">
                <button
                    v-for="tab in tabGroups"
                    :key="tab.id"
                    type="button"
                    @click="currentTab = tab.id"
                    class="py-4 border-b-2 font-sans text-xs font-bold transition-all duration-200 cursor-pointer select-none whitespace-nowrap flex items-center gap-2 -mb-[1px]"
                    :class="[
                        currentTab === tab.id
                            ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400'
                            : 'border-transparent text-brand-text-muted hover:text-brand-text'
                    ]"
                >
                    <span>{{ tab.name }}</span>
                    <span
                        class="px-1.5 py-0.5 text-[9px] font-extrabold rounded-md"
                        :class="[
                            currentTab === tab.id
                                ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                                : 'bg-zinc-100 dark:bg-zinc-800 text-brand-text-muted'
                        ]"
                    >
                        {{ getTabCount(tab.id) }}
                    </span>
                </button>
            </div>

            <!-- Active Matrix Subtitle Info -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/10 dark:bg-zinc-900/5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-[11px] text-brand-text-secondary leading-relaxed">
                <span>{{ activeTab.description }}</span>
                <div class="flex items-center gap-3 shrink-0">
                    <button
                        type="button"
                        @click="selectAllTabPermissions"
                        class="font-bold cursor-pointer text-emerald-600 dark:text-emerald-400 hover:underline"
                    >
                        Select all tab actions
                    </button>
                    <span class="text-zinc-200 dark:text-zinc-800">|</span>
                    <button
                        type="button"
                        @click="clearTabPermissions"
                        class="font-semibold cursor-pointer text-brand-text-muted hover:text-brand-text hover:underline"
                    >
                        Clear tab actions
                    </button>
                </div>
            </div>

            <!-- Matrix Table Grid -->
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] text-left border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-800">
                            <th class="px-6 py-4 text-[10px] font-extrabold uppercase tracking-wider text-brand-text-muted font-sans w-2/5">
                                Resource / feature Name
                            </th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-center uppercase tracking-wider text-brand-text-muted font-sans w-3/20">
                                View
                            </th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-center uppercase tracking-wider text-brand-text-muted font-sans w-3/20">
                                Create
                            </th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-center uppercase tracking-wider text-brand-text-muted font-sans w-3/20">
                                Update
                            </th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-center uppercase tracking-wider text-brand-text-muted font-sans w-3/20">
                                Delete
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        <tr
                            v-for="item in filteredPermissions"
                            :key="item.label"
                            class="transition-colors duration-150 hover:bg-zinc-50/40 dark:hover:bg-zinc-900/10"
                        >
                            <!-- Resource Label -->
                            <td class="px-6 py-5">
                                <span class="text-xs font-bold text-brand-text">{{ item.label }}</span>
                            </td>

                            <!-- VIEW Checkbox -->
                            <td class="px-6 py-5 text-center">
                                <input
                                    type="checkbox"
                                    :checked="modelValue.includes(item.viewPerm)"
                                    @change="togglePermission(item.viewPerm)"
                                    class="h-5 w-5 rounded border-zinc-350 dark:border-zinc-700 text-emerald-500 focus:ring-emerald-500/10 focus:ring-4 cursor-pointer transition-all duration-150"
                                />
                            </td>

                            <!-- CREATE Checkbox -->
                            <td class="px-6 py-5 text-center">
                                <input
                                    type="checkbox"
                                    :checked="modelValue.includes(item.createPerm)"
                                    @change="togglePermission(item.createPerm)"
                                    class="h-5 w-5 rounded border-zinc-350 dark:border-zinc-700 text-emerald-500 focus:ring-emerald-500/10 focus:ring-4 cursor-pointer transition-all duration-150"
                                />
                            </td>

                            <!-- UPDATE Checkbox -->
                            <td class="px-6 py-5 text-center">
                                <input
                                    type="checkbox"
                                    :checked="modelValue.includes(item.updatePerm)"
                                    @change="togglePermission(item.updatePerm)"
                                    class="h-5 w-5 rounded border-zinc-350 dark:border-zinc-700 text-emerald-500 focus:ring-emerald-500/10 focus:ring-4 cursor-pointer transition-all duration-150"
                                />
                            </td>

                            <!-- DELETE Checkbox -->
                            <td class="px-6 py-5 text-center">
                                <input
                                    type="checkbox"
                                    :checked="modelValue.includes(item.deletePerm)"
                                    @change="togglePermission(item.deletePerm)"
                                    class="h-5 w-5 rounded border-zinc-350 dark:border-zinc-700 text-emerald-500 focus:ring-emerald-500/10 focus:ring-4 cursor-pointer transition-all duration-150"
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
