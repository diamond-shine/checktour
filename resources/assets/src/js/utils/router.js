import _ from 'lodash';

export const routeGroup = (prefix, routes) => {
    prefix = _.trimEnd(prefix, '/');

    return routes.map(route => {
        route.path = `${prefix}/${_.trimStart(route.path, '/')}`;

        return route;
    });
};
