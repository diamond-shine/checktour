import { taskManager } from '@plugins/taskManager';
import http from '@utils/http';

export default {
    namespaced: true,
    actions: {
        fetch: async () => {
            const {
                data: { data }
            } = await taskManager.run(
                'users@invite.fetch',
                http.get(`users/invite`)
            );

            return data;
        },

        send: (ctx, { form }) => taskManager.run(
            'users@invite.send',
            form.submit('post', 'users/invite/send')
        )
    }
};
