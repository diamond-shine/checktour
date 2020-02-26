import _ from 'lodash';
import pluralize from 'pluralize';
import { taskManager } from '@plugins/taskManager';
import filters from '@utils/filters';
import http from '@utils/http';

const joinModules = (modules, delimiter) => {
    if (_.isString(modules)) {
        return modules;
    }

    return modules.join(delimiter);
};

const urlPrefix = (modules, args, payload) => {
    if (modules.length === 1) {
        return modules[ 0 ];
    }

    const [ mainModule, ...submodules ] = modules;

    let submodule;
    const submoduleParts = [];

    let i = 0;

    while (submodule = submodules.shift()) {
        submoduleParts.push(`${submodule}/${payload[ args[ i++ ] ]}`);
    }

    return `${mainModule}/${submoduleParts.join('/')}`;
};

const generateOptions = (rawModules, payload) => {
    const modules = _.isString(rawModules) ? [ rawModules ] : rawModules;

    const args = modules.map(
        module => {
            const name = _.camelCase(
                pluralize.singular(module)
            );
            return `${name}Id`;
        }
    );

    return {
        taskPrefix: joinModules(modules, '.'),
        filtersName: joinModules(modules, '-'),
        urlPrefix: urlPrefix(modules, args, payload),
        args,
        currentEntityId: args[ args.length - 1 ]
    };
};

export const list = modules => async (ctx, payload) => {
    const options = generateOptions(modules, payload);

    const {
        data: { data }
    } = await taskManager.run(
        `${options.taskPrefix}@list.fetch`,
        filters(options.filtersName).apply(`${options.urlPrefix}/list`)
    );

    return data;
};

export const create = modules => async (ctx, payload) => {
    const options = generateOptions(modules, payload);

    const {
        data: { data }
    } = await taskManager.run(
        `${options.taskPrefix}@create.fetch`,
        http.get(`${options.urlPrefix}/create`)
    );

    return data;
};

export const store = modules => async (ctx, payload) => {
    const options = generateOptions(modules, payload);

    const {
        data: { data }
    } = await taskManager.run(
        `${options.taskPrefix}@create.store`,
        payload.form.submit('post', `${options.urlPrefix}/create`)
    );

    return data;
};

export const view = modules => async (ctx, payload) => {
    const options = generateOptions(modules, payload);

    const {
        data: { data }
    } = await taskManager.run(
        `${options.taskPrefix}@view.fetch`,
        http.get(`${options.urlPrefix}/view/${payload[ options.currentEntityId ]}`)
    );

    return data;
};

export const edit = modules => async (ctx, payload) => {
    const options = generateOptions(modules, payload);

    const {
        data: { data }
    } = await taskManager.run(
        [
            `${options.taskPrefix}@edit.fetch`,
            `${options.taskPrefix}@edit.${payload[ options.currentEntityId ]}.fetch`
        ],
        http.get(`${options.urlPrefix}/edit/${payload[ options.currentEntityId ]}`)
    );

    return data;
};

export const update = modules => async (ctx, payload) => {
    const options = generateOptions(modules, payload);

    const {
        data: { data }
    } = await taskManager.run(
        [
            `${options.taskPrefix}@edit.update`,
            `${options.taskPrefix}@edit.${payload[ options.currentEntityId ]}.update`
        ],
        payload.form.submit('post', `${options.urlPrefix}/edit/${payload[ options.currentEntityId ]}`)
    );

    return data;
};

export const destroy = modules => async (ctx, payload) => {
    const options = generateOptions(modules, payload);

    const {
        data: { data }
    } = await taskManager.run(
        [
            `${options.taskPrefix}@delete.destroy`,
            `${options.taskPrefix}@delete.${payload[ options.currentEntityId ]}.destroy`
        ],
        http.delete(`${options.urlPrefix}/delete/${payload[ options.currentEntityId ]}`)
    );

    return data;
};

export default (modules, except = []) => {
    const actions = {};

    if (!except.includes('list')) {
        actions.list = list(modules);
    }

    if (!except.includes('create')) {
        actions.create = create(modules);
    }

    if (!except.includes('store')) {
        actions.store = store(modules);
    }

    if (!except.includes('view')) {
        actions.view = view(modules);
    }

    if (!except.includes('edit')) {
        actions.edit = edit(modules);
    }

    if (!except.includes('update')) {
        actions.update = update(modules);
    }

    if (!except.includes('destroy')) {
        actions.destroy = destroy(modules);
    }

    return actions;
};
