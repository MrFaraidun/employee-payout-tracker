<script setup>
import { useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({
    employees: { type: Array, required: true },
    payout: { type: Object, default: null }
});

const emit = defineEmits(['payout-saved']);

const form = useForm({
    employee_id: '',
    task: '',
    amount_iqd: '',
    status: 'pending'
});

onMounted(() => {
    if (props.payout) {
        form.employee_id = props.payout.employee_id;
        form.task = props.payout.task;
        form.amount_iqd = props.payout.amount_iqd;
        form.status = props.payout.status;
    }
});

const submit = () => {
    if (props.payout) {
        form.put(route('payouts.update', props.payout.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit('payout-saved', 'Payout updated successfully!');
            }
        });
    } else {
        form.post(route('payouts.store'), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset('task', 'amount_iqd');
                emit('payout-saved', 'Payout logged successfully!');
            }
        });
    }
};
</script>

<template>
    <div class="bg-white dark:bg-surface-900 border border-surface-200 dark:border-surface-800 p-6">
        <div class="mb-6 pb-4 border-b border-surface-200 dark:border-surface-800">
            <h2 class="text-sm font-semibold text-surface-900 dark:text-white">
                {{ payout ? 'Edit Payout' : 'Log New Payout' }}
            </h2>
            <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                {{ payout ? 'Update the details of this transaction.' : 'Record a new payout transaction.' }}
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <!-- Employee -->
            <div>
                <label for="employee_id" class="block text-[11px] font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1.5">Employee</label>
                <div class="relative">
                    <select
                        id="employee_id"
                        v-model="form.employee_id"
                        class="w-full bg-surface-50 dark:bg-surface-950 border border-surface-200 dark:border-surface-700 px-3 py-2.5 text-sm text-surface-900 dark:text-surface-100 focus:border-accent dark:focus:border-accent focus:ring-1 focus:ring-accent/30 transition-colors duration-150 appearance-none cursor-pointer"
                        required
                    >
                        <option value="" disabled>Select employee...</option>
                        <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                            {{ employee.name }}
                        </option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-surface-400">
                        <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                    </div>
                </div>
                <span v-if="form.errors.employee_id" class="block text-[11px] text-rose-500 mt-1">{{ form.errors.employee_id }}</span>
            </div>

            <!-- Task -->
            <div>
                <label for="task" class="block text-[11px] font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1.5">Task Description</label>
                <input
                    type="text"
                    id="task"
                    v-model="form.task"
                    placeholder="e.g. Code Review, UI Redesign"
                    class="w-full bg-surface-50 dark:bg-surface-950 border border-surface-200 dark:border-surface-700 px-3 py-2.5 text-sm text-surface-900 dark:text-surface-100 placeholder:text-surface-400 dark:placeholder:text-surface-600 focus:border-accent dark:focus:border-accent focus:ring-1 focus:ring-accent/30 transition-colors duration-150"
                    required
                />
                <span v-if="form.errors.task" class="block text-[11px] text-rose-500 mt-1">{{ form.errors.task }}</span>
            </div>

            <!-- Amount -->
            <div>
                <label for="amount_iqd" class="block text-[11px] font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1.5">Amount (IQD)</label>
                <div class="relative">
                    <input
                        type="number"
                        id="amount_iqd"
                        v-model="form.amount_iqd"
                        placeholder="150000"
                        min="1"
                        class="w-full bg-surface-50 dark:bg-surface-950 border border-surface-200 dark:border-surface-700 pl-3 pr-14 py-2.5 text-sm font-mono font-semibold text-surface-900 dark:text-surface-100 placeholder:text-surface-400 dark:placeholder:text-surface-600 focus:border-accent dark:focus:border-accent focus:ring-1 focus:ring-accent/30 transition-colors duration-150"
                        required
                    />
                    <span class="absolute inset-y-0 right-3 flex items-center text-[10px] font-medium text-surface-400 uppercase tracking-wider">IQD</span>
                </div>
                <span v-if="form.errors.amount_iqd" class="block text-[11px] text-rose-500 mt-1">{{ form.errors.amount_iqd }}</span>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-[11px] font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400 mb-1.5">Status</label>
                <div class="relative">
                    <select
                        id="status"
                        v-model="form.status"
                        class="w-full bg-surface-50 dark:bg-surface-950 border border-surface-200 dark:border-surface-700 px-3 py-2.5 text-sm text-surface-900 dark:text-surface-100 focus:border-accent dark:focus:border-accent focus:ring-1 focus:ring-accent/30 transition-colors duration-150 appearance-none cursor-pointer"
                        required
                    >
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-surface-400">
                        <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                    </div>
                </div>
                <span v-if="form.errors.status" class="block text-[11px] text-rose-500 mt-1">{{ form.errors.status }}</span>
            </div>

            <!-- Submit -->
            <button
                type="submit"
                :disabled="form.processing"
                class="w-full bg-accent hover:bg-accent-dark text-white font-semibold text-xs uppercase tracking-wider py-3 px-4 cursor-pointer transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed select-none active:opacity-90 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-surface-900"
            >
                {{ form.processing ? 'Processing...' : (payout ? 'Update Payout' : 'Submit Payout') }}
            </button>
        </form>
    </div>
</template>
