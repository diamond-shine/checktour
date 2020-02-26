import Vue from 'vue';
import Vuex from 'vuex';

import { registerStore } from '@helpers';

// Plugins
import TaskManager from '@plugins/taskManager';
import VueForm from '@plugins/forms';

import createPersistedState from 'vuex-persistedstate';
import { VUEX_DEEP_SET } from 'vue-deepset';

// Modules
import system from './modules/system';
import guard from './modules/guard';
import autocomplete from './modules/autocomplete';

Vue.use(Vuex);

const store = new Vuex.Store({
    plugins: [
        VueForm.storeRegisterer,
        TaskManager.storeRegisterer,
        createPersistedState({
            paths: [
                'system.theme'
            ]
        })
    ],
    modules: {
        system,
        guard,
        autocomplete
    },
    mutations: {
        VUEX_DEEP_SET
    }
});

// Store auto register
const files = require.context('../modules', true, /\/store\/index\.js$/);

files.keys().forEach(key => {
    const name = key.replace('./', '').replace('/store/index.js', '');
    const data = files(key).default;

    registerStore(name, data, store);
});

export default store;
