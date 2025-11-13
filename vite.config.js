// vite.config.js
import { defineConfig } from 'vite';
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
    ],

    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: '103.123.98.9',
            port: 5173,
        },
        cors: {
            origin: [
                'http://103.123.98.9:8080',
                'http://127.0.0.1:8080',
                'http://0.0.0.0:8080',
            ],
            methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            credentials: true
        }
    },
    // server: {
    //     hmr: {
    //         host: 'localhost'

    //     },
    // },
});