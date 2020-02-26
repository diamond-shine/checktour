import { list, create, store, update, destroy } from '@utils/store-factories';

const MODULES = [ 'tickets', 'options' ];

export default {
    namespaced: true,
    actions: {
        list: list(MODULES),

        store: store(MODULES),

        update: update(MODULES),

        create: create(MODULES),

        destroy: destroy(MODULES)
    }
};