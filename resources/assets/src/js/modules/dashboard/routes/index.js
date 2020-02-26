import chunk from '@utils/chunk';

const Dashboard = chunk(
    () => import(/* webpackChunkName: "dashboard" */ '../views/Dashboard')
);
const Home = chunk(
    () => import(/* webpackChunkName: "dashboard" */ '../views/Home')
);

export default [
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard
    },
    {
        path: '/home',
        name: 'home',
        component: Home
    }
];
