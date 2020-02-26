<template>
    <div
        :class="{ [sizeClass]: true, 'c-file--border-lg': radius }"
        class="c-file"
    >
        <div
            v-if="status === 'empty'"
            :class="{ 'c-file__upload--icon': icon }"
            class="c-file__upload c-file__inner"
            @click="$emit('click')"
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

        <div
            v-if="status === 'preview' && file"
            :class="{ 'c-file__preview--border': border, 'is-active': active }"
            class="c-file__preview c-file__inner u-color-primary"
        >
            <div class="c-file__preview-inner">
                <img :src="file.thumbs.canvas.medium">

                <div class="c-file__controls c-file__inner">
                    <el-button
                        type="text"
                        icon="fal fa-search-plus"
                        class="u-btn-icon-primary"
                        @click="$emit('preview')"
                    />
                    <el-button
                        v-if="remove"
                        type="text"
                        icon="el-icon-delete"
                        class="u-btn-icon-danger"
                        @click="$emit('remove')"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            file: {
                type: Object,
                required: false,
                default: () => ({})
            },
            radius: {
                type: Boolean,
                default: false
            },
            border: {
                type: Boolean,
                default: false
            },
            icon: {
                type: String,
                default: null
            },
            active: {
                type: Boolean,
                default: false
            },
            remove: {
                type: Boolean,
                default: true
            },
            size: {
                type: String,
                default: null,
                validator: value => ['xs', 'sm', 'md', 'lg'].includes(value)
            },
            status: {
                type: String,
                default: null,
                validator: value => ['empty', 'preview'].includes(value)
            },
            placeholderUrl: {
                type: String,
                default: null
            }
        },
        computed: {
            sizeClass() {
                return this.$props.size ?
                    `c-file--${this.$props.size}` :
                    '';
            }
        }
    };
</script>
