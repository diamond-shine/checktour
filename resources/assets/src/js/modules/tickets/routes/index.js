import chunk from '@utils/chunk';
import { $t } from '@utils/i18n';


const CreatePage = chunk(
    () => import(/* webpackChunkName: "tickets" */ '../views/CreatePage')
);

const ListPage = chunk(
    () => import(/* webackChunkName: "tickets" */ '../views/ListPage')
);

const ViewPage = chunk(
    () => import(/* webpuckChunkName: "tickets" */ '../views/ViewPage')
);

const EditPage = chunk(
    () => import(/* webpackChunkName: "tickets" */ '../views/EditPage')
);

const OptionsPage = chunk(
    () => import(/* webpackChunkName: "tickets" */ '../views/OptionsPage')
);


export default [
    {
        path: "tours/:tourId/tickets",
        component: ListPage,
        meta: {
            title: $t('tickets')
        },
        children: [
            {
                path: '/',
                name: 'tickets.list'
            },
            {
                path: 'create',
                name: 'tickets.create',
                component: CreatePage,
                meta: {
                    sidebar: true,
                    checkForm: 'tickets'
                }
            },
            {
                path: 'options/:ticketId',
                name: 'tickets.options',
                component: OptionsPage,
                meta: {
                    sidebar: true
                }
            },
            {
                path: 'view/:ticketId',
                name: 'tickets.view',
                component: ViewPage,
                meta: {
                    sidebar: true
                }
            },
            {
                path: 'edit/:ticketId',
                name: 'tickets.edit',
                component: EditPage,
                meta: {
                    sidebar: true,
                    checkForm: 'tour'
                }
            }
        ]
    }
]