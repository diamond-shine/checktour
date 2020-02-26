import 'regenerator-runtime/runtime';

import './static';

import Vue from 'vue';
import ElementUI from 'element-ui';
import locale from 'element-ui/lib/locale/lang/en';
import NProgress from 'nprogress';
import vueScroll from 'vuescroll';
import VueCookies from 'vue-cookies';

import store from '@store';
import router from '@router';
import eventEmitter from '@utils/eventEmitter';

import { install as VueI18n } from '@utils/i18n';
import VueForm from '@plugins/forms';
import TaskManager from '@plugins/taskManager';
import Permissions from '@plugins/permissions';
import UI from '@plugins/ui';

import http from '@utils/http';
import { install as HelpersInstaller } from '@helpers';








import VLayout from './components/VLayout';
import VSidebarLayout from './components/VSidebarLayout';


import SidebarLayout from './components/SidebarLayout';
import SidebarContentLayout from './components/SidebarContentLayout';
import Modals from './components/Modals';

Vue.component('v-layout', VLayout);
Vue.component('v-sidebar-layout', VSidebarLayout);

Vue.component('sidebar-layout', SidebarLayout);
Vue.component('sidebar-content-layout', SidebarContentLayout);

Vue.directive('log', {
    inserted(el, bindings) {
        console.log(bindings.value);
    },
    update(el, bindings) {
        console.log(bindings.value);
    }
});

Vue.directive('copy', {
    bind(el, bindings) {
        const event = Object.keys(bindings.modifiers)[ 0 ] || 'click';

        el.addEventListener(event, () => {
            const input = document.createElement('input');

            input.value = bindings.value;
            input.style.position = 'absolute';
            input.style.transform = 'translate(-10000px,-10000px)';

            document.body.append(input);

            input.select();

            document.execCommand('copy');

            document.body.removeChild(input);
        });
    }
});

Vue.mixin({
    methods: {
        log(...args) {
            console.log(args);
        }
    }
});

VueForm.config({
    httpClient: http,
    defaultRequestType: 'json'
});

Vue.use(ElementUI, { locale });
Vue.use(HelpersInstaller);
Vue.use(VueForm);
Vue.use(VueI18n);
Vue.use(TaskManager);
Vue.use(Permissions);
Vue.use(UI);
Vue.use(vueScroll);
Vue.use(VueCookies);

const modules = require.context('./modules', true, /\.\/\w+([\_\-]+\w+)*\/index\.js$/);

modules.keys().forEach(
    key => modules(key)
);

(new Vue({
    store,
    router,
    template: '<router-view />'
})).$mount('#app');

(new Vue({
    store,
    router,
    render: h => h(Modals)
})).$mount('#modals');

// eventEmitter.on(
//     'chunk-load-start',
//     () => NProgress.start()
// );

// eventEmitter.on(
//     'chunk-load-end',
//     () => NProgress.done(true)
// );

// eventEmitter.on(
//     'chunk-load-fail',
//     () => NProgress.done(true)
// );
