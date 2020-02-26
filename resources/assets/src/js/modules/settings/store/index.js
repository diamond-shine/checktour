import { taskManager } from '@plugins/taskManager';
import filters from '@utils/filters';
import http from '@utils/http';


export default {
    namespaced: true,
    actions: {
        optionsList: async(ctx) => {
            const { data: { data } } = await taskManager.run(
                'tour-options@all-list.fetch',
                http.get(`tour-options/all`)
            )

            return data
        },

        optionsUpdate: async (ctx, { form }) => {
            const {
                data: { data }
            } = await taskManager.run(
                'tour-options@edit.update',
                form.submit('post', 'tour-options/manage')
            );

            return data;
        },

        importsList: async(ctx) => {
            const { data: { data } } = await taskManager.run(
                'import@list.fetch',
                http.get('import/list')
            )

            return data
        },

        makeImport: async (ctx, { form }) => {
            const {
                data: { data }
            } = await taskManager.run(
                'import@create.store',
                form.submit('post', 'import/bookings')
            );

            return data;
        }
    }
};
