<template>
    <button
        class="inline-flex items-center justify-center rounded-xl border border-transparent bg-[color:var(--primary)] text-[color:var(--primary-text)] font-sans text-xs font-semibold tracking-wide py-2.5 px-5 transition-all duration-200 ease-out hover:opacity-90 active:scale-[0.98] hover:shadow-lg hover:shadow-brand-primary/10 disabled:opacity-50 cursor-pointer focus:outline-none select-none"
    >
        <slot />
    </button>
</template>
