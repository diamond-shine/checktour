import { store, create, update, edit, view } from '@utils/store-factories';
import { taskManager } from '@plugins/taskManager';
import http from '@utils/http';
import filters from '@utils/filters';

const MODULES = 'bookings';

export default {
    namespaced: true,

    actions: {
        store: store(MODULES),
        create: create(MODULES),
        update: update(MODULES),
        edit: edit(MODULES),
        view: view(MODULES),

        listForecast: async(ctx) => {
            const { data: { data } } = await taskManager.run(
                'bookings@listForecast.fetch',
                filters('bookings-forecast').apply(`bookings/list-forecasting`)
            )

            return data
        },

        listRostered: async(ctx) => {
            const { data: { data } } = await taskManager.run(
                'bookings@listRostered.fetch',
                filters('bookings-rostered').apply(`bookings/list-rostered`)
            )

            return data
        },

        rosterBookings: async(ctx, { rosterId }) => {
            const { data: { data } } = await taskManager.run(
                'bookings@rosterBookings.fetch',
                filters('roster-bookings').apply(`bookings/roster-bookings/${rosterId}`)
            )

            return data
        },

        listProcessed: async(ctx) => {
            const { data: { data } } = await taskManager.run(
                'bookings@listProcessed.fetch',
                filters('bookings-procseed').apply(`bookings/list-processed`)
            )

            return data
        },

        listWaitingRoom: async(ctx) => {
            const { data: { data } } = await taskManager.run(
                'bookings@listWaitingRoom.fetch',
                filters('bookings-waiting-room').apply(`bookings/list-waiting-room`)
            )

            return data
        },

        list: async(ctx) => {
            const { data: { data } } = await taskManager.run(
                'bookings@list.fetch',
                filters('bookings').apply(`bookings/list`)
            )

            return data
        },

        process: async (ctx, {bookingId, form}) => {
            const { data: { data } } = await taskManager.run(
                `bookings@process.update`,
                form.submit('post', `bookings/process/${bookingId}`)
            );

            return data;
        }
    }
};