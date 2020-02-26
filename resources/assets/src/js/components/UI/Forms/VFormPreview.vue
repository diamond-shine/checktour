<template>
    <div class="c-form-preview">
        <label
            v-if="label"
            class="c-form-preview__label"
        >{{ label }}</label>

        <div
            v-if="(value !== undefined && value !== null) || $slots.default"
            class="c-form-preview__value"
        >
            <slot>
                <template v-if="typeof value === 'boolean'">
                    <i :class="[ value ? 'u-color-success fal fa-check' : 'u-color-danger fal fa-times' ]" />
                </template>
                <template v-else>
                    {{ value }}
                </template>
            </slot>
        </div>

        <div
            v-else
            class="c-form-preview__value text-muted"
        ><em>({{ noValue }})</em></div>
    </div>
</template>

<script>
    export default {
        props: {
            label: {
                type: String,
                default: null
            },
            value: {
                required: false
            },
            noValue: {
                type: String,
                default() {
                    return this.$t('Value not set');
                }
            }
        }
    };
</script>
