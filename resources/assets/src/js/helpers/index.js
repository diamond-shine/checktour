import registerStore from './libs/registerStore';
import icon from './libs/icon';
import eventEmitter from '@utils/eventEmitter';
import filters from '@utils/filters';
import arrayHelpers from './libs/arrayHelpers';
import doubleCurlyBraces from './libs/doubleCurlyBraces';
import modal from './libs/modal';

import _ from 'lodash';
import dayJS from 'dayjs';
import AsyncEvent from '@helpers/libs/AsyncEvent';

export {
    registerStore,
    doubleCurlyBraces
};

export const install = {
    install(Vue) {
        Vue.prototype.$helpers = {
            icon,
            date: (data, format) => dayJS(data).format(format),
            array: arrayHelpers,
            modal
        };

        Vue.prototype.$ee = eventEmitter;

        Vue.prototype.$listenCycle = function (event, callback) {
            this.$ee.on(event, callback);

            this.$once(
                'hook:beforeDestroy',
                () => this.$ee.off(event)
            );
        };

        Vue.prototype.$asyncEmit = function (event, payload) {
            const eventObj = new AsyncEvent(event, payload);

            this.$emit(event, eventObj);

            return eventObj;
        };

        Vue.prototype.$vConfirm = async function (question, text, config = {}) {
            return new Promise((resolve, reject) => {
                this.$store.dispatch('system/confirm', {
                    question,
                    text: text || this.$t('Виконати дію?'),
                    resolve,
                    reject,
                    type: config.type || 'warning',
                    confirmButtonText: this.$t('Yes'),
                    cancelButtonText: this.$t('No'),
                    confirmButtonType: config.confirmButtonType
                });
            });
        };

        Vue.prototype.$vConfirmDelete = function (confirmText, question, params = {}) {

            const config = {
                confirmButtonText: this.$t('Delete'),
                cancelButtonText: this.$t('Cancel'),
                type: 'error'
            };

            if (params.withComment) {
                return this.$prompt(
                    confirmText,
                    question || this.$t('This action cannot be undone.'),
                    {
                        inputType: 'textarea',
                        inputValidator: value => !!value,
                        inputPlaceholder: this.$t('Вкажіть причину видалення.'),
                        inputErrorMessage: this.$t('Вкажіть причину видалення.'),

                        ...config
                    }
                );
            }

            return this.$vConfirm(
                confirmText,
                question || this.$t('This action cannot be undone.'),
                {
                    confirmButtonText: this.$t('Delete'),
                    confirmButtonClass: 'el-button--danger-important',
                    cancelButtonText: this.$t('Cancel'),
                    type: params.type || 'error'
                }
            );
        };

        Vue.prototype.$routeParam = function (name, defaults) {
            return _.get(this.$route, `params.${name}`, defaults);
        };

        Vue.prototype.$routeParams = function (names, defaults = {}) {
            names = _.isString(names) ? [ names ] : names;

            return names.reduce((carry, name) => {
                carry[ name ] = _.get(this.$route, `params.${name}`, defaults[ name ]);

                return carry;
            }, {});
        };

        Vue.prototype.$hasRouteParam = function (name) {
            return !!_.get(this.$route, `params.${name}`);
        };

        Vue.prototype.$appendFilters = function (path, params) {
            const query = _.assign(
                {},
                this.$router.currentRoute.query,
                params
            );

            this.$router.push({
                path: path,
                query: query
            });
        };

        Vue.prototype.$lodash = _;

        Vue.prototype.$formatValidationError = e => _.reduce(
            e.response.data.errors,
            (carry, errors) => carry.concat(errors),
            []
        ).join('\n');

        Vue.prototype.$filters = filters;
    }
};
