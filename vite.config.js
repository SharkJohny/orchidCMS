import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // Původní vstupní soubory
            input: ['resources/css/app.css', 'resources/css/platform.css', 'resources/js/app.js', 'resources/js/platform.js'],
            refresh: true,
        }),

    ],
});