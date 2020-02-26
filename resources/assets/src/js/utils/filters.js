import Vue from 'vue';
import qs from 'qs';
import _ from 'lodash';

const pushToHistory = form => {
    const query = qs.stringify(
        _.pickBy(
            form.data,
            (value, name) => value && (name !== 'page' || value !== 1)
        )
    );
    const urlWithoutQuery = _.trim(
        document.location.href.split('?')[ 0 ],
        '/'
    );

    window.history.pushState(
        {},
        document.title,
        urlWithoutQuery + (query && `/?${query}`)
    );
};

const __config = {};

export default name => {
    const form = Vue.$forms(`filters--${name}`);

    return {
        data: form.data,

        config: (config = {}) => {
            if (!_.has(__config, name)) {
                __config[ name ] = config;
            } else {
                __config[ name ] = _.assign({}, __config[ name ], config);
            }
        },

        apply: url => {
            const request = form.submit('get', url, {
                filter: (value, name) => name !== 'page' || value !== 1
            });

            if (!__config[ name ]?.silent) {
                request.then(
                    response => pushToHistory(form, response)
                );
            }

            return request;
        },

        restoreFromUrl: (routeName) => {
            const router = require('@router').default;

            const currentRoute = router.match(
                document.location.hash.replace('#', '')
            );

            if (currentRoute?.name !== routeName) {
                return new Promise(
                    resolve => resolve()
                );
            }

            const params = qs.parse(
                document.location.href.split('?')?.[ 1 ] || ''
            );

            return form.setData(params, false);
        },

        clear: () => form.reset()
    };
};
