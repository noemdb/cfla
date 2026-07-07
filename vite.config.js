import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: [
                '**/vendor/**',
                '**/storage/**',
                '**/bootstrap/cache/**',
            ],
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules/tw-elements')) {
                        return 'tw-elements';
                    }
                    if (id.includes('node_modules/chart.js')) {
                        return 'chart';
                    }
                    if (id.includes('node_modules/chartjs-plugin-datalabels')) {
                        return 'chart';
                    }
                    if (id.includes('node_modules/swiper')) {
                        return 'swiper';
                    }
                    if (id.includes('node_modules/flowbite')) {
                        return 'flowbite';
                    }
                },
            },
        },
    },
});
