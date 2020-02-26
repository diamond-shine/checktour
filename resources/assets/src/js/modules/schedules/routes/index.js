import chunk from '@utils/chunk';
import { $t } from '@utils/i18n';


const CreatePage = chunk(
    () => import(/* webpackChunkName: "schedules" */ '../views/CreatePage')
);

const ListPage = chunk(
    () => import(/* webackChunkName: "schedules" */ '../views/ListPage')
);

const ViewPage = chunk(
    () => import(/* webpuckChunkName: "schedules" */ '../views/ViewPage')
);

const EditPage = chunk(
    () => import(/* webpackChunkName: "schedules" */ '../views/EditPage')
);


export default [
    {
        path: "tour/:tourId/schedules",
        component: ListPage,
        meta: {
            title: $t('Sessions')
        },
        children: [
            {
                path: '/',
                name: 'schedules.list'
            },
            {
                path: 'create',
                name: 'schedules.create',
                component: CreatePage,
                meta: {
                    sidebar: true,
                    checkForm: 'schedules'
                }
            },
            {
                path: 'view/:scheduleId',
                name: 'schedules.view',
                component: ViewPage,
                meta: {
                    sidebar: true
                }
            },
            {
                path: 'edit/:scheduleId',
                name: 'schedules.edit',
                component: EditPage,
                meta: {
                    sidebar: true,
                    checkForm: 'schedule'
                }
            }
        ]
    }
]