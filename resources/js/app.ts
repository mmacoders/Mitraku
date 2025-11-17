import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import VueApexCharts from 'vue3-apexcharts';
import { registerServiceWorker } from './registerServiceWorker';
import Vue3Avatar from 'vue3-avatar';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Register service worker for PWA
registerServiceWorker();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue', { eager: false }),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(VueApexCharts as any)
            .use(ZiggyVue)
            .component('Vue3Avatar', Vue3Avatar)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});