<template>
    <el-select
        :value="selected"
        :loading="loading || creating"
        :placeholder="placeholderText"
        :remote-method="onSearch"
        :disabled="disabled"
        :multiple="multiple"
        filterable
        remote
        clearable
        @change="onChange($event, options, term)"
        @visible-change="onVisibleChange"
        @clear="onClear"
        @focus="onFocus"
    >
        <slot v-if="current">
            <el-option
                v-if="!multiple"
                :key="current[valueKey]"
                :label="current[labelKey]"
                :value="current[valueKey]"
            />

            <template v-else>
                <el-option
                    v-for="currentItem in current"
                    :key="currentItem[valueKey]"
                    :label="currentItem[labelKey]"
                    :value="currentItem[valueKey]"
                />
            </template>
        </slot>

        <slot
            v-for="item in items"
            :item="item"
        >
            <el-option
                :key="item[valueKey]"
                :label="item[labelKey]"
                :value="item[valueKey]"
            />
        </slot>

        <el-option
            v-if="allowCreate && term && notExists"
            :label="text"
            value="CREATE_NEW"
        />
    </el-select>
</template>

<script>
    export default {
        props: {
            options: {
                required: true,
                type: Array
            },
            value: {
                type: [ Object, Array ],
                default: undefined
            },
            term: {
                type: String,
                default: ''
            },
            placeholder: {
                type: String,
                default: undefined
            },
            loading: {
                type: Boolean,
                default: false
            },
            valueKey: {
                type: String,
                default: 'id'
            },
            labelKey: {
                type: String,
                default: 'title'
            },
            createText: {
                type: String,
                default: null
            },
            allowCreate: {
                type: Boolean,
                default: false
            },
            disabled: {
                type: Boolean,
                default: false
            },
            multiple: {
                type: Boolean,
                default: false
            }
        },
        data: () => ({
            created: null,
            creating: false
        }),
        computed: {
            placeholderText() {
                if (this.$props.value && this.$props.value.length) {
                    return;
                }

                return !this.$props.options.length ?
                    this.$t('No options') :
                    this.$props.placeholder;
            },

            current() {
                return this.$data.created || this.$props.value;
            },

            selected() {
                const {
                    value,
                    valueKey,
                    multiple
                } = this.$props;

                if (value) {
                    return multiple ?
                        value.map(item => item[ valueKey ]) :
                        value[ valueKey ];
                }

                return undefined;
            },

            text() {
                return this.$ti(
                    this.$props.createText || this.$t('Створити «%s»'),
                    [ this.$props.term ]
                );
            },

            items() {
                const {
                    options,
                    valueKey,
                    multiple
                } = this.$props;

                if (!this.current) {
                    return options;
                }

                return this.$lodash.filter(options, option => {
                    return multiple ?
                        this.$lodash.findIndex(
                            this.current,
                            {
                                [ valueKey ]: option[ valueKey ]
                            }
                        ) === -1 :
                        option[ valueKey ] !== this.current[ valueKey ];
                });
            },

            notExists() {
                const term = this.$lodash.lowerCase(this.$props.term);

                return !this.$props.options.some(
                    option => this.$lodash.lowerCase(option[ this.$props.labelKey ]) === term
                );
            }
        },
        methods: {
            onSearch(term) {
                this.$emit('search', term);
            },
            onClear() {
                this.$data.created = null;
            },
            onFocus(event) {
                this.$emit('focus', event);
            },
            onVisibleChange(status) {
                this.$emit('visible-change', status);
            },

            async onChange(id, items, term) {
                if (this.$props.multiple) {
                    this.changeMultiple(id, items, term);
                } else {
                    this.changeSingular(id, items, term);
                }
            },

            async changeSingular(id, items, term) {
                const item = await this.findOrCreate(id, items, term);

                if (item?.was_recently_created) {
                    this.$data.created = item;
                }

                this.$nextTick(
                    () => {
                        this.$emit('input', item);
                        this.$emit('select', item);
                    }
                );
            },

            async changeMultiple(ids, items, term) {
                const result = [];
                const created = [];

                items = [
                    ...items,

                    ...(this.$props.value || [])
                ];

                for (const id of ids) {
                    const item = await this.findOrCreate(id, items, term);

                    if (item) {
                        result.push(item);

                        if (item.was_recently_created) {
                            created.push(item);
                        }
                    }
                }

                if (created.length) {
                    this.$data.created = created;
                }

                this.$nextTick(
                    () => {
                        this.$emit('input', result);
                        this.$emit('select', result);
                    }
                );
            },

            async findOrCreate(id, items, term) {
                let item;

                if (id === 'CREATE_NEW') {
                    item = {
                        ...await this.create(term),

                        was_recently_created: true
                    };
                } else {
                    item = this.$lodash.find(items, {
                        [ this.$props.valueKey ]: id
                    });
                }

                return item;
            },

            async create(value) {
                this.$data.creating = true;

                const item = await new Promise(
                    resolve => this.$emit('create', { value, resolve })
                );

                this.$data.creating = false;

                return item;
            }
        }
    };
</script>
