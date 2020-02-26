<template>
    <el-input
        v-model="inputValue"
        :size="size"
        :disabled="disabled"
        :readonly="lock"
        :tabindex="tabindex"
        :class="{ 'c-slugify--lock is-disabled': lock }"
        class="c-slugify"
    >
        <i
            slot="suffix"
            :class="iconClass"
            class="el-input__icon"
            @click.prevent="toggle"
        />
    </el-input>
</template>

<script>
    import slugify from 'voca/slugify';

    export default {
        props: {
            from: {
                type: [ String, Number ],
                default: ''
            },
            value: {
                type: [ String, Number ],
                default: ''
            },
            size: {
                type: String,
                default: undefined
            },
            disabled: {
                type: Boolean,
                default: false
            },
            tabindex: {
                type: String,
                default: undefined
            }
        },
        data() {
            return {
                lock: false,
                initialLock: this.$lodash.once(() => {
                    this.$data.lock = true;
                })
            };
        },
        computed: {
            inputValue: {
                get() {
                    if (this.$data.lock) {
                        return slugify(this.$props.from);
                    }

                    return this.$props.value;
                },
                set(value) {
                    this.$emit(
                        'input',
                        slugify(value)
                    );
                }
            },

            iconClass() {
                return this.lock ?
                    'fal fa-lock-alt' :
                    'fal fa-unlock-alt';
            }
        },
        watch: {
            inputValue: {
                handler(value) {
                    if (this.$data.lock) {
                        this.$emit('input', value);
                    }
                },
                immediate: true
            },
            value: {
                handler(value) {
                    if (!value) {
                        this.$data.initialLock();
                    }
                },
                immediate: true
            }
        },
        methods: {
            toggle() {
                this.$data.lock = !this.$data.lock;
            }
        }
    };
</script>
