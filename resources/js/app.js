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

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Spaektionary';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .mixin({ methods: { route } })
            .mixin({
                components: {
                    ActionButton,
                    Alert,
                    Container,
                    Datepicker,
                    QuillEditor,
                }
            })
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
