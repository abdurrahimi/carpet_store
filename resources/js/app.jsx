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
        app.directive("money", {
            beforeMount(el, binding) {
                const formatValue = (value) => {
                    if (!value) return "";
                    return parseInt(value.replace(/\D/g, "") || "0").toLocaleString("id-ID");
                };

                // Update tampilan input saat ada perubahan
                const updateDisplay = (value) => {
                    el.value = formatValue(value);
                };

                // Event listener buat input
                el.addEventListener("input", (e) => {
                    let rawValue = e.target.value.replace(/\D/g, "");
                    el.value = formatValue(rawValue);

                    // Emit event 'update:modelValue' supaya reactive di parent
                    el.dispatchEvent(new CustomEvent("update:modelValue", {
                        detail: parseInt(rawValue) || 0,
                        bubbles: true,
                    }));
                });

                // Update tampilan awal kalau ada value binding
                if (binding.value) {
                    updateDisplay(binding.value.toString());
                }
            },
            updated(el) {
                if(el.value == 0 || el.value == null || el.value == ""){ el.value = 0; return; }
                el.value = parseInt(el.value.toString().replace(/\D/g, "")).toLocaleString("id-ID");
            },
        });


        app.component('Table', Table);
        app.mount(el);
        delete el.dataset.page;
        return app;
    },
    progress: {
        color: '#4B5563',
    },
});


