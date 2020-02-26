import _ from 'lodash';

/**
 * @param string url
 * @param string|number|Array|Object params
 * @return string
 * @throws Error
 */
export default (url, params) => {
    const placeholders = url
        .split('/')
        .filter((param) => /\{.*\}/.test(param))
        .map((param) => param.replace('{', '').replace('}', ''))
    ;

    if (_.isArray(params)) {
        return bindArray(url, placeholders, params);
    } else if (_.isObject(params)) {
        return bindObject(url, placeholders, params);
    } else {
        return bindString(url, placeholders, params);
    }

    throw new Error('Invalid parameters');
}


/**
 * @param string url
 * @param string[] placeholders
 * @param string[] params
 */
function bindArray(url, placeholders, params) {
    const requiredPlaceholders = _.filter(
        placeholders,
        (value) => value[value.length - 1] !== '?'
    );

    if (params.length < requiredPlaceholders.length) {
        throw new Error('Missed required parameters');
    }

    return _.reduce(placeholders, (state, placeholder, index) => {
        if (!params[index]
            && requiredPlaceholders.indexOf(placeholder) === -1) {
            return state.replace(`/{${placeholder}}`, '');
        }

        return state.replace(`{${placeholder}}`, params[index]);
    }, url);
}

/**
 * @param string url
 * @param string[] placeholders
 * @param string[] params
 */
function bindObject(url, placeholders, params) {
    return _.reduce(placeholders, (state, placeholder) => {
        if (!params.hasOwnProperty(placeholder)) {
            throw new Error(`Missed required parameter [${placeholder}]`);
        }

        return state.replace(`{${placeholder}}`, params[placeholder]);
    }, url);
}

/**
 * @param string url
 * @param string[] placeholders
 * @param string[] params
 */
function bindString(url, placeholders, param) {
    if (placeholders.length > 1) {
        throw new Error('Missed required parameters');
    }

    return url.replace(`{${placeholders[0]}}`, param);
}
