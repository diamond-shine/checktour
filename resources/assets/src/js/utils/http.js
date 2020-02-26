import NProgress from 'nprogress';
import axios from 'axios';
import _ from 'lodash';

import { $t } from '@utils/i18n';

import { Message as message } from 'element-ui';

import qs from 'qs';

const http = axios.create({
    baseURL: window.App.baseUrl || '/api/',
    headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    },
    paramsSerializer: params => qs.stringify(params)
});

const processMeta = ({ meta = {} }) => _.each(meta, (value, key) => {
    if (value.type === 'message') {
        message({
            ...value.payload,

            offset: 40 * key + 20
        });
    } else if (value.type === 'permissions') {
        require('@store')
            .default
            .commit('guard/PUT_PERMISSIONS', value.payload);
    } else if (value.type === 'state') {
        require('@store')
            .default
            .commit('system/UPDATE_STATE', value.payload);
    }
});

const bindRouteParams = url => {
    const matches = url.match(/\@\w+\??/g);

    if (matches) {
        const route = require('@router').default.currentRoute;

        for (const match of matches) {
            const isOptional = match[ match.length - 1 ] === '?';
            const binding = match.replace('@', '').replace('?', '');

            if (!route.params[ binding ]) {
                if (isOptional) {
                    url = url.replace(`@${binding}?`, '');
                }

                continue;
            }

            url = url.replace(match, route.params[ binding ]);
        }
    }

    return url;
};

NProgress.configure({
    easing: 'linear',
    speed: 350,
    showSpinner: false
});

http.interceptors.request.use((config) => {
    if (window.App.csrf_token && config.method === 'post') {
        config.headers[ 'X-CSRF-TOKEN' ] = window.App.csrf_token;
    }

    const jsonServerConfig = window.App.jsonServerConfig || {};

    if (jsonServerConfig.ignoreParams) {
        config.params = _.pickBy(config.params, (value, key) => {
            return jsonServerConfig.ignoreParams.indexOf(key) === -1;
        });
    }

    config.url = bindRouteParams(config.url);

    if (config.method === 'get') {
        NProgress.start();
    }

    return config;
});

http.interceptors.response.use((response) => {
    if (response.config.method === 'get') {
        NProgress.done(true);
    }

    processMeta(response.data);

    return response;
}, (error) => {
    if (error.response.config.method === 'get') {
        NProgress.done(true);
    }

    const isInitialized = require('@store').default.state.system.initialized;

    processMeta(error.response.data);

    switch (error.response.status) {
        case 301:
            document.location.href = error.response.data.redirect_url;
            break;

        case 302:
            require('@router').default.push(
                error.response.data.route
            );
            break;

        case 401:
            document.location.href = `${document.location.origin}/control/login?back_url=${document.location.href}`;
            break;

        case 403:
            if (isInitialized) {
                message({
                    showClose: true,
                    type: 'error',
                    title: $t('Access denied'),
                    message: $t('Access denied')
                });
            } else {
                require('@router').default.push({
                    name: 'error',
                    params: {
                        httpCode: 403
                    }
                });
            }
            break;

        case 404:
            if (isInitialized) {
                message({
                    showClose: true,
                    type: 'error',
                    title: $t('Page not found'),
                    message: $t('Page not found')
                });
            }
            break;

        case 422:
            if (isInitialized) {
                message({
                    showClose: true,
                    type: 'error',
                    title: $t('Validation error'),
                    message: $t('Validation error')
                });
            } else {
                require('@router').default.push({
                    name: 'error',
                    params: {
                        httpCode: 422
                    }
                });
            }

            break;

        case 500:
            if (isInitialized) {
                message({
                    showClose: true,
                    type: 'error',
                    title: $t('Server error'),
                    message: $t('Server error')
                });
            } else {
                require('@router').default.push({
                    name: 'error',
                    params: {
                        httpCode: 500
                    }
                });
            }
            break;
        case 504:
            if (isInitialized) {
                message({
                    showClose: true,
                    type: 'error',
                    title: $t('Server error'),
                    message: $t('Server error')
                });
            } else {
                require('@router').default.push({
                    name: 'error',
                    params: {
                        httpCode: 500
                    }
                });
            }
            break;
    }

    return Promise.reject(error);
});

export default http;
