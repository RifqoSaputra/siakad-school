import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/design-system.css',
                'resources/js/app.js',
                'resources/css/components.css',
                'resources/js/app-ui.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
