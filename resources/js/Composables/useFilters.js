import { router } from '@inertiajs/vue3';
import { watch, reactive } from 'vue';

export function useFilters(initialFilters, routeName) {
    const filters = reactive({ ...initialFilters });

    // Simple pure JS debounce to avoid third-party dependencies (YAGNI / Ponytail)
    const debounceVisit = debounce((currentFilters) => {
        const queryParams = {};
        for (const [key, value] of Object.entries(currentFilters)) {
            if (value !== '' && value !== null && value !== undefined) {
                queryParams[key] = value;
            }
        }

        router.get(
            routeName ? route(routeName) : window.location.pathname,
            queryParams,
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            }
        );
    }, 300);

    watch(
        filters,
        (newVal) => {
            debounceVisit(newVal);
        },
        { deep: true }
    );

    return {
        filters,
    };
}

// Pure JS simple debounce helper
function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}
