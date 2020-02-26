import factory from '@utils/store-factories';
import { taskManager } from '@plugins/taskManager';
import filters from '@utils/filters';
import http from '@utils/http';

const MODULES = 'schedules';

export default {
    namespaced: true,
    actions: {
        ...factory(MODULES),

        list: async(ctx, payload) => {
            const { data: { data } } = await taskManager.run(
                'schedules@list.fetch',
                filters('schedules').apply('schedules/schedule-users')
            )

            return data
        },

        view: async(ctx, {scheduleId}) => {
            const { data: { data } } = await taskManager.run(
                'schedules@view.fetch',
                filters('schedules').apply(`schedules/view/${scheduleId}`)
            )

            return data
        }

    }
};


// const { data: { data } } = await taskManager.run(
//                 [
//                     `excursions@delete.destroy`,
//                     `excursions@delete.${excursionId}.destroy`
//                 ],
//                 http.delete(`tours/${tourId}/excursions/delete/${excursionId}`)
//             );

//             return data;