import _ from 'lodash';
import deepForEach from 'deep-for-each';

const typeConversion = (data) => {
    deepForEach(data, (value, prop, subject, path) => {
        if (value === 'Infinity') {
            _.set(data, path, Infinity);
        }
    });

    return data;
};

export default function (name, Store, store) {
    store = store || require('@store').default;

    const initialState = _.get(window, `__vars.store.${name}`, null);

    if (initialState !== null) {
        const modules = _.get(initialState, '@modules', null);

        if (modules !== null) {
            _.each(modules, (state, name) => {
                const initState = _.get(Store.modules, `${name}.state`, {});

                _.set(
                    Store.modules,
                    `${name}.state`,
                    typeConversion({
                        ...initState,
                        ...state
                    })
                );
            });

            delete initialState[ '@modules' ];
        }

        Store.state = typeConversion(
            _.merge(Store.state, initialState)
        );
    }

    store.registerModule(name, Store);
}
