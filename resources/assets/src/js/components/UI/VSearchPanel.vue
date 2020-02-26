<template>
    <div class="el-input el-input-group el-input-group--append el-input--suffix">
        <input
            v-model="model"
            :placeholder="$t('Search…')"
            type="text"
            autocomplete="off"
            class="el-input__inner"
            @keyup="onSearch"
        >

        <div class="el-input-group__append">
            <button
                v-if="model"
                style="border-right: 1px solid var(--input-border-color); border-radius: 0px;"
                class="el-button el-button--default"
                @click="onClear"
            >
                <i class="icon far fa-times"></i>
            </button>

            <button
                type="button"
                class="el-button el-button--default"
                @click="onSearch"
            >
                <i class="el-icon-search" />
            </button>
        </div>

    </div>
</template>

<script>
    import debounce from 'lodash/debounce'

    export default {
        props: {
            value: {
                type: String,
                default: ''
            }
        },
        computed: {
            model: {
                get() {
                    return this.$props.value;
                },

                set(value) {
                    this.$emit('input', value);
                }
            }
        },
        methods: {
            onSearch: debounce(function() {
                this.$emit('search');
            }, 400),

            onClear() {
                this.model = '';
                this.$emit('search');
            }
        }
    };
</script>
