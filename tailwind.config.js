import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Inter"', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', '"SF Mono"', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                brand: {
                    bg: 'var(--bg-main)',
                    sidebar: 'var(--bg-sidebar)',
                    input: 'var(--bg-input)',
                    surface: 'var(--bg-surface)',
                    primary: 'var(--primary)',
                    'primary-glow': 'var(--primary-glow)',
                    glass: 'var(--border-glass)',
                    inner: 'var(--border-inner)',
                    'card-from': 'var(--card-from)',
                    'card-to': 'var(--card-to)',
                    text: 'var(--text-primary)',
                    'text-secondary': 'var(--text-secondary)',
                    'text-muted': 'var(--text-muted)',
                },
                // Retain standard surface colors for ease of styling
                surface: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    850: '#131b2e',
                    900: '#0f172a',
                    950: '#040711',
                }
            },
            boxShadow: {
                'premium': '0 10px 40px rgba(0, 0, 0, 0.5)',
                'premium-sm': '0 4px 15px rgba(0, 0, 0, 0.3)',
                'glass-inset': 'inset 0 1px 0 0 rgba(255, 255, 255, 0.05)',
            }
        },
    },

    plugins: [forms],
};
