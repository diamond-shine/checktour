<template>
    <div>
        <span
            :style="{ paddingBottom: (height || 100) + '%' }"
            class="c-file__sizer"
            aria-hidden="true"
        />
        <div class="c-file__preview c-file__inner u-color-primary">
            <div class="c-file__preview-inner">
                <div
                    v-if="type === 'image'"
                    class="c-file__focus"
                >
                    <img :src="imageUrl">
                </div>

                <i
                    v-else
                    class="icon fal fa-file"
                />

                <div class="c-file__controls c-file__inner">
                    <slot
                        :actions="{ preview, remove: onRemove }"
                        name="actions"
                    >
                        <el-button
                            v-if="actions.includes('preview') && type === 'image'"
                            type="text"
                            icon="fal fa-search-plus"
                            class="u-btn-icon-primary"
                            @click="preview"
                        />

                        <el-button
                            v-if="actions.includes('remove') && !disabled"
                            type="text"
                            icon="el-icon-delete"
                            class="u-btn-icon-danger"
                            @click="onRemove"
                        />
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapActions } from 'vuex';

    export default {
        props: {
            data: {
                required: true,
                type: Object
            },
            actions: {
                type: Array,
                default: () => ([ 'preview', 'remove' ])
            },
            disabled: {
                type: Boolean,
                default: false
            },
            imageSize: {
                type: String,
                default: 'canvas.medium',
                validator: size => [
                    'original',
                    'canvas.small',
                    'canvas.medium',
                    'resize.small',
                    'resize.medium',
                    'scale_x.small',
                    'scale_x.medium',
                    'scale_y.small',
                    'scale_y.medium'
                ].includes(size)
            },
            adaptiveHeight: {
                type: Boolean,
                default: false
            }
        },
        data: () => ({
            height: 0
        }),
        computed: {
            type() {
                return this.$lodash.startsWith(this.$props.data.mime, 'image/') ? 'image' : 'file';
            },

            imageUrl() {
                if (this.type !== 'image') {
                    return '';
                }

                switch (this.$props.imageSize) {
                    case 'original':
                        return this.$props.data.url;

                    case 'canvas.small':
                        return this.$props.data.thumbs.canvas.small;

                    case 'canvas.medium':
                        return this.$props.data.thumbs.canvas.medium;

                    case 'resize.small':
                        return this.$props.data.thumbs.resize.small;

                    case 'resize.medium':
                        return this.$props.data.thumbs.resize.medium;

                    case 'scale_x.small':
                        return this.$props.data.thumbs.scale_x.small;

                    case 'scale_x.medium':
                        return this.$props.data.thumbs.scale_x.medium;

                    case 'scale_y.small':
                        return this.$props.data.thumbs.scale_y.small;

                    case 'scale_y.medium':
                        return this.$props.data.thumbs.scale_y.medium;
                }

                return '';
            }
        },
        watch: {
            data() {
                this.calcHeight();
            }
        },
        mounted() {
            this.calcHeight();
        },
        methods: {
            calcHeight() {
                if (!this.$props.adaptiveHeight || this.type !== 'image') {
                    return;
                }

                let height, width;

                let {
                    height: originalHeight,
                    width: originalWidth
                } = this.$props.data;

                switch (this.$props.imageSize) {
                    case 'canvas.small':
                    case 'resize.small':
                        height = width = 100;
                        break;

                    case 'canvas.medium':
                    case 'resize.medium':
                        height = width = 500;
                        break;

                    case 'scale_x.small':
                    case 'scale_x.small':
                        width = 100;
                        height = originalHeight * 100 / originalWidth;
                        break;

                    case 'scale_y.small':
                    case 'scale_y.small':
                        height = 100;
                        width = originalWidth * 100 / originalHeight;
                        break;

                    default:
                        height = originalHeight;
                        width = originalWidth;
                }

                const viewHeight = height * this.$el.offsetWidth / width;

                this.$data.height = viewHeight * 100 / this.$el.offsetWidth;
            },

            onRemove() {
                this.$emit('remove', this.$props.data);
            },

            ...mapActions('file-uploader/preview', {
                preview(dispatch) {
                    if (this.type !== 'image') {
                        throw new Error('Preview work only with images');
                    }

                    return dispatch('open', this.$props.data);
                }
            })
        }
    };
</script>
