
import factory from '@utils/store-factories';
import { taskManager } from '@plugins/taskManager';
import filters from '@utils/filters';
import http from '@utils/http';

const MODULES = 'notifications';

export default {
    namespaced: true,
    actions: {
        ...factory(MODULES),

        list: async () => {
            const {
                data: { data }
            } = await taskManager.run(
                'notifications@list.fetch',
                http.get(`notifications/list`)
            );

            return data;
        }
    }
};
