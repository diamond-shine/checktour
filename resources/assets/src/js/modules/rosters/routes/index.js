import chunk from '@utils/chunk';
import { $t } from '@utils/i18n';

const ListPage = chunk(
    () => import(/* webackChunkName: "rosters" */ '../views/ListPage')
);

const ViewPage = chunk(
    () => import(/* webpuckChunkName: "rosters" */ '../views/ViewPage')
);

let children = [
    {
        path: '/',
        name: 'rosters.list'
    },
    {
        path: 'view/:rosterId',
        name: 'rosters.view',
        component: ViewPage,
        meta: {
            sidebar: true,
            checkForm: 'roster'
        }
    }
];

export default [
    {
        path: "/rosters",
        component: ListPage,
        meta: {
            title: $t('Rosters')
        },
        children: children
    }
];