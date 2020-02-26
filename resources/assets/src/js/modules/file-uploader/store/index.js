import Vue from 'vue';
import _ from 'lodash';

import { taskManager } from '@plugins/taskManager';
import http from '@utils/http';

import form from './modules/form';
import preview from './modules/preview';
import uploads from './modules/uploads';

export default {
    namespaced: true,
    modules: {
        form,
        preview,
        uploads
    },
    state: {
        show: false,
        cwd: null,
        config: {
            root: null,
            scope: null,
            privateMode: false,
            multiple: false,
            accept: []
        },
        resolver: {}
    },
    mutations: {
        OPEN: (state, config = {}) => {
            state.show = true;

            Vue.set(state.config, 'scope', config.scope || null);
            Vue.set(state.config, 'multiple', !!config.multiple);
            Vue.set(state.config, 'privateMode', !!config.privateMode);
            Vue.set(state.config, 'root', !!config.root);

            if (config.cwd) {
                Vue.set(state.cwd, 'cwd', config.cwd);
            }

            if (config.accept && !_.isArray(config.accept)) {
                throw new Error('[File manager]: Invalid config: accept must bee Array[String]');
            }

            if (!config.accept) {
                return;
            }

            const accept = _.reduce(config.accept, (carry, value) => {
                const [ group, type ] = value.split('/').map(_.trim);

                if (!type || type === '*') {
                    carry.push({
                        group,
                        type: null
                    });
                } else {
                    carry.push({ group, type });
                }

                return carry;
            }, []);

            Vue.set(state.config, 'accept', accept);
        },

        CLOSE: state => state.show = false,

        RESET: state => {
            state.config.scope = null;
            state.config.privateMode = false;
            state.config.multiple = false;
            state.config.accept = [];
        },

        SET_RESOLVER: (state, { resolve, reject }) => {
            state.resolver = { resolve, reject };
        },

        SET_CURRENT_FOLDER: (state, folder) => state.cwd = folder
    },
    actions: {
        open: ({ commit, dispatch }, config = {}) => {

            const promise = new Promise(
                async (resolve, reject) => {
                    commit('SET_RESOLVER', { resolve, reject });

                    if (config.cwd) {
                        const { item: folder } = await dispatch('firstOrCreateFolderInfo', config.cwd);

                        config.cwd = folder;

                        commit('SET_CURRENT_FOLDER', folder);
                    }

                    commit('OPEN', config);
                }
            );

            promise.catch(
                (reason) => (console.log(reason))
            );

            return promise;
        },

        list: async ({ state }, payload = {}) => {
            const processes = [
                'file-uploader@list.fetch'
            ];

            if (state.cwd) {
                processes.push(`file-uploader@list.${state.cwd.id}.fetch`);
            }

            let params = {};

            if (_.trim(payload.term)) {
                params.term = _.trim(payload.term);
            }

            const headers = {};

            if (state.config.privateMode) {
                headers[ 'X-PRIVATE-MODE' ] = 'yes';
            }

            const {
                data: { data }
            } = await taskManager.run(
                processes,
                http.get(`file-manager/explorer/list/${state.cwd?.id || ''}`, { params, headers })
            );

            return data;
        },

        loadMore: async ({ state }, payload = {}) => {
            let params = {
                pagination_by: payload.pagination_by,
                last_item: payload.last_item
            };

            if (_.trim(payload.term)) {
                params.term = payload.term;
            }

            const headers = {};

            if (state.config.privateMode) {
                headers[ 'X-PRIVATE-MODE' ] = 'yes';
            }

            const {
                data: { data }
            } = await taskManager.run(
                'file-uploader@explorer.list.load-more',
                http.get(`file-manager/explorer/load-more/${state.cwd?.id || ''}`, { params, headers })
            );

            return data;
        },

        firstOrCreateFolderInfo: async (ctx, cwd) => {
            let path = null;

            if (_.isArray(cwd)) {
                path = _.reduce(cwd, (result, value) => {
                    result += `/${value.mark}`;

                    if (value.name) {
                        result += `:${value.name}`;
                    }

                    return result;
                }, '');
            } else if (_.isObject(cwd)) {
                path = `${cwd.mark}`;

                if (cwd.name) {
                    path += `:${cwd.mane}`;
                }
            } else if (_.isString(cwd)) {
                path = cwd;
            } else {
                throw new Error('CWD is not defined');
            }

            const {
                data: { data }
            } = await http.get('file-manager/explorer/info/first-or-create-folder', {
                params: {
                    path
                }
            });

            return data;
        },

        folderInfo: async (ctx, folderId) => {
            const {
                data: { data }
            } = await http.get(`file-manager/explorer/info/folder/${folderId}`);

            return data;
        },

        storeFolder: async ({ state }, form) => {
            const {
                data: { data }
            } = await taskManager.run(
                'file-uploader@explorer.folders.create',
                form.submit('post', `file-manager/explorer/store-folder/${state.cwd?.id || ''}`)
            );

            return data;
        },

        enterToFolder: ({ commit, dispatch, state }, folder) => {
            commit('SET_CURRENT_FOLDER', folder);

            const cwd = state.config.cwd;

            if (cwd
                && (
                    folder === null
                    || !(cwd._lft <= folder._lft && cwd._rgt >= folder._rgt)
                )
            ) {
                throw new Error('PERMISSION_DENIED');
            }

            return dispatch('list');
        },

        moveSelected: async ({ state }, selected) => {
            const {
                data: { data }
            } = await taskManager.run(
                'file-uploader@explorer.list.move-selected',
                http.post(`file-manager/explorer/move-selected/${state.cwd?.id || ''}`, selected)
            );

            return data;
        },

        destroySelected: async ({ state }, selected) => {
            const {
                data: { data }
            } = await taskManager.run(
                'file-uploader@explorer.list.destroy-selected',
                http.delete(`file-manager/explorer/destroy-selected/${state.cwd?.id || ''}`, {
                    params: selected
                })
            );

            return data;
        },

        submit: ({ commit, state, dispatch }, selected) => {
            state.resolver.resolve(
                state.config.multiple ?
                    selected :
                    (_.head(selected) || null)
            );

            commit('CLOSE');

            dispatch('reset');
        },

        close: ({ commit, state, dispatch }) => {
            commit('CLOSE');

            state.resolver.reject('File manager modal closed');

            dispatch('reset');
        },

        reset: ({ commit, dispatch }) => {
            commit('RESET');

            dispatch('uploads/reset');
        }
    }
};
