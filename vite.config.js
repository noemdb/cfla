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
        // El chunk de Mermaid (3.4 MB / 943 kB gzip) se carga bajo demanda
        // solo en páginas LMS. El límite default es 500 kB.
        chunkSizeWarningLimit: 3500,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules/tw-elements')) {
                        return 'tw-elements';
                    }
                    if (id.includes('node_modules/flowbite')) {
                        return 'flowbite';
                    }
                    // Mermaid: todos sus diagramas y dependencias internas en un solo chunk.
                    // Se carga bajo demanda solo en páginas LMS, así que un chunk grande
                    // es mejor que 50+ micro-archivos con overhead de HTTP.
                    if (id.includes('node_modules/mermaid') || id.includes('node_modules/dagre')) {
                        return 'mermaid';
                    }
                    // Dependencias internas de mermaid: cytoscape, katex, etc.
                    if (id.includes('node_modules/cytoscape') || id.includes('node_modules/katex') || id.includes('node_modules/d3-')) {
                        return 'mermaid';
                    }
                },
            },
        },
    },
});
