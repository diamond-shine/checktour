<template>
    <div
        :class="size ? `c-file--${size}` : ''"
        class="c-file"
    >
        <v-form-file-input
            v-if="!value"
            :placeholder-url="placeholderUrl"
            :icon="icon"
            :cwd="cwd"
            :disabled="disabled"
            :accept="accept"
            @choose="onChoose"
        />

        <v-form-file-preview
            v-else
            :data="value"
            :actions="actions"
            :disabled="disabled"
            :image-size="imageSize"
            :adaptive-height="adaptiveHeight"
            @remove="onRemove"
        />
    </div>
</template>

<script>
    export default {
        props: {
            value: {
                type: Object,
                default: null
            },
            icon: {
                type: String,
                default: null
            },
            cwd: {
                type: [ Array, String, Object ],
                default: null
            },
            size: {
                type: String,
                default: 'md',
                validator: value => [ 'xs', 'sm', 'md', 'lg', 'relative' ].includes(value)
            },
            imageSize: {
                type: String,
                default: 'canvas.medium'
            },
            adaptiveHeight: {
                type: Boolean,
                default: false
            },
            placeholderUrl: {
                type: String,
                default: null
            },
            actions: {
                type: Array,
                default: () => ([ 'preview', 'remove' ])
            },
            disabled: {
                type: Boolean,
                default: false
            },
            type: {
                type: String,
                default: 'file'
            },
            accept: {
                type: Array,
                default: () => ([])
            }
        },
        methods: {
            onChoose(value) {
                this.$emit('input', value);
            },

            onRemove() {
                this.$emit('input', null);
            }
        }
    };
</script>
