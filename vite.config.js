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
        hmr: {
            // host:'192.168.0.39' // host JAMAS
            host: '192.168.1.24' // host OREMUS
        },
        cors: {
            // origin: ['http://192.168.0.39:8081', 'http://127.0.0.1:8000'], // Server JAMAS
            origin: ['http://192.168.1.24:8081', 'http://127.0.0.1:8000'], // SERVER OREMUS
            methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            credentials: true
        }
    },
});