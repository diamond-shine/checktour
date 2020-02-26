import Vue from 'vue';
import http from '@utils/http';

export default {
    namespaced: true,
    state: {
        sidebar: [],
        current_site: null,
        site_url: null,
        sites: [],
        user: {},
        show_sidebar: true,
        show_sidebar_mobile: false,
        floating_sidebar_status: 'closed',
        confirm: {
            show: false,
            payload: null
        },
        http_error: null,
        initialized: false,
        theme: 'light'
    },
    mutations: {
        FILL: (state, { permissions }) => {
            state.permissions = permissions;
        },

        UPDATE_STATE: (state, { sidebar, user, sites, current_site, site_url }) => {
            state.sidebar = sidebar;
            state.user = user;
            state.sites = sites;
            state.current_site = current_site;
            state.site_url = site_url;
        },

        CHANGE_CURRENT_SITE: (state, site) => {
            state.current_site = site;
        },

        TOGGLE_SIDEBAR_STATUS: (state, flag = null) => {
            state.show_sidebar = flag !== null ? flag : !state.show_sidebar;
        },

        TOGGLE_SIDEBAR_MOBILE_STATUS: (state, flag = null) => {
            state.show_sidebar_mobile = flag !== null ? flag : !state.show_sidebar_mobile;
        },

        SET_FLOATING_SIDEBAR_STATUS: (state, status) => {
            state.floating_sidebar_status = status;
        },

        SHOW_CONFIRM: (state, payload) => {
            state.confirm = {
                show: true,
                payload
            };
        },

        CLOSE_CONFIRM: state => {
            Vue.set(state.confirm, 'show', false);
        },

        MARK_AS_INITIALIZED: state => state.initialized = true,

        SWITCH_THEME: (state, theme) => state.theme = theme
    },
    actions: {
        generateUuid: () => http.get('service/generate/uuid'),

        toggleSidebar: ({ commit }, flag) => commit('TOGGLE_SIDEBAR_STATUS', flag),

        toggleSidebarMobile: ({ commit }, flag) => commit('TOGGLE_SIDEBAR_MOBILE_STATUS', flag),

        setFloatingSidebarStatus: ({ commit }, status) => commit('SET_FLOATING_SIDEBAR_STATUS', status),

        state: async ({ commit }) => {
            const {
                data: { data }
            } = await http.get('/service/menu');


            commit('FILL', data);

            return data;
        },

        confirm: ({ commit }, payload = null) => {
            if (payload) {
                commit('SHOW_CONFIRM', payload);
            } else {
                commit('CLOSE_CONFIRM');
            }
        },

        markAsInitialized: ({ commit }) => commit('MARK_AS_INITIALIZED'),

        switchTheme: ({ commit }, theme) => commit('SWITCH_THEME', theme),

        changeSite: ({ commit }, site) => commit('CHANGE_CURRENT_SITE', site)
    }
};
