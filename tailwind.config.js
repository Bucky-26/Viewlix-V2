import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './node_modules/preline/dist/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Instrument Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'viewlix-dark': '#0d0d0d',
                'viewlix-darker': '#080808',
                'viewlix-gray': '#1a1a1a',
                'viewlix-gray-light': '#2a2a2a',
                'viewlix-border': '#2a2a2a',
                'viewlix-red': '#e50914',
                'viewlix-red-dark': '#b20710',
                'viewlix-red-light': '#f40612',
            },
        },
    },

    plugins: [forms],
};
