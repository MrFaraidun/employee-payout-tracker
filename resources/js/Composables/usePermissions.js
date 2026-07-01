import { usePage } from '@inertiajs/vue3';

export function usePermissions() {
    const page = usePage();

    const user = () => page.props.auth?.user;

    const hasPermission = (permission) => {
        const permissions = page.props.auth?.permissions || [];
        return permissions.includes(permission) || user()?.role === 'SuperAdmin';
    };

    const hasRole = (role) => {
        return user()?.role === role;
    };

    return {
        user,
        hasPermission,
        hasRole,
    };
}
