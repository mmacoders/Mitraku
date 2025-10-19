import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import fs from 'fs'

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.ts',
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
//       server: {
//         host: 'mitraku.test',
//         port: 5173,
//         https: {
//             key: fs.readFileSync('D:/Priv/e-pks/ssl/mitraku.test-key.pem'),
//             cert: fs.readFileSync('D:/Priv/e-pks/ssl/mitraku.test.pem'),
//         },
//         hmr: {
//             host: 'mitraku.test',
//             protocol: 'wss',
//         },
//   },
   
//     // server: {
//     //     hmr: {
//     //         host: '192.168.1.6',
//     //     },
//     // },
    build: {
        rollupOptions: {
            input: {
                app: 'resources/js/app.ts',
                'service-worker': 'public/service-worker.js',
                'manifest': 'public/manifest.json',
            },
            output: {
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name === 'manifest.json') {
                        return 'manifest.json';
                    }
                    return 'assets/[name]-[hash][extname]';
                },
                entryFileNames: 'assets/[name]-[hash].js',
                chunkFileNames: 'assets/[name]-[hash].js',
            },
        },
    },
});