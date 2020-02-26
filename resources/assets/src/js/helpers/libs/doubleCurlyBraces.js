import _ from 'lodash';
import vocaReplaceAll from 'voca/replace_all';
import vocaTrim from 'voca/trim';

export const parse = pattern => {
    let lastIndex = 0;

    const placeholders = {};

    let startIndex;

    while ((startIndex = pattern.indexOf('{{', lastIndex)) !== -1) {
        const endIndex = pattern.indexOf('}}', startIndex);

        if (endIndex === -1) {
            lastIndex = startIndex + 2;
            continue;
        }

        lastIndex = endIndex + 2;

        const placeholder = pattern.substr(startIndex, endIndex - startIndex + 2);

        placeholders[ placeholder ] = vocaTrim(
            placeholder.substr(2, placeholder.length - 4)
        );
    }

    return placeholders;
};

export const compile = (pattern, placeholders, data) => {
    let result = pattern;

    _.each(placeholders, (field, placeholder) => {
        const value = _.get(data, field, '');

        result = vocaReplaceAll(result, placeholder, value);
    });

    return result;
};

export const parseAndCompile = (pattern, data) => compile(
    pattern,
    parse(pattern),
    data
);

export default {
    parse,
    compile,
    parseAndCompile
};
