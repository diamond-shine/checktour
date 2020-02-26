import { taskManager } from '@plugins/taskManager';
import filters from '@utils/filters';
import http from '@utils/http';

import invite from './modules/invite';

export default {
    namespaced: true,
    modules: {
        invite
    },
    actions: {
        list: async () => {
            const {
                data: { data }
            } = await taskManager.run(
                'users@list.fetch',
                filters('users').apply('users/list')
            );

            return data;
        },

        create: async () => {
            const {
                data: { data }
            } = await taskManager.run(
                'users@create.store',
                http.get('users/create')
            );

            return data;
        },

        store: async (ctx, { form }) => {
            const {
                data: { data }
            } = await taskManager.run(
                'users@create.store',
                form.submit('post', 'users/store')
            );

            return data;
        },

        view: async (ctx, { userId }) => {
            const {
                data: { data }
            } = await taskManager.run(
                'users@view.fetch',
                http.get(`users/view/${userId}`)
            );

            return data;
        },

        destroy: (ctx, { userId }) => taskManager.run(
            'users@delete.destroy',
            http.delete(`users/delete/${userId}`)
        ),

        edit: async (ctx, { userId }) => {
            const {
                data: { data }
            } = await taskManager.run(
                'users@edit.fetch',
                http.get(`users/edit/${userId}`)
            );

            return data;
        },

        update: async (ctx, { userId, form }) => {
            const {
                data: { data }
            } = await taskManager.run(
                'users@edit.update',
                form.submit('post', `users/update/${userId}`)
            );

            return data;
        }
    }
};
