import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vueJsx from '@vitejs/plugin-vue-jsx'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.jsx', 'public/js/sb-admin-2.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        vueJsx()
    ],
    base: '/admin-panel/',
});
