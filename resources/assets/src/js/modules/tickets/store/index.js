import factory from '@utils/store-factories';
import { taskManager } from '@plugins/taskManager';
import http from '@utils/http';

import options from './modules/options';

const MODULES = 'tickets';

export default {
    namespaced: true,
    modules: {
        options
    },
    actions: {
        ...factory(MODULES),

        options: async (ctx, { ticketId }) => {
            const {
                data: { data }
            } = await taskManager.run(
                'tickets@options.fetch',
                http.get(`tickets/options/${ticketId}`)
            );

            return data;
        }
    }
};