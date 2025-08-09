import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Mendefinisikan warna-warna kustom Anda
                // Sekarang Anda bisa menggunakan kelas seperti 'bg-bps-orange', 'text-bps-green', dll.
                'bps-orange': '#FF6600',
                'bps-gray': '#F5F5F5',
                'bps-green': '#8FD14F',
                'bps-purple': '#604CC3',
                // Anda juga bisa mengganti warna default Tailwind jika mau,
                // misalnya mengganti warna 'orange' bawaan.
                'orange': {
                    50: '#F5F5F5', // Contoh: Mengganti warna orange default
                    100: '#FFF5E0',
                    200: '#FFEBCC',
                    300: '#FFD799',
                    400: '#FFC066',
                    500: '#FF6600', // Warna utama orange Anda
                    600: '#E65C00',
                    700: '#B34700',
                    800: '#803300',
                    900: '#4D1F00',
                },
            },
        },
    },

    plugins: [forms],
};
