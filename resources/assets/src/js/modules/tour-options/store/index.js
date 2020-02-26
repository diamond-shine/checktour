import factory from '@utils/store-factories';
import { taskManager } from '@plugins/taskManager';
import http from '@utils/http';

const MODULES = 'tour-options';

export default {
    namespaced: true,
    actions: {
        ...factory(MODULES),

        list: async(ctx, {tourId}) => {
            const { data: { data } } = await taskManager.run(
                'tour-options@list.fetch',
                http.get(`tours/${tourId}/tour-options/list`)
            )

            return data
        },

        view: async(ctx, {tourId, tourOptionId}) => {
            const { data: { data } } = await taskManager.run(
                'tour-options@view.fetch',
                http.get(`tours/${tourId}/tour-options/view/${tourOptionId}`)
            )

            return data
        },

        edit: async(ctx, {tourId, tourOptionId}) => {
            const { data: { data } } = await taskManager.run(
                'tour-options@edit.fetch',
                http.get(`tours/${tourId}/tour-options/edit/${tourOptionId}`)
            )

            return data
        },

        update: async(ctx, {tourId, tourOptionId, form}) => {
            const { data: { data } } = await taskManager.run(
                'tour-options@edit.update',
                form.submit('post', `tours/${tourId}/tour-options/edit/${tourOptionId}`)
            )

            return data
        },

        store: async (ctx, {tourId, form}) => {
            const { data: { data } } = await taskManager.run(
                `tour-options@create.store`,
                form.submit('post', `tours/${tourId}/tour-options/create`)
            );

            return data;
        },

        create: async (ctx, {tourId}) => {

            const { data: { data } } = await taskManager.run(
                `tour-options@create.fetch`,
                http.get(`tours/${tourId}/tour-options/create`)
            );

            return data;
        },

        destroy: async (ctx, {tourId, tourOptionId}) => {
            const { data: { data } } = await taskManager.run(
                [
                    `tour-options@delete.destroy`,
                    `tour-options@delete.${tourOptionId}.destroy`
                ],
                http.delete(`tours/${tourId}/tour-options/delete/${tourOptionId}`)
            );

            return data;
        }
    }
};
