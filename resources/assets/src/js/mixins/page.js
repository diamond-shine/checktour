export default {
    data: () => ({
        locked: true
    }),
    methods: {
        lock(promise) {
            if (this.$lodash.isFunction(promise)) {
                promise = promise();
            }

            this.$data.locked = true;

            if (Promise.resolve(promise) === promise) {
                promise.then(
                    () => this.unlock()
                );
            }

            return promise;
        },

        unlock() {
            this.$data.locked = false;
        }
    }
};
