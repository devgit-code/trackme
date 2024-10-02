import { defineConfig, splitVendorChunkPlugin } from 'vite';
// import { resolve } from 'path'
// import cesium from 'vite-plugin-cesium';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        // cesium(),
        splitVendorChunkPlugin(),
    ],
    server: {
        // https: {
        //     key: '/var/lib/caddy/.local/share/caddy/certificates/acme-v02.api.letsencrypt.org-directory/trackme.info/trackme.info.key',
        //     cert: '/var/lib/caddy/.local/share/caddy/certificates/acme-v02.api.letsencrypt.org-directory/trackme.info/trackme.info.crt',
        // },
        // host: 'http://127.0.0.1',
        host: '0.0.0.0',
        hmr: {
            host: 'localhost'
        }
    }
});
