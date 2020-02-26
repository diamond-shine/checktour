<template>
    <div
        :class="[ sizeClass, { 'c-empty--static': staticHeight !== false } ]"
        :style="{
            height: $lodash.isNumber(staticHeight) ? `${staticHeight}px` : '',
            'min-height': $lodash.isNumber(staticHeight) ? `${staticHeight}px` : '200px'
        }"
        class="c-empty"
    >
        <div class="c-empty__inner">
            <div
                v-if="icon"
                :class="colorClass"
                class="c-empty__icon"
            >
                <i :class="icon" />
            </div>

            <div
                :class="colorClass"
                class="c-empty__title"
            >{{ title !== null ? title : $t('Empty list…') }}
            </div>

            <template v-if="$slots.append">
                <div class="mt-10">
                    <slot name="append" />
                </div>
            </template>

            <template v-if="!$slots.append && allowedAppend">
                <div class="mt-10">
                    <el-button
                        size="small"
                        type="default"
                        @click="onClickAppend"
                    >{{ $t('Додати') }}
                    </el-button>
                </div>
            </template>

            <slot />
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            title: {
                type: String,
                default: null
            },
            size: {
                type: String,
                default: null,
                validator: value => [ 'xs', 'sm', 'md', 'lg' ].includes(value)
            },
            icon: {
                type: String,
                default: 'c-icon-question'
            },
            staticHeight: {
                type: [ Boolean, Number ],
                default: false
            },
            allowedAppend: {
                type: Boolean,
                default: false
            },
            colorClass: {
                type: String,
                default: ''
            }
        },
        computed: {
            sizeClass() {
                return this.$props.size ?
                    `c-empty--${this.$props.size}` :
                    '';
            }
        },
        methods: {
            onClickAppend() {
                this.$emit('append');
            }
        }
    };
</script>
