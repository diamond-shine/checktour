import chunk from '@utils/chunk';
import { $t } from '@utils/i18n';


const CreatePage = chunk(
    () => import(/* webpackChunkName: "tours" */ '../views/CreatePage')
);

const ListPage = chunk(
    () => import(/* webackChunkName: "tours" */ '../views/ListPage')
);

const ViewPage = chunk(
    () => import(/* webpuckChunkName: "tours" */ '../views/ViewPage')
);

const EditPage = chunk(
    () => import(/* webpackChunkName: "tours" */ '../views/EditPage')
);

const UsersPage = chunk(
    () => import(/* webpackChunkName: "tours" */ '../views/UsersPage')
);

const ExcursionsPage = chunk(
    () => import(/* webpackChunkName: "excursions" */ '../views/ExcursionsPage')
);


export default [
    {
        path: "/tours",
        component: ListPage,
        meta: {
            title: $t('Tours')
        },
        children: [
            {
                path: '/',
                name: 'tours.list'
            },
            {
                path: 'create',
                name: 'tours.create',
                component: CreatePage,
                meta: {
                    sidebar: true,
                    checkForm: 'tours'
                }
            },
            {
                path: 'view/:tourId',
                name: 'tours.view',
                component: ViewPage,
                meta: {
                    sidebar: true
                }
            },
            {
                path: 'edit/:tourId',
                name: 'tours.edit',
                component: EditPage,
                meta: {
                    sidebar: true,
                    checkForm: 'tours'
                }
            },
            {
                path: 'users/:tourId',
                name: 'tours.users',
                component: UsersPage,
                meta: {
                    sidebar: true
                }
            },
            {
                path: 'view/:tourId/excursions',
                name: 'tours.excursions',
                component: ExcursionsPage,
                meta: {
                    sidebar: true
                }
            },
        ]
    }
]