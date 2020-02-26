import _ from 'lodash';

const modules = require.context('../../components/UI', true, /\.vue$/);

export default {
    install(Vue) {
        modules.keys().forEach(key => {
            const name = key.match(/(\w+)\.vue/);

            if (name) {
                Vue.component(
                    _.kebabCase(name[1]),
                    modules(key).default
                );
            }
        });
    }
};
