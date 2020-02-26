import chunk from '@utils/chunk';
import { $t } from '@utils/i18n';


const CreatePage = chunk(
    () => import(/* webpackChunkName: "bookings" */ '../views/CreatePage')
);

const ListPage = chunk(
    () => import(/* webackChunkName: "bookings" */ '../views/ListPage')
);

const RosterBookings = chunk(
    () => import(/* webackChunkName: "bookings" */ '../views/RosterBookings')
);

const ViewPage = chunk(
    () => import(/* webpuckChunkName: "bookings" */ '../views/ViewPage')
);

const EditPage = chunk(
    () => import(/* webpackChunkName: "bookings" */ '../views/EditPage')
);

const ForecastingPage = chunk(
    () => import(/* webpackChunkName: "bookings" */ '../views/ForecastingPage')
);

const WaitingRoomPage = chunk(
    () => import(/* webpackChunkName: "bookings" */ '../views/WaitingRoomPage')
);

const ProcessedPage = chunk(
    () => import(/* webpackChunkName: "bookings" */ '../views/ProcessedPage')
);

const RosteredPage = chunk(
    () => import(/* webpackChunkName: "bookings" */ '../views/RosteredPage')
);

let bookingChildren = [
    {
        path: '/',
        name: 'bookings.list'
    },
    {
        path: 'view/:bookingId',
        name: 'bookings.view',
        component: ViewPage,
        meta: {
            sidebar: true,
            checkForm: 'booking'
        }
    },
    {
        path: 'edit/:bookingId',
        name: 'bookings.edit',
        component: EditPage,
        meta: {
            sidebar: true,
            checkForm: 'booking'
        }
    }
];

let forecastingChildren = [
    {
        path: '/',
        name: 'forecasting.list'
    },
    {
        path: 'view/:bookingId',
        name: 'forecasting.view',
        component: ViewPage,
        meta: {
            sidebar: true,
            checkForm: 'booking'
        }
    },
    {
        path: 'edit/:ticketId',
        name: 'forecasting.edit',
        component: EditPage,
        meta: {
            sidebar: true,
            checkForm: 'booking'
        }
    }
];

let waitingRoomChildren = [
    {
        path: '/',
        name: 'waiting-room.list'
    },
    {
        path: 'create',
        name: 'waiting-room.create',
        component: CreatePage,
        meta: {
            sidebar: true,
            checkForm: 'booking'
        }
    },
    {
        path: 'view/:bookingId',
        name: 'waiting-room.view',
        component: ViewPage,
        meta: {
            sidebar: true,
            checkForm: 'booking'
        }
    },
    {
        path: 'edit/:bookingId',
        name: 'waiting-room.edit',
        component: EditPage,
        meta: {
            sidebar: true,
            checkForm: 'booking'
        }
    }
];

let rosteredChildren = [
    {
        path: '/',
        name: 'rostered.list'
    },
    // {
    //     path: 'create',
    //     name: 'rostered.create',
    //     component: CreatePage,
    //     meta: {
    //         sidebar: true,
    //         checkForm: 'booking'
    //     }
    // },
    {
        path: 'view/:bookingId',
        name: 'rostered.view',
        component: ViewPage,
        meta: {
            sidebar: true,
            checkForm: 'booking'
        }
    },
    {
        path: 'edit/:bookingId',
        name: 'rostered.edit',
        component: EditPage,
        meta: {
            sidebar: true,
            checkForm: 'booking'
        }
    }
];

let rosterChildren = [
    {
        path: '/',
        name: 'bookings.roster-bookings',
        component: RosterBookings,
    },
    {
        path: 'view/:bookingId',
        name: 'bookings.roster-bookings.view',
        component: ViewPage,
        meta: {
            sidebar: true,
            checkForm: 'booking'
        }
    }
];

let processedChildren = [
    {
        path: '/',
        name: 'processed.list',
        component: ProcessedPage,
    },
    {
        path: 'view/:bookingId',
        name: 'processed.view',
        component: ViewPage,
        meta: {
            sidebar: true,
            checkForm: 'booking'
        }
    }
];

export default [
    {
        path: "/processed",
        component: ProcessedPage,
        meta: {
            title: $t('Processed')
        },
        children: processedChildren
    },
    {
        path: "/rosters/view/:rosterId/list",
        component: RosterBookings,
        meta: {
            title: $t('Roster bookings')
        },
        children: rosterChildren
    },
    {
        path: "/bookings",
        component: ListPage,
        meta: {
            title: $t('All bookings')
        },
        children: bookingChildren
    },
    {
        path: "/forecasting",
        component: ForecastingPage,
        meta: {
            title: $t('Forecasting')
        },
        children: forecastingChildren
    },
    {
        path: "/waiting-room",
        component: WaitingRoomPage,
        meta: {
            title: $t('Waiting room')
        },
        children: waitingRoomChildren
    },
    {
        path: "/rostered",
        component: RosteredPage,
        meta: {
            title: $t('Rostered')
        },
        children: rosteredChildren
    }
];