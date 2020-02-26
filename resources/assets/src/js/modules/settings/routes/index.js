import chunk from '@utils/chunk';
import { routeGroup } from '@utils/router';
import { $t } from '@utils/i18n';

const EditPage = chunk(
    () => import(/* webpackChunkName: "settings" */ '../views/EditPage')
);


export default [
    ...routeGroup('/settings', [
        {
            path: '/',
            name: 'settings.edit',
            component: EditPage,
            meta: {
                checkForm: 'settings',
                title: $t('settings')
            }
        }
    ])
];
