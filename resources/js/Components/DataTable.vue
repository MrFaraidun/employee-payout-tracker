<script setup>
defineProps({
    headers: {
        type: Array,
        required: true,
    },
    items: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <!-- Premium Rounded Table Shell -->
    <div class="w-full overflow-x-auto bg-brand-surface border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm">
        <table class="w-full text-left border-collapse rounded-2xl overflow-hidden">
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-800">
                    <th
                        v-for="(header, index) in headers"
                        :key="index"
                        class="px-5 py-3.5 first:pl-6 last:pr-6 bg-zinc-50 dark:bg-zinc-900/80 text-brand-text-muted font-sans text-[10px] font-semibold uppercase tracking-wider select-none"
                    >
                        {{ typeof header === 'object' ? header.label : header }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 font-sans text-xs text-brand-text-secondary bg-brand-surface">
                <tr
                    v-for="(item, index) in items"
                    :key="index"
                    class="hover:bg-emerald-500/[0.04] dark:hover:bg-emerald-500/[0.06] hover:text-brand-text transition-all duration-150 ease-out"
                >
                    <slot name="row" :item="item" :index="index"></slot>
                </tr>
            </tbody>
        </table>
    </div>
</template>
