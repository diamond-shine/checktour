<template>
    <el-date-picker
        v-model="model"
        :placeholder="placeholder"
        :clearable="false"
        :disabled="disabled"
        :type="withTime ? 'datetime' : 'date'"
    />
</template>

<script>
    import dayJS from 'dayjs';

    export default {
        props: {
            value: {
                type: [ String, null ],
                default: null
            },
            placeholder: {
                type: String,
                default: null
            },
            disabled: {
                type: Boolean,
                default: false
            },
            clearable: {
                type: Boolean,
                default: false
            },
            withTime: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            format() {
                return this.$props.withTime ? 'YYYY-MM-DD HH:mm:ss' : 'YYYY-MM-DD';
            },

            model: {
                get() {
                    return this.$props.value ?
                        dayJS(this.$props.value).toDate() :
                        null;
                },

                set(datetime) {
                    this.$emit(
                        'input',
                        dayJS(datetime).format(this.format)
                    );
                }
            }
        }
    };
</script>
