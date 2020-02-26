<template>
    <div class="c-form-preview">
        <label
            v-if="label"
            class="c-form-preview__label"
        >{{ label }}</label>

        <div class="c-form-preview__value">
            <slot>
                <span v-if="value">{{ $t('Так') }}</span>
                <span v-else>{{ $t('Ні') }}</span>
            </slot>
        </div>
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
                required: true,
                type: Boolean
            }
        }
    };
</script>
