import chunk from '@utils/chunk';

const ErrorPage = chunk(
    () => import(/* webpackChunkName: "errors" */ '../components/ErrorPage')
);

export default [
    {
        path: '/errors/:httpCode',
        name: 'error',
        component: ErrorPage
    }
];
