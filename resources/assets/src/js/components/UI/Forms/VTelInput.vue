<template>
    <vue-tel-input
        :value="number"
        :preferred-countries="preferredCountries"
        :placeholder="placeholder"
        @input="onInput"
    />
</template>

<script>
    import VueTelInput from 'vue-tel-input';

    export default {
        components: {
            VueTelInput
        },
        props: {
            value: {
                type: Object,
                default: null
            },
            preferredCountries: {
                type: Array,
                default: () => ([])
            },
            placeholder: {
                type: String,
                default: null
            }
        },
        computed: {
            number() {
                return this.$props.value?.number;
            }
        },
        methods: {
            onInput(number, country) {
                this.$emit('input', {
                    number,
                    code: country?.iso2
                });
            }
        }
    };
</script>
