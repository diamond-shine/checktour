import _ from 'lodash';
import print from 'sprintf-js';

export const $t = (text, scope) => text;

export const $ti = function (text, args, scope) {
    if (!_.isArray(args)) {
        throw Error('Must bee array');
    }

    return print.sprintf(
        $t(text),
        ...args
    );
};

export const install = Vue => {
    Vue.prototype.$t = $t;
    Vue.prototype.$ti = $ti;
};
