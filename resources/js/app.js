import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { confirmAlert, errorAlert, successAlert } from './Plugins/swal';
import Table from './Components/Table.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue);
            //.use(SweetalertPlugin)

            app.config.globalProperties.$confirm = confirmAlert;
            app.config.globalProperties.$success = successAlert;
            app.config.globalProperties.$error = errorAlert;

            app.component('Table', Table);
            app.mount(el);
            delete el.dataset.page
        return app;
    },
    progress: {
        color: '#4B5563',
    },

});
