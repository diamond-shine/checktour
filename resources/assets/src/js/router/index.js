import Vue from 'vue';
import VueRouter from 'vue-router';

import _ from 'lodash';

import store from '@store';

import { sync } from 'vuex-router-sync';
import qs from 'qs';

import App from '@components/App';

Vue.use(VueRouter);

const files = require.context('../modules', true, /\/routes\/index\.js$/);

const children = [];

files.keys().forEach(key => {
    const routes = files(key).default;

    if (!_.isArray(routes)) {
        console.error('Routes must bee array');
    } else {
        children.push(routes);
    }
});

const router = new VueRouter({
    linkActiveClass: 'active',
    base: '/',
    routes: [
        {
            path: '/',
            name: 'control',
            redirect: {
                name: 'forecasting.list'
            },
            component: App,
            children: _.flattenDeep(children)
        },
        {
            path: '*',
            redirect: {
                name: 'error',
                params: {
                    httpCode: 404
                }
            }
        }
    ],
    parseQuery(query) {
        return qs.parse(query);
    },
    stringifyQuery(query) {
        var result = qs.stringify(query);

        return result ? ('?' + result) : '';
    }
});

sync(store, router);

router.beforeEach(async (to, from, next) => {
    if (!from.meta.checkForm) {
        return next();
    }

    next(false);

    const { forms } = require('@plugins/forms').default;
    const { $t } = require('@utils/i18n');

    if (forms(from.meta.checkForm).changed()) {
        await new Promise((resolve, reject) => {
            require('@store').default.dispatch('system/confirm', {
                question: $t('Continue without saving?'),
                text: $t('Data was modified!'),
                resolve,
                reject,
                type: 'warning',
                confirmButtonText: $t('Yes'),
                cancelButtonText: $t('No')
            });
        });

        next();
    } else {
        next();
    }
});

router.beforeEach(async (to, from, next) => {
    const store = require('@store').default;
    const floatingSidebarStatus = _.get(store.state, 'system.floating_sidebar_status');

    if (floatingSidebarStatus === 'closed'
        && to.meta.sidebar
    ) {
        await store.dispatch('system/setFloatingSidebarStatus', 'loading');
    }

    if (floatingSidebarStatus !== 'closed'
        && !to.meta.sidebar
    ) {
        await store.dispatch('system/setFloatingSidebarStatus', 'closed');
    }

    next();
});

router.afterEach(async to => {
    const store = require('@store').default;
    const floatingSidebarStatus = _.get(store.state, 'system.floating_sidebar_status');

    const routeWithTitle = to.matched.find(m => m.meta.title);

    if (routeWithTitle) {
        document.title = `${routeWithTitle.meta.title} | ${window.App.name}`;
    } else {
        document.title = window.App.name;
    }

    if (floatingSidebarStatus !== 'loaded'
        && to.meta.sidebar
    ) {
        await store.dispatch('system/setFloatingSidebarStatus', 'loaded');
    }
});

export default router;
