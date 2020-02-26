import { list, store } from '@utils/store-factories';

const MODULES = 'dashboard';

export default {
    namespaced: true,
    actions: {
        list: list(MODULES),

        store: store(MODULES)
    }
};
