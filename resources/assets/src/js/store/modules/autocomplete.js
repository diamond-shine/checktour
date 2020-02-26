import Vue from 'vue';
import http from '@utils/http';

export default {
    namespaced: true,
    state: {
        instances: {}
    },
    getters: {
        instance: ({ instances }) => id => instances[ id ] || []
    },
    mutations: {
        FILL: (state, { id, items, pagination }) => {
            Vue.set(state.instances, id, { items, pagination });
        },

        RESET: (state, id) => {
            Vue.set(state.instances, id, []);
        },

        DESTROY: (state, id) => {
            Vue.delete(state.instances, id);
        }
    },
    actions: {
        fetch: async ({ commit }, { id, url, params = {} }) => {
            const {
                data: { data }
            } = await http.get(url, { params });

            commit('FILL', {
                id,
                items: data.items || [],
                pagination: data.pagination || null,
            });

            return data;
        },

        reset: ({ commit }) => commit('RESET'),

        destroy: ({ commit }) => commit('DESTROY')
    }
};
