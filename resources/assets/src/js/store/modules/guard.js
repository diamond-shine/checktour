import Vue from 'vue';
import _ from 'lodash';

export default {
    namespaced: true,
    state: {
        roles: [],
        permissions: {}
    },
    getters: {
        permissions: ({ permissions }) => _.flatten(
            _.map(permissions, items => items)
        ),

        can: (state, { permissions }) => (...args) => _.flattenDeep([ args ]).every(
            name => permissions.indexOf(name) !== -1
        ),

        canAny: (state, { permissions }) => (...args) => _.flattenDeep([ args ]).some(
            name => permissions.indexOf(name) !== -1
        )
    },
    mutations: {
        PUT_PERMISSIONS: (state, { scope, permissions }) => {
            Vue.set(state.permissions, scope, permissions);
        }
    }
};
