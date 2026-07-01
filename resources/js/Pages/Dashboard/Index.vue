<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import DataTable from '@/Components/DataTable.vue';
import EmptyState from '@/Components/EmptyState.vue';

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    recentPayouts: {
        type: Object,
        required: true,
    },
    recentEmployees: {
        type: Object,
        required: true,
    },
    chartData: {
        type: Object,
        required: true,
    },
    recentAuditLogs: {
        type: Array,
        required: true,
    },
});

const formatIQD = (amount) => {
    return new Intl.NumberFormat('en-US').format(amount) + ' IQD';
};

// Calculate SVG Pie/Donut Chart parameters
const chartTotal = Object.values(props.chartData).reduce((sum, count) => sum + count, 0);

// Status color definitions for CSS using theme variables
const getStatusColor = (status) => {
    switch (status.toLowerCase()) {
        case 'completed': return 'var(--success)';
        case 'pending':
        case 'processing': return 'var(--warning)';
        case 'failed': return 'var(--danger)';
        default: return 'var(--text-muted)';
    }
};

const getStatusBgColor = (status) => {
    switch (status.toLowerCase()) {
        case 'completed': return 'rgba(16, 185, 129, 0.1)';
        case 'pending':
        case 'processing': return 'rgba(245, 158, 11, 0.1)';
        case 'failed': return 'rgba(239, 68, 68, 0.1)';
        default: return 'rgba(113, 113, 122, 0.1)';
    }
};

const getActionBadgeClass = (action) => {
    switch (action.toLowerCase()) {
        case 'created':
        case 'onboarded':
            return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20';
        case 'updated':
        case 'edited':
            return 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20';
        case 'deleted':
        case 'archived':
            return 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20';
        default:
            return 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border-zinc-500/20';
    }
};
</script>

<template>
    <AppLayout>
        <template #header>Dashboard Overview</template>
        <Head title="Dashboard" />

        <div class="space-y-8 py-4 max-w-[1600px] mx-auto">
            
            <!-- Stats Bento Grid (Tactile Double-Bezel Design) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Highlight Card: Total Completed Payouts -->
                <div class="p-1 bg-gradient-to-br from-emerald-500/15 to-teal-500/5 dark:from-emerald-500/10 dark:to-zinc-800/10 border border-emerald-500/25 dark:border-emerald-500/15 rounded-3xl shadow-sm transition-all duration-300 hover:scale-[1.01] hover:shadow-lg hover:shadow-emerald-500/5 group">
                    <div class="p-6 bg-brand-surface rounded-[calc(1.5rem-1px)] h-full flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <div class="space-y-2.5">
                                <span class="text-[10px] font-sans font-bold uppercase tracking-[0.15em] text-brand-text-muted">Total Completed Payouts</span>
                                <div class="text-2xl sm:text-3xl font-mono font-extrabold text-emerald-600 dark:text-emerald-400 tracking-tight transition-transform duration-300 group-hover:translate-x-1">
                                    {{ formatIQD(stats.total_amount_iqd) }}
                                </div>
                            </div>
                            
                            <!-- Premium SVG Sparkline -->
                            <div class="w-16 h-8 shrink-0 pb-1 text-emerald-500/80 group-hover:text-emerald-500 transition-colors duration-300">
                                <svg viewBox="0 0 100 30" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
                                    <path d="M0,25 Q15,5 35,20 T70,5 T100,15" />
                                </svg>
                            </div>
                        </div>
                        <span class="text-[10px] text-brand-text-muted font-sans uppercase tracking-wider mt-6 flex items-center gap-2 border-t border-zinc-100 dark:border-zinc-800/60 pt-3">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Live Audited Balance
                        </span>
                    </div>
                </div>
                
                <!-- Pending Approvals Card -->
                <div class="p-1 bg-zinc-200/40 dark:bg-zinc-800/30 border border-zinc-200/80 dark:border-zinc-800/60 rounded-3xl shadow-sm transition-all duration-300 hover:scale-[1.01] hover:shadow-lg hover:shadow-amber-500/5 group">
                    <div class="p-6 bg-brand-surface rounded-[calc(1.5rem-1px)] h-full flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <div class="space-y-2.5">
                                <span class="text-[10px] font-sans font-bold uppercase tracking-[0.15em] text-brand-text-muted">Pending Approvals</span>
                                <div class="text-2xl sm:text-3xl font-mono font-extrabold text-amber-500 tracking-tight transition-transform duration-300 group-hover:translate-x-1">
                                    {{ stats.pending_count }}
                                </div>
                            </div>
                            
                            <!-- Premium SVG Sparkline (Warning / Pending) -->
                            <div class="w-16 h-8 shrink-0 pb-1 text-amber-500/80 group-hover:text-amber-500 transition-colors duration-300">
                                <svg viewBox="0 0 100 30" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
                                    <path d="M0,10 Q20,25 40,10 T80,20 T100,5" />
                                </svg>
                            </div>
                        </div>
                        <span class="text-[10px] text-brand-text-muted font-sans uppercase tracking-wider mt-6 flex items-center gap-2 border-t border-zinc-100 dark:border-zinc-800/60 pt-3">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Awaiting settlement approval
                        </span>
                    </div>
                </div>

                <!-- Processing Payouts Card -->
                <div class="p-1 bg-zinc-200/40 dark:bg-zinc-800/30 border border-zinc-200/80 dark:border-zinc-800/60 rounded-3xl shadow-sm transition-all duration-300 hover:scale-[1.01] hover:shadow-lg hover:shadow-blue-500/5 group">
                    <div class="p-6 bg-brand-surface rounded-[calc(1.5rem-1px)] h-full flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <div class="space-y-2.5">
                                <span class="text-[10px] font-sans font-bold uppercase tracking-[0.15em] text-brand-text-muted">Processing Logs</span>
                                <div class="text-2xl sm:text-3xl font-mono font-extrabold text-blue-500 tracking-tight transition-transform duration-300 group-hover:translate-x-1">
                                    {{ stats.processing_count }}
                                </div>
                            </div>
                            
                            <!-- Premium SVG Sparkline (Processing) -->
                            <div class="w-16 h-8 shrink-0 pb-1 text-blue-500/80 group-hover:text-blue-500 transition-colors duration-300">
                                <svg viewBox="0 0 100 30" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-full h-full">
                                    <path d="M0,20 Q15,5 35,25 T75,10 T100,20" />
                                </svg>
                            </div>
                        </div>
                        <span class="text-[10px] text-brand-text-muted font-sans uppercase tracking-wider mt-6 flex items-center gap-2 border-t border-zinc-100 dark:border-zinc-800/60 pt-3">
                            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                            Active transfer cycles
                        </span>
                    </div>
                </div>
            </div>

            <!-- Lower Layout: Recent Data, Lists & SVG Chart (Strict 24px Gap) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Column 1: Recent Payout Logs (Span 6) -->
                <div class="lg:col-span-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                        <div class="space-y-1">
                            <h2 class="text-xs font-sans font-bold uppercase tracking-wider text-brand-text">Recent Payout Logs</h2>
                            <p class="text-[10px] text-brand-text-muted leading-none">Latest logged payout cycles for verified contractors</p>
                        </div>
                        <Link :href="route('payouts.index')" class="text-[10px] font-sans font-extrabold uppercase tracking-[0.1em] text-emerald-600 dark:text-emerald-400 hover:underline transition-all duration-150">View All</Link>
                    </div>

                    <div v-if="recentPayouts.data.length === 0">
                        <EmptyState title="No payouts logged yet" description="Click on Payouts to log your first payment record." />
                    </div>
                    <div v-else class="p-1 bg-zinc-200/40 dark:bg-zinc-800/30 border border-zinc-200/80 dark:border-zinc-800/60 rounded-3xl shadow-sm">
                        <DataTable :headers="['Employee', 'Task Description', 'Amount', 'Status']" :items="recentPayouts.data">
                            <template #row="{ item }">
                                <td class="px-5 py-3.5 font-sans font-semibold text-brand-text">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center font-bold text-[10px] uppercase select-none">
                                            {{ item.employee?.name.slice(0, 2) || 'NA' }}
                                        </div>
                                        <span>{{ item.employee?.name || 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 max-w-[200px] truncate text-brand-text-secondary">{{ item.task }}</td>
                                <td class="px-5 py-3.5 font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ formatIQD(item.amount_iqd) }}</td>
                                <td class="px-5 py-3.5"><StatusBadge :value="item.status" /></td>
                            </template>
                        </DataTable>
                    </div>
                </div>

                <!-- Column 2: Chart Breakdown & Recent Employees (Span 3) -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Status Chart -->
                    <div class="p-1 bg-zinc-200/40 dark:bg-zinc-800/30 border border-zinc-200/80 dark:border-zinc-800/60 rounded-3xl shadow-sm">
                        <div class="p-5 bg-brand-surface rounded-[calc(1.5rem-1px)]">
                            <h2 class="text-xs font-sans font-bold uppercase tracking-wider text-brand-text border-b border-zinc-100 dark:border-zinc-800/60 pb-3 mb-4">
                                Status Distribution
                            </h2>

                            <div v-if="chartTotal === 0" class="text-center py-6 text-xs font-sans text-brand-text-muted uppercase">
                                No chart data available
                            </div>
                            <div v-else class="space-y-4">
                                <div v-for="(count, status) in chartData" :key="status" class="space-y-2">
                                    <div class="flex items-center justify-between text-[10px] font-semibold uppercase tracking-wider text-brand-text-secondary">
                                        <span class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full" :style="{ backgroundColor: getStatusColor(status) }"></span>
                                            {{ status }}
                                        </span>
                                        <span class="font-mono text-brand-text-muted font-bold">{{ count }} ({{ Math.round((count / chartTotal) * 100) }}%)</span>
                                    </div>
                                    <div class="w-full bg-zinc-100 dark:bg-zinc-900/50 h-2 rounded-full overflow-hidden border border-zinc-200/60 dark:border-zinc-800/60">
                                        <div
                                            class="h-full transition-all duration-500 ease-out rounded-full"
                                            :style="{
                                                width: `${(count / chartTotal) * 100}%`,
                                                backgroundColor: getStatusColor(status)
                                            }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Employees List -->
                    <div class="p-1 bg-zinc-200/40 dark:bg-zinc-800/30 border border-zinc-200/80 dark:border-zinc-800/60 rounded-3xl shadow-sm">
                        <div class="p-5 bg-brand-surface rounded-[calc(1.5rem-1px)]">
                            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/60 pb-3 mb-3">
                                <h2 class="text-xs font-sans font-bold uppercase tracking-wider text-brand-text">Recent Employees</h2>
                                <Link :href="route('employees.index')" class="text-[10px] font-sans font-extrabold uppercase tracking-[0.1em] text-emerald-600 dark:text-emerald-400 hover:underline transition-all duration-150">All</Link>
                            </div>

                            <div v-if="recentEmployees.data.length === 0" class="text-center py-4 text-xs font-sans text-brand-text-muted uppercase">
                                No employees onboarded
                            </div>
                            <div v-else class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                                <div v-for="emp in recentEmployees.data" :key="emp.id" class="py-3 flex items-center justify-between text-xs font-sans">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-7 h-7 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center font-bold text-[10px] uppercase shrink-0 select-none">
                                            {{ emp.name.slice(0, 2) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-brand-text truncate">{{ emp.name }}</div>
                                            <div class="text-[8px] text-brand-text-muted uppercase tracking-wider mt-0.5 truncate">{{ emp.department }}</div>
                                        </div>
                                    </div>
                                    <StatusBadge :value="emp.status" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column 3: Recent Activities (Live Timeline Span 3) -->
                <div class="lg:col-span-3 space-y-6">
                    <div class="p-1 bg-zinc-200/40 dark:bg-zinc-800/30 border border-zinc-200/80 dark:border-zinc-800/60 rounded-3xl shadow-sm h-full">
                        <div class="p-5 bg-brand-surface rounded-[calc(1.5rem-1px)] h-full flex flex-col">
                            <h2 class="text-xs font-sans font-bold uppercase tracking-wider text-brand-text border-b border-zinc-100 dark:border-zinc-800/60 pb-3 mb-4">
                                Live Audit Feed
                            </h2>

                            <div v-if="recentAuditLogs.length === 0" class="text-center py-6 text-xs font-sans text-brand-text-muted uppercase flex-1 flex items-center justify-center">
                                No activities logged
                            </div>
                            
                            <!-- Timeline Feed -->
                            <div v-else class="relative flex-1 pl-4 space-y-5">
                                <!-- Vertical Connective Line -->
                                <div class="absolute left-1.5 top-2.5 bottom-2.5 w-[1.5px] bg-zinc-150 dark:bg-zinc-800/80"></div>
                                
                                <div v-for="log in recentAuditLogs" :key="log.id" class="relative text-left group">
                                    <!-- Timeline Node Circle -->
                                    <div 
                                        class="absolute -left-[19.5px] top-1.5 w-2.5 h-2.5 rounded-full border border-brand-surface shadow-sm transition-transform duration-300 group-hover:scale-125 z-10"
                                        :style="{ backgroundColor: getStatusColor(log.action === 'created' ? 'completed' : (log.action === 'updated' ? 'pending' : 'failed')) }"
                                    ></div>
                                    
                                    <div class="flex flex-col gap-1.5 text-xs">
                                        <!-- Header Block: Action Type & Timestamp -->
                                        <div class="flex items-center justify-between">
                                            <span 
                                                class="px-2 py-0.5 rounded border text-[8px] font-sans font-extrabold uppercase tracking-wider"
                                                :class="getActionBadgeClass(log.action)"
                                            >
                                                {{ log.action }}
                                            </span>
                                            <span class="font-mono text-[9px] text-brand-text-muted font-semibold">
                                                {{ new Date(log.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}
                                            </span>
                                        </div>
                                        
                                        <!-- Log Text Details -->
                                        <p class="text-[10px] text-brand-text-secondary leading-relaxed font-sans">
                                            User <span class="font-bold text-brand-text font-mono">#{{ log.user_id }}</span> 
                                            <span class="font-medium text-brand-text">({{ log.user?.name || 'System' }})</span> 
                                            modified details for 
                                            <span class="font-semibold text-brand-text">{{ log.model_type ? log.model_type.split('\\').pop() : 'System Record' }}</span>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
