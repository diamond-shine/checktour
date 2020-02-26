import chunk from '@utils/chunk';
import { routeGroup } from '@utils/router';
import { $t } from '@utils/i18n';

const ListPage = chunk(
    () => import(/* webpackChunkName: "users" */ '../views/ListPage')
);
const CreatePage = chunk(
    () => import(/* webpackChunkName: "users" */ '../views/CreatePage')
);
const EditPage = chunk(
    () => import(/* webpackChunkName: "users" */ '../views/EditPage')
);
const ViewPage = chunk(
    () => import(/* webpackChunkName: "users" */ '../views/ViewPage')
);
const InvitePage = chunk(
    () => import(/* webpackChunkName: "users" */ '../views/InvitePage')
);

export default [
    ...routeGroup('/users', [
        {
            path: '/',
            component: ListPage,
            meta: {
                title: $t('Users')
            },
            children: [
                {
                    path: '/',
                    name: 'users.list'
                },
                {
                    path: 'view/:userId',
                    name: 'users.view',
                    component: ViewPage,
                    meta: {
                        sidebar: true
                    }
                },
                {
                    path: 'invite',
                    name: 'users.invite',
                    component: InvitePage,
                    meta: {
                        sidebar: true
                    }
                }
            ]
        },
        {
            path: 'create',
            name: 'users.create',
            component: CreatePage,
            meta: {
                checkForm: 'user',
                title: $t('Users')
            }
        },
        {
            path: 'view/:userId/edit',
            name: 'users.edit',
            component: EditPage,
            meta: {
                checkForm: 'user',
                title: $t('Users')
            }
        }
    ])
];
