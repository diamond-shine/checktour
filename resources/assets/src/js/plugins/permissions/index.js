import methods from './methods';

export default {
    install(Vue) {
        Vue.prototype.$can = function (...args) {
            return methods.hasPermission(args);
        };

        Vue.prototype.$cant = function (...args) {
            return !this.$can(...args);
        };

        Vue.prototype.$canAny = function (...args) {
            return methods.hasAnyPermission(args);
        };

        Vue.prototype.$canNone = function (...args) {
            return !this.$canAny(...args);
        };
    }
};
