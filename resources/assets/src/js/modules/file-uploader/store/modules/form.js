import { taskManager } from '@plugins/taskManager';

export default {
    namespaced: true,
    actions: {
        update: async (ctx, { type, id, form }) => {
            const {
                data: { data }
            } = await taskManager.run(
                [
                    'file-uploader@edit.update',
                    `file-uploader@edit.${id}.update`
                ],
                form.submit('post', `file-manager/${type}/update/${id}`)
            );

            return data;
        }
    }
};
