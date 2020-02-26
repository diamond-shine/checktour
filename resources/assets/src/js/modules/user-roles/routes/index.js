import chunk from '@utils/chunk';
import { $t } from '@utils/i18n';

const ListPage = chunk(
    () => import(/* webpackChunkName: "user-roles" */ '../views/ListPage')
);
const CreatePage = chunk(
    () => import(/* webpackChunkName: "user-roles" */ '../views/CreatePage')
);
const ViewPage = chunk(
    () => import(/* webpackChunkName: "user-roles" */ '../views/ViewPage')
);
const EditPage = chunk(
    () => import(/* webpackChunkName: "user-roles" */ '../views/EditPage')
);

export default [
    {
        path: '/user-roles',
        component: ListPage,
        meta: {
            title: $t('Ролі користувачів')
        },
        children: [
            {
                path: '/',
                name: 'user-roles.list'
            },
            {
                path: 'view/:userRoleId',
                name: 'user-roles.view',
                component: ViewPage,
                meta: {
                    sidebar: true
                }
            },
            {
                path: 'create',
                name: 'user-roles.create',
                component: CreatePage,
                meta: {
                    sidebar: {
                        size: 'md'
                    },
                    checkForm: 'user-role'
                }
            },
            {
                path: 'edit/:userRoleId',
                name: 'user-roles.edit',
                component: EditPage,
                meta: {
                    sidebar: {
                        size: 'md'
                    },
                    checkForm: 'user-role'
                }
            }
        ]
    }
];
