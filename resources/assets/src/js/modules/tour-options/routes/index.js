import chunk from '@utils/chunk';
import { $t } from '@utils/i18n';


const CreatePage = chunk(
    () => import(/* webpackChunkName: "tour-options" */ '../views/CreatePage')
);

const ListPage = chunk(
    () => import(/* webackChunkName: "tour-options" */ '../views/ListPage')
);

const ViewPage = chunk(
    () => import(/* webpuckChunkName: "tour-options" */ '../views/ViewPage')
);

const EditPage = chunk(
    () => import(/* webpackChunkName: "tour-options" */ '../views/EditPage')
);


export default [
    {
        path: "/tours/:tourId/tour-options",
        component: ListPage,
        meta: {
            title: $t('Tour options')
        },
        children: [
            {
                path: '/',
                name: 'tour-options.list'
            },
            {
                path: 'create',
                name: 'tour-options.create',
                component: CreatePage,
                meta: {
                    sidebar: true,
                    checkForm: 'tour-options'
                }
            },
            {
                path: 'view/:tourOptionId',
                name: 'tour-options.view',
                component: ViewPage,
                meta: {
                    sidebar: true
                }
            },
            {
                path: 'edit/:tourOptionId',
                name: 'tour-options.edit',
                component: EditPage,
                meta: {
                    sidebar: true,
                    checkForm: 'tour-options'
                }
            }
        ]
    }
]