export default {
    namespaced: true,
    state: {
        image: null
    },
    mutations: {
        INIT: (state, image) => state.image = image,

        DESTROY: state => state.image = null
    },
    actions: {
        open: ({ commit }, image) => commit('INIT', image),

        close: ({ commit }) => commit('DESTROY')
    }
};
