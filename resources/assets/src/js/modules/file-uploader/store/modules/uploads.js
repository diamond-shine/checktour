import Uploader from '../../libs/uploader';
import uuidV4 from 'uuid/v4';
import _ from 'lodash';

export default {
    namespaced: true,
    state: {
        uploading: []
    },
    mutations: {
        ADD_UPLOADING: (state, data) => {
            state.uploading.unshift(data);
        },

        SET_UPLOAD_PROGRESS: (state, { id, percent }) => {
            const index = _.findIndex(state.uploading, { id });

            if (index !== -1) {
                const data = state.uploading[ index ];

                state.uploading.splice(index, 1, {
                    ...data,

                    percent
                });
            }
        },

        FINISH_FILE_UPLOADING: (state, { id }) => {
            const uploadedFileIndex = _.findIndex(state.uploading, { id });

            if (uploadedFileIndex !== -1) {
                state.uploading.splice(uploadedFileIndex, 1);
            }
        },

        RESET: state => state.uploading = []
    },
    actions: {
        upload: ({ commit, rootState }, { file, currentFolder = null }) => {

            return new Promise(resolve => {
                let uploader;
                if (currentFolder) {
                    uploader = new Uploader(file, `/api/file-manager/uploads/upload`);
                } else {
                    uploader = new Uploader(file, `/api/file-manager/uploads/upload/${currentFolder?.id || ''}`);
                }

                uploader.setHeader('X-CSRF-TOKEN', window.App.csrf_token);

                if (rootState[ 'file-uploader' ].config.scope) {
                    uploader.setHeader('X-SCOPE-KEY', rootState[ 'file-uploader' ].config.scope);
                }

                if (rootState[ 'file-uploader' ].config.privateMode) {
                    uploader.setHeader('X-PRIVATE-MODE', 'yes');
                }

                const uploadingFile = {
                    id: uuidV4(),
                    percent: 0
                };

                commit('ADD_UPLOADING', uploadingFile);

                uploader.on('onProgress', ({ loaded, total }) => {
                    commit('SET_UPLOAD_PROGRESS', {
                        id: uploadingFile.id,
                        percent: Math.round(loaded / total * 100 * 100) / 100
                    });
                });

                uploader.on('end', ({ data = null }, error) => {
                    if (error) {
                        throw error;
                    }

                    if (!data) {
                        throw new Error('File not loaded');
                    }

                    commit('FINISH_FILE_UPLOADING', {
                        id: uploadingFile.id,
                        file: data.item
                    });

                    resolve(data);
                });

                uploader.start();
            });
        },

        reset: ({ commit }) => commit('RESET')
    }
};
