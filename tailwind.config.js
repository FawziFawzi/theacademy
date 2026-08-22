import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Manrope', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                canvas: 'var(--color-bg)',
                surface: {
                    DEFAULT: 'var(--color-surface)',
                    raised: 'var(--color-surface-raised)',
                },
                accent: {
                    DEFAULT: 'var(--color-accent)',
                    hover: 'var(--color-accent-hover)',
                    soft: 'var(--color-accent-soft)',
                },
                ink: {
                    DEFAULT: 'var(--color-text)',
                    muted: 'var(--color-text-muted)',
                },
                line: 'var(--color-border)',
                danger: 'var(--color-danger)',
                warning: 'var(--color-warning)',
            },
            borderRadius: {
                card: 'var(--radius-card)',
                pill: 'var(--radius-pill)',
            },
            boxShadow: {
                card: 'var(--shadow-card)',
            },
        },
    },

    plugins: [forms],
};
