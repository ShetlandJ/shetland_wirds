require('./bootstrap');

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { QuillEditor } from '@vueup/vue-quill'
import '@vuepic/vue-datepicker/dist/main.css'

import Alert from './components/global/Alert.vue';
import ActionButton from './components/global/ActionButton.vue';
import Datepicker from '@vuepic/vue-datepicker';
import Container from './components/global/Container.vue';
import SanitisedHtml from './components/global/SanitisedHtml.vue';
import vSelect from 'vue-select'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Spaektionary';

import { createPinia } from 'pinia';
const pinia = createPinia();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(pinia)
            .mixin({ methods: { route } })
            .mixin({
                components: {
                    ActionButton,
                    Alert,
                    Container,
                    Datepicker,
                    QuillEditor,
                    SanitisedHtml,
                }
            })
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
