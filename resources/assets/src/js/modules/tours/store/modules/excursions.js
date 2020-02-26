import { list, create, store, update, destroy } from '@utils/store-factories';
import { taskManager } from '@plugins/taskManager';
import http from '@utils/http';
import filters from '@utils/filters';

const MODULES = [ 'tours', 'excursions' ];

export default {
    namespaced: true,
    actions: {
        list: async(ctx, {tourId}) => {
            const { data: { data } } = await taskManager.run(
                'excursions@list.fetch',
                filters('excursions').apply(`tours/${tourId}/excursions/list`)
            )

            return data
        },

        store: async (ctx, {tourId, form}) => {
            const { data: { data } } = await taskManager.run(
                `excursions@create.store`,
                form.submit('post', `tours/${tourId}/excursions/create`)
            );

            return data;
        },

        create: async (ctx, {tourId}) => {

            const { data: { data } } = await taskManager.run(
                `excursions@create.fetch`,
                http.get(`tours/${tourId}/excursions/create`)
            );

            return data;
        },

        destroy: async (ctx, {tourId, excursionId}) => {
            const { data: { data } } = await taskManager.run(
                [
                    `excursions@delete.destroy`,
                    `excursions@delete.${excursionId}.destroy`
                ],
                http.delete(`tours/${tourId}/excursions/delete/${excursionId}`)
            );

            return data;
        }
    }
};

