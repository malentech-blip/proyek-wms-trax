// tailwind.config.js
const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'brand': {
                    'light': '#ecfdf5', // very light green
                    'DEFAULT': '#22c55e', // primary green
                    'dark': '#15803d',   // dark green
                },
                'primary': '#111827', // dark gray for text
                'secondary': '#6b7280', // medium gray for secondary text
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};