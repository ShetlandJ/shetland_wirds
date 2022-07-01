require('./bootstrap');

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { QuillEditor } from '@vueup/vue-quill'

import Alert from './components/global/Alert.vue';
import ActionButton from './components/global/ActionButton.vue';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Spaektionary';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .mixin({ methods: { route } })
            .mixin({ components: { QuillEditor, Alert, ActionButton } })
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
