<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { usePermissions } from '@/Composables/usePermissions';

const { hasRole, hasPermission } = usePermissions();
const page = usePage();
const user = page.props.auth.user;

const sidebarOpen = ref(false);
const isCollapsed = ref(false);
const dropdownOpen = ref(false);
const dropdownContainer = ref(null);
const currentTheme = ref('dark');

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const toggleCollapse = () => {
    isCollapsed.value = !isCollapsed.value;
    localStorage.sidebarCollapsed = isCollapsed.value ? 'true' : 'false';
};

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
};

const sidebarContainer = ref(null);

const closeDropdown = (e) => {
    if (dropdownContainer.value && !dropdownContainer.value.contains(e.target)) {
        dropdownOpen.value = false;
    }
};

const closeSidebarOutside = (e) => {
    if (window.innerWidth < 768 && sidebarOpen.value) {
        const toggleBtn = document.querySelector('.hamburger-toggle');
        if (sidebarContainer.value && !sidebarContainer.value.contains(e.target) && (!toggleBtn || !toggleBtn.contains(e.target))) {
            sidebarOpen.value = false;
        }
    }
};

const toggleTheme = () => {
    if (currentTheme.value === 'dark') {
        currentTheme.value = 'light';
        localStorage.theme = 'light';
        document.documentElement.classList.remove('dark');
    } else {
        currentTheme.value = 'dark';
        localStorage.theme = 'dark';
        document.documentElement.classList.add('dark');
    }
};

onMounted(() => {
    document.addEventListener('click', closeDropdown);
    document.addEventListener('click', closeSidebarOutside);
    isCollapsed.value = localStorage.sidebarCollapsed === 'true';
    if (document.documentElement.classList.contains('dark')) {
        currentTheme.value = 'dark';
    } else {
        currentTheme.value = 'light';
    }
});

onUnmounted(() => {
    document.removeEventListener('click', closeDropdown);
    document.removeEventListener('click', closeSidebarOutside);
});
</script>

<template>
    <div class="min-h-screen md:h-screen bg-brand-bg text-brand-text font-sans antialiased flex flex-col md:flex-row relative md:overflow-hidden">
        
        <!-- Mobile Sidebar Backdrop Overlay -->
        <div 
            v-show="sidebarOpen" 
            @click="sidebarOpen = false" 
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-25 md:hidden transition-all duration-300"
        ></div>

        <!-- Sidebar -->
        <aside
            ref="sidebarContainer"
            class="bg-brand-sidebar md:border-b-0 md:border-r border-brand-glass flex flex-col shrink-0 transition-transform md:transition-all duration-300 ease-in-out z-30"
            :class="[
                sidebarOpen 
                    ? 'fixed inset-y-0 left-0 w-72 translate-x-0 shadow-2xl md:relative md:shadow-none md:translate-x-0' 
                    : 'fixed inset-y-0 left-0 w-72 -translate-x-full md:relative md:translate-x-0 md:flex',
                isCollapsed ? 'md:w-20' : 'md:w-64'
            ]"
        >
            <!-- Logo area -->
            <div class="h-16 flex items-center justify-between px-6 border-b border-zinc-200 dark:border-zinc-800" :class="{ 'md:px-4 md:justify-center': isCollapsed }">
                <div class="flex items-center gap-3" v-if="!isCollapsed">
                    <div class="w-8 h-8 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center rounded-xl font-bold text-sm shadow-sm select-none">
                        <span>$</span>
                    </div>
                    <span class="text-xs font-sans font-extrabold uppercase tracking-[0.18em] text-brand-text">PAYOUT.LOG</span>
                </div>
                <div v-else class="w-8 h-8 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center rounded-xl font-bold text-sm shadow-sm select-none">
                    <span>$</span>
                </div>

                <!-- Collapse Toggle Button (Floating Chevron Toggler - Borderless & Transparent) -->
                <button
                    @click="toggleCollapse"
                    class="hidden md:flex items-center justify-center w-5 h-5 rounded-full text-brand-text-secondary hover:text-brand-text hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all duration-150 cursor-pointer focus:outline-none absolute right-[-10px] top-5.5 z-45"
                >
                    <svg v-if="!isCollapsed" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    <svg v-else class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Mobile close button -->
                <button @click="toggleSidebar" class="md:hidden text-brand-text-secondary hover:text-brand-text transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-4 space-y-1.5 font-sans text-xs font-semibold overflow-y-auto" :class="{ 'md:px-2': isCollapsed }">
                <Link
                    :href="route('dashboard')"
                    class="flex items-center rounded-xl border transition-all duration-200 group"
                    :class="[
                        route().current('dashboard') 
                            ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-600 dark:text-emerald-400' 
                            : 'border-transparent text-brand-text-secondary hover:bg-zinc-50 dark:hover:bg-zinc-900/50 hover:text-brand-text',
                        isCollapsed ? 'md:justify-center md:px-0 py-3' : 'px-4 py-3'
                    ]"
                    :title="isCollapsed ? 'Dashboard Overview' : ''"
                >
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 transition-colors duration-200" :class="route().current('dashboard') ? 'text-emerald-500' : 'text-brand-text-muted group-hover:text-brand-text'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                        </svg>
                        <span v-if="!isCollapsed">Dashboard Overview</span>
                    </div>
                </Link>

                <!-- SuperAdmin Only -->
                <Link
                    v-if="hasRole('SuperAdmin')"
                    :href="route('organizations.index')"
                    class="flex items-center rounded-xl border transition-all duration-200 group"
                    :class="[
                        route().current('organizations.*') 
                            ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-600 dark:text-emerald-400' 
                            : 'border-transparent text-brand-text-secondary hover:bg-zinc-50 dark:hover:bg-zinc-900/50 hover:text-brand-text',
                        isCollapsed ? 'md:justify-center md:px-0 py-3' : 'px-4 py-3'
                    ]"
                    :title="isCollapsed ? 'Organizations' : ''"
                >
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 transition-colors duration-200" :class="route().current('organizations.*') ? 'text-emerald-500' : 'text-brand-text-muted group-hover:text-brand-text'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span v-if="!isCollapsed">Organizations</span>
                    </div>
                </Link>

                <!-- SuperAdmin Only: Roles & Permissions -->
                <Link
                    v-if="hasRole('SuperAdmin')"
                    :href="route('roles.index')"
                    class="flex items-center rounded-xl border transition-all duration-200 group"
                    :class="[
                        route().current('roles.*') 
                            ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-600 dark:text-emerald-400' 
                            : 'border-transparent text-brand-text-secondary hover:bg-zinc-50 dark:hover:bg-zinc-900/50 hover:text-brand-text',
                        isCollapsed ? 'md:justify-center md:px-0 py-3' : 'px-4 py-3'
                    ]"
                    :title="isCollapsed ? 'Roles & Permissions' : ''"
                >
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 transition-colors duration-200" :class="route().current('roles.*') ? 'text-emerald-500' : 'text-brand-text-muted group-hover:text-brand-text'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span v-if="!isCollapsed">Roles & Permissions</span>
                    </div>
                </Link>

                <!-- SuperAdmin / Admin -->
                <Link
                    v-if="hasPermission('manage admins')"
                    :href="route('users.index')"
                    class="flex items-center rounded-xl border transition-all duration-200 group"
                    :class="[
                        route().current('users.*') 
                            ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-600 dark:text-emerald-400' 
                            : 'border-transparent text-brand-text-secondary hover:bg-zinc-50 dark:hover:bg-zinc-900/50 hover:text-brand-text',
                        isCollapsed ? 'md:justify-center md:px-0 py-3' : 'px-4 py-3'
                    ]"
                    :title="isCollapsed ? 'Admins & Users' : ''"
                >
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 transition-colors duration-200" :class="route().current('users.*') ? 'text-emerald-500' : 'text-brand-text-muted group-hover:text-brand-text'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span v-if="!isCollapsed">Admins & Users</span>
                    </div>
                </Link>

                <Link
                    v-if="hasPermission('manage employees') || hasRole('Accountant')"
                    :href="route('employees.index')"
                    class="flex items-center rounded-xl border transition-all duration-200 group"
                    :class="[
                        route().current('employees.*') 
                            ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-650 dark:text-emerald-400' 
                            : 'border-transparent text-brand-text-secondary hover:bg-zinc-50 dark:hover:bg-zinc-900/50 hover:text-brand-text',
                        isCollapsed ? 'md:justify-center md:px-0 py-3' : 'px-4 py-3'
                    ]"
                    :title="isCollapsed ? 'Employees' : ''"
                >
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 transition-colors duration-200" :class="route().current('employees.*') ? 'text-emerald-500' : 'text-brand-text-muted group-hover:text-brand-text'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span v-if="!isCollapsed">Employees</span>
                    </div>
                </Link>

                <Link
                    :href="route('payouts.index')"
                    class="flex items-center rounded-xl border transition-all duration-200 group"
                    :class="[
                        route().current('payouts.*') 
                            ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-650 dark:text-emerald-400' 
                            : 'border-transparent text-brand-text-secondary hover:bg-zinc-50 dark:hover:bg-zinc-900/50 hover:text-brand-text',
                        isCollapsed ? 'md:justify-center md:px-0 py-3' : 'px-4 py-3'
                    ]"
                    :title="isCollapsed ? 'Payout Logs' : ''"
                >
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0 transition-colors duration-200" :class="route().current('payouts.*') ? 'text-emerald-500' : 'text-brand-text-muted group-hover:text-brand-text'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span v-if="!isCollapsed">Payout Logs</span>
                    </div>
                </Link>
            </nav>

            <!-- Bottom Brand Details (Simple logo placement) -->
            <div class="mx-4 mb-4 p-3 bg-zinc-50 dark:bg-zinc-900/30 border border-zinc-200 dark:border-zinc-800/80 rounded-2xl flex items-center justify-between gap-3 shadow-sm select-none" :class="{ 'md:mx-2 md:p-1.5 md:justify-center': isCollapsed }">
                <div class="flex items-center gap-2.5 min-w-0" :class="{ 'md:justify-center': isCollapsed }">
                    <div class="w-8 h-8 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center font-sans text-xs font-semibold uppercase rounded-lg shrink-0">
                        {{ user.name.slice(0, 2) }}
                    </div>
                    <div class="flex flex-col text-left min-w-0" v-if="!isCollapsed">
                        <span class="text-[10px] font-sans font-bold text-zinc-900 dark:text-zinc-100 truncate">{{ user.name }}</span>
                        <span class="text-[8px] font-mono font-medium text-zinc-500 dark:text-zinc-400 truncate">@{{ user.name.toLowerCase().replace(' ', '') }}-{{ user.id }}</span>
                    </div>
                </div>
                <Link
                    v-if="!isCollapsed"
                    :href="route('profile.edit')"
                    class="px-2.5 py-1 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-[10px] font-semibold text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded-lg transition-all duration-150 shadow-sm"
                >
                    Edit
                </Link>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 z-10 md:h-screen md:overflow-hidden">
            <!-- Topbar -->
            <header class="sticky top-0 z-20 h-16 bg-brand-sidebar border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between px-6 shrink-0">
                <div class="flex items-center gap-3">
                    <button @click="toggleSidebar" class="md:hidden text-brand-text-secondary hover:text-brand-text transition-all duration-150 hamburger-toggle">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h2 class="text-sm font-semibold tracking-tight text-brand-text">
                        <slot name="header"></slot>
                    </h2>
                </div>

                <!-- User Profile & Dropdown -->
                <div class="relative" ref="dropdownContainer">
                    <button
                        @click="toggleDropdown"
                        class="w-9 h-9 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center font-semibold text-xs uppercase shadow-sm hover:bg-emerald-500/20 active:scale-95 transition-all duration-200 cursor-pointer focus:outline-none"
                    >
                        {{ user.name.slice(0, 2) }}
                    </button>

                    <!-- Dropdown Card (Solid Background, anchored next to the avatar) -->
                    <div
                        v-show="dropdownOpen"
                        class="absolute right-0 mt-2.5 w-64 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl z-50 py-3 overflow-hidden text-left"
                    >
                        <!-- Header with User Details -->
                        <div class="flex flex-col items-center text-center pb-4 border-b border-zinc-100 dark:border-zinc-800/80 mb-3 px-4 select-none">
                            <div class="w-12 h-12 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center font-semibold text-lg uppercase shadow-sm mb-2">
                                {{ user.name.slice(0, 2) }}
                            </div>
                            <h3 class="text-xs font-bold text-brand-text">{{ user.name }}</h3>
                            <p class="text-[9px] text-emerald-500 font-bold uppercase tracking-wider mt-0.5">{{ user.role }}</p>
                            <p class="text-[9px] text-brand-text-muted font-mono mt-0.5">@{{ user.name.toLowerCase().replace(' ', '') }}-{{ user.id }}</p>
                        </div>

                        <!-- Actions List -->
                        <div class="space-y-1 px-2">
                            <!-- Edit Profile -->
                            <Link
                                :href="route('profile.edit')"
                                class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-brand-text-secondary hover:bg-zinc-50 dark:hover:bg-zinc-800 hover:text-brand-text rounded-xl transition-all duration-150 cursor-pointer block text-left"
                                @click="dropdownOpen = false"
                            >
                                <svg class="w-4 h-4 text-brand-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profile Settings
                            </Link>

                            <!-- Theme Switcher -->
                            <button
                                @click="toggleTheme"
                                class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-brand-text-secondary hover:bg-zinc-50 dark:hover:bg-zinc-800 hover:text-brand-text rounded-xl transition-all duration-150 cursor-pointer text-left focus:outline-none"
                            >
                                <template v-if="currentTheme === 'dark'">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.364l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                                    </svg>
                                    Light Theme
                                </template>
                                <template v-else>
                                    <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                    </svg>
                                    Dark Theme
                                </template>
                            </button>

                            <div class="h-[1px] bg-zinc-200 dark:bg-zinc-800 my-1"></div>

                            <!-- Logout -->
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-red-650 hover:bg-red-500/5 dark:hover:bg-red-500/10 rounded-xl transition-all duration-150 cursor-pointer text-left focus:outline-none"
                                @click="dropdownOpen = false"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </Link>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content viewport (Strict Bento Padding) -->
            <main class="flex-1 p-6 overflow-y-auto max-w-[1600px] w-full mx-auto">
                <slot></slot>
            </main>
        </div>
    </div>
</template>
