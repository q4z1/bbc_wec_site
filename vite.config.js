import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { copyFileSync, mkdirSync, readdirSync } from 'node:fs';

// Die beiden Bootswatch-Themes werden nicht gebundelt: das Layout tauscht sie
// zur Laufzeit ueber <link id="theme-css"> aus, sie brauchen also stabile
// Dateinamen ohne Hash. Frueher erledigte das mix.copy(); hier kopiert sie ein
// kleiner Plugin-Hook, damit sie nicht von Hand synchron gehalten werden muessen.
function copyThemeCss() {
    const copy = () => {
        mkdirSync('public/css', { recursive: true });
        readdirSync('resources/css')
            .filter((f) => /^theme\..*\.css$/.test(f))
            .forEach((f) => copyFileSync(`resources/css/${f}`, `public/css/${f}`));
    };
    return {
        name: 'wec-copy-theme-css',
        buildStart: copy,
        configureServer: copy,
    };
}

export default defineConfig({
    plugins: [
        copyThemeCss(),
        laravel({
            input: ['resources/js/app.js', 'resources/sass/app.scss'],
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
    ],
    resolve: {
        alias: {
            // Die Blade-Templates enthalten Vue-Markup, das erst im Browser
            // compiliert wird - dafuer wird der Runtime-Compiler gebraucht.
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
    build: {
        rollupOptions: {
            output: {
                // Nach Modulpfad zuordnen, nicht nach Paketname: ein Eintrag
                // 'element-plus' wuerde das ganze Paket in den Chunk ziehen und
                // das Tree Shaking der expliziten Imports in app.js aushebeln.
                manualChunks(id) {
                    if (id.includes('node_modules/element-plus')
                        || id.includes('node_modules/@element-plus')) {
                        return 'element-plus';
                    }
                    if (id.includes('node_modules/@vue') || id.includes('node_modules/vue/')) {
                        return 'vue';
                    }
                    if (id.includes('node_modules/chart.js')) {
                        return 'chartjs';
                    }
                },
            },
        },
        chunkSizeWarningLimit: 900,
    },
    css: {
        preprocessorOptions: {
            scss: {
                silenceDeprecations: ['color-functions', 'global-builtin', 'import'],
            },
        },
    },
});
