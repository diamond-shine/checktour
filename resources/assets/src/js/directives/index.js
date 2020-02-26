import Vue from 'vue';
import _ from 'lodash';

import { Message } from 'element-ui';
import { $t } from '@utils/i18n';

Vue.directive('log', {
    inserted(el, bindings) {
        console.log(bindings.value);
    },
    update(el, bindings) {
        console.log(bindings.value);
    }
});

Vue.directive('copy', {
    bind(el, bindings) {
        const event = Object.keys(bindings.modifiers)[ 0 ] || 'click';

        el.addEventListener(event, () => {
            const input = document.createElement('input');

            if (_.isFunction(bindings.value)) {
                input.value = bindings.value();
            } else {
                input.value = bindings.value;
            }

            input.style.position = 'absolute';
            input.style.transform = 'translate(-10000px,-10000px)';

            document.body.append(input);

            input.select();

            document.execCommand('copy');

            document.body.removeChild(input);

            Message.success(
                $t('Спопійовано')
            );
        });
    }
});
