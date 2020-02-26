import factory from '@utils/store-factories';

const MODULES = 'user-roles';

export default {
    namespaced: true,
    actions: {
        ...factory(MODULES)
    }
};
