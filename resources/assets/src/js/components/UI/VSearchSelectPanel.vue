<template>
    <div class="el-input el-input-group el-input-group--append el-input--suffix">
        <v-autocomplete
                id="game-game-subtypes"
                :url="url"
            >
            <template slot-scope="{ items, fetching }">
                <el-select v-model="model"
                    :placeholder="currentPlaceholder"
                    @change="onSearch">
                    <el-option v-for="item in items"
                        :key="'search_' + item.id"
                        :label="item.name"
                        :value="item.id"
                    />
                </el-select>
            </template>
        </v-autocomplete>

        <div v-if="model && allowClearing" class="el-input-group__append">
            <button

                style="border-right: 1px solid var(--input-border-color); border-radius: 0px;"
                class="el-button el-button--default"
                @click="onClear"
            >
                <i class="icon far fa-times"></i>
            </button>
        </div>

    </div>
</template>

<script>
    export default {
        props: {
            value: Number,
            items: {
                type: Array
            },
            placeholder: {
                type: String
            },
            fetchUrl: String,
            allowClearing: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            url() {
                return this.fetchUrl;
            },
            model: {
                get() {
                    return this.$props.value;
                },

                set(value) {
                    this.$emit('input', value);
                }
            },
            currentPlaceholder() {
                if (this.placeholder) {
                    return this.placeholder;
                }
                return this.$t('Select');
            }
        },
        methods: {
            onSearch() {
                this.$emit('search');
            },

            onClear() {
                this.model = null;
                this.$emit('search');
            },

        },
        mount() {

        }
    };
</script>
