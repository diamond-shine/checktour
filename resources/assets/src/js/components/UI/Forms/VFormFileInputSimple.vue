<template>
    <div>
        <span
            class="c-file__sizer"
            aria-hidden="true"
        />
        <label for="file-uploader">
        <div
            class="c-file__upload c-file__inner"

        >
            <img
                v-if="placeholderUrl"
                :src="placeholderUrl"
            >

            <i
                v-else-if="icon"
                :class="icon"
                class="icon"
            />
        </div>
        </label>
        <input
            :multiple="true"
            id="file-uploader"
            type="file"
            class="d-none"
            @change="change"
        >
    </div>
</template>

<script>
    import { mapActions } from 'vuex';

    export default {
        props: {
            icon: {
                type: String,
                default: null
            },
            placeholderUrl: {
                type: String,
                default: null
            },
            disabled: {
                type: Boolean,
                default: false
            },
            multiple: {
                type: Boolean,
                default: false
            },
            cwd: {
                type: [ Array, String, Object ],
                default: null
            },
            accept: {
                type: Array,
                default: () => ([])
            }
        },
        methods: {
            change(e) {
                this.upload(e.target.files);
            },

            ...mapActions('file-uploader/uploads', {
                upload(dispatch, files) {
                    this.$lodash.each(files, file => {
                        const request = dispatch('upload', {
                            file,
                            currentFolder: this.cwd
                        });

                        request.then(
                            ({ item }) => {
                                return this.$emit('choose', [ item ]);
                            }
                        );
                    });
                }
            })
        }
    };
</script>
