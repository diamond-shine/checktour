import _ from 'lodash';

export default class {
    constructor(file, url) {
        this.file = file;
        this.url = url;

        this.chunkSize = 1024 * 1024;
        this.threadsQuantity = 1;

        this.aborted = false;
        this.uploadedSize = 0;
        this.progressCache = {};
        this.activeConnections = {};

        this.headers = {};

        this.lastResponseData = null;
    }

    setHeader(name, value) {
        this.headers[ name ] = value;
    }

    setChunkSize(size) {
        this.chunkSize = size;
    }

    setThreadsQuantity(quantity) {
        this.threadsQuantity = quantity;
    }

    start() {
        if (!this.file) {
            throw new Error('Can\'t start uploading: file have not chosen');
        }

        const chunksQuantity = Math.ceil(this.file.size / this.chunkSize);

        this.chunksQueue = new Array(chunksQuantity)
            .fill()
            .map((_, index) => index)
            .reverse();

        const xhr = new XMLHttpRequest;

        xhr.open('post', this.url);

        xhr.setRequestHeader('X-Content-Length', this.file.size);
        xhr.setRequestHeader(
            'X-Content-Name',
            this.base64Encode(this.file.name)
        );
        xhr.setRequestHeader('X-Upload-Init', 'on');
        xhr.setRequestHeader('X-Chunks-Quantity', chunksQuantity);

        _.each(this.headers, (value, name) => {
            xhr.setRequestHeader(name, value);
        });

        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);

                if (!response.data.fileId) {
                    this.complete(new Error('Can\'t create file id'));
                    return;
                }

                this.fileId = response.data.fileId;

                this.sendNext();
            }
        };

        xhr.onerror = (error) => {
            this.complete(error);
        };

        xhr.send();
    }

    sendNext() {
        const activeConnections = Object.keys(this.activeConnections).length;

        if (activeConnections >= this.threadsQuantity) {
            return;
        }

        if (!this.chunksQueue.length) {
            if (!activeConnections) {
                this.complete(null);
            }

            return;
        }

        const chunkId = this.chunksQueue.pop();
        const sentSize = chunkId * this.chunkSize;
        const chunk = this.file.slice(sentSize, sentSize + this.chunkSize);

        this.sendChunk(chunk, chunkId)
            .then(() => {
                this.sendNext();
            })
            .catch((error) => {
                this.chunksQueue.push(chunkId);

                this.complete(error);
            });

        this.sendNext();
    }

    complete(error) {
        if (error && !this.aborted) {
            this.end && this.end(this.lastResponseData, error);
            return;
        }

        this.end && this.end(this.lastResponseData, null);
    }

    sendChunk(chunk, id) {
        return new Promise(
            async (resolve, reject) => {
                try {
                    const response = await this.upload(chunk, id);
                    const data = JSON.parse(response);

                    this.lastResponseData = data;

                    if (data.data.size !== chunk.size) {
                        reject(new Error('Failed chunk upload'));
                        return;
                    }
                } catch (error) {
                    reject(error);
                    return;
                }

                resolve();
            }
        );
    }

    handleProgress(chunkId, event) {
        if (event.type === 'progress'
            || event.type === 'error'
            || event.type === 'abort'
        ) {
            this.progressCache[ chunkId ] = event.loaded;
        }

        if (event.type === 'loadend') {
            this.uploadedSize += this.progressCache[ chunkId ] || 0;

            delete this.progressCache[ chunkId ];
        }

        const inProgress = Object
            .keys(this.progressCache)
            .reduce(
                (memo, id) => memo + this.progressCache[ id ],
                0
            );

        const sentLength = Math.min(this.uploadedSize + inProgress, this.file.size);

        this.onProgress({
            loaded: sentLength,
            total: this.file.size
        });
    }

    upload(file, id) {
        return new Promise(
            (resolve, reject) => {
                const xhr = this.activeConnections[ id ] = new XMLHttpRequest();
                const progressListener = this.handleProgress.bind(this, id);

                xhr.upload.addEventListener('progress', progressListener);

                xhr.addEventListener('error', progressListener);
                xhr.addEventListener('abort', progressListener);
                xhr.addEventListener('loadend', progressListener);

                xhr.open('post', this.url);

                xhr.setRequestHeader('Content-Type', 'application/octet-stream');
                xhr.setRequestHeader('X-Content-Id', this.fileId);
                xhr.setRequestHeader('X-Chunk-Id', id);

                _.each(this.headers, (value, name) => {
                    xhr.setRequestHeader(name, value);
                });

                xhr.onreadystatechange = () => {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        resolve(xhr.responseText);

                        delete this.activeConnections[ id ];
                    }
                };

                xhr.onerror = (error) => {
                    reject(error);

                    delete this.activeConnections[ id ];
                };

                xhr.onabort = () => {
                    reject(new Error('Upload canceled by user'));

                    delete this.activeConnections[ id ];
                };

                xhr.send(file);
            }
        );
    }

    on(method, callback) {
        if (typeof callback !== 'function') {
            callback = () => ({});
        }

        this[ method ] = callback;
    }

    abort() {
        Object.keys(this.activeConnections).forEach((id) => {
            this.activeConnections[ id ].abort();
        });

        this.aborted = true;
    }

    base64Encode(str) {
        return btoa(
            encodeURIComponent(str).replace(
                /%([0-9A-F]{2})/g,
                (match, p1) => String.fromCharCode('0x' + p1)
            )
        );
    }
};
