export default (importCallback, call = false) => {
    const callback = () => {
        const eventEmitter = require('@utils/eventEmitter').default;
        const request = importCallback();

        eventEmitter.emit('chunk-load-start');

        request
            .then(
                () => eventEmitter.emit('chunk-load-end')
            )
            .catch(
                () => eventEmitter.emit('chunk-load-fail')
            );

        return request;
    };

    return call ? callback() : callback;
};
