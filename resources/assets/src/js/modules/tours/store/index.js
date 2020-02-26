import factory from '@utils/store-factories';
import { taskManager } from '@plugins/taskManager';
import http from '@utils/http';

import users from './modules/users';
import excursions from './modules/excursions';

const MODULES = 'tours';

export default {
    namespaced: true,
    modules: {
        users,
        excursions
    },
    actions: {
        ...factory(MODULES),

        users: async (ctx, { ticketId }) => {
            const {
                data: { data }
            } = await taskManager.run(
                'tours@users.fetch',
                http.get(`tours/users/${tourId}`)
            );

            return data;
        }


    }
};