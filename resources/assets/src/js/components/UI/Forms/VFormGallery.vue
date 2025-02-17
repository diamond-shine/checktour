<template>
    <draggable
        :class="{ 'c-gallery--with-main': withMain }"
        :value="value"
        :move="onCheckMove"
        draggable=".c-gallery__item--draggable"
        class="c-gallery"
        @input="onUpdateOrder"
    >
        <div
            v-for="(item, index) in value"
            :key="`${index}-${item.id}`"
            class="c-gallery__item c-gallery__item--draggable"
        >
            <div
                :class="size ? `c-file--${size}` : ''"
                class="c-file"
            >
                <v-form-file-preview
                    :data="item"
                    @remove="onRemove(index)"
                />
            </div>
        </div>

        <div
            v-if="!disabled"
            class="c-gallery__item disabled"
        >
            <div
                :class="size ? `c-file--${size}` : ''"
                class="c-file"
            >
                <v-form-file-input-simple
                    :placeholder-url="placeholderUrl"
                    :icon="icon"
                    :disabled="disabled"
                    :cwd="cwd"
                    multiple
                    @choose="onChoose"
                />
            </div>
        </div>
    </draggable>
</template>

<script>
    import Draggable from 'vuedraggable';

    export default {
        components: {
            Draggable
        },
        props: {
            value: {
                type: Array,
                default: () => ([])
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
                default: null,
                validator: value => [ 'xs', 'sm', 'md', 'lg', 'relative' ].includes(value)
            },
            placeholderUrl: {
                type: String,
                default: null
            },
            actions: {
                type: Array,
                default: () => ([ 'preview', 'remove' ])
            },
            withMain: {
                type: Boolean,
                default: false
            },
            disabled: {
                type: Boolean,
                default: false
            },
            type: {
                type: String,
                default: 'file'
            }
        },
        methods: {
            onCheckMove: ({ related }) => !related.classList.contains('disabled'),

            onUpdateOrder(items) {
                let order = 0;

                items.forEach(element => {
                    element.weight = order++;
                });

                return this.$emit('input', items);
            },

            onChoose(value) {
                const files = [ ...this.$props.value, ...value ];

                files.forEach((file, key) => file.weight = key);

                this.$emit('input', files);
            },

            onRemove(index) {
                const items = [ ...this.$props.value ];

                items.splice(index, 1);

                this.$emit('input', items);
            }
        }
    };
</script>
