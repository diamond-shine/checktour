const open = (component, title, config = {}) => {
    const store = require('@store').default;

    store.dispatch('global-modal/open', {
        component,
        title,
        config
    });
};

const close = () => {
    const store = require('@store').default;

    store.dispatch('global-modal/close');
};

export default {
    open,
    close
};
