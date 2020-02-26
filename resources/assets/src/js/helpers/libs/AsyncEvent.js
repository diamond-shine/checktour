export default class AsyncEvent {
    #resolver;
    #rejecter;

    constructor(event, payload) {
        this.event = event;
        this.payload = payload;

        this.promise = new Promise((resolve, reject) => {
            this.#resolver = resolve;
            this.#rejecter = reject;
        });
    }

    resolve(data) {
        return this.#resolver(data);
    }

    reject() {
        return this.#rejecter();
    }
}
