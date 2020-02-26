<template>
    <div>
        <slot
            :fetch="fetch"
            :fetching="psFetch"
            :items="payload.items || []"
            :pagination="payload.pagination"
            :fetch-suggestions="(...args) => search(args, fetch)"
        />
    </div>
</template>

<script>
    import uuidV4 from 'uuid/v4';
    import { mapStatuses } from '@plugins/taskManager';
    import { mapActions, mapGetters } from 'vuex';

    export default {
        props: {
            id: {
                type: String,
                default: () => uuidV4()
            },
            url: {
                required: true,
                type: String
            },
            params: {
                type: Object,
                default: () => ({})
            },
            initialFetch: {
                type: Boolean,
                default: true
            },
            manualFetch: {
                type: Boolean,
                default: false
            },
            refresh: {
                type: Function,
                default: null
            },
            disabled: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            payload() {
                return this.instance(this.$props.id);
            },

            ...mapGetters('autocomplete', [ 'instance' ]),

            ...mapStatuses([
                {
                    name: 'fetch',
                    process() {
                        return `autocomplete@${this.$props.id}.fetch`;
                    }
                }
            ])
        },
        watch: {
            params(value, oldValue) {
                if (!this.$props.manualFetch
                    && JSON.stringify(
                        value
                    ) !== JSON.stringify(
                        oldValue
                    )
                ) {
                    this.fetchWithParams();
                }
            },
            url(value, oldValue) {
                if (!this.$props.manualFetch
                    && value !== oldValue
                ) {
                    this.fetchWithParams();
                }
            },
            disabled(value) {
                if (!value) {
                    this.fetchWithParams();
                }
            }
        },
        mounted() {
            if (this.$props.initialFetch
                && !this.$props.manualFetch
            ) {
                this.fetchWithParams();
            }

            if (this.$props.refresh) {
                this.$props.refresh(
                    (params = null) => {
                        if (params !== null) {
                            return this.fetchWithParams();
                        } else {
                            return this.fetch(params);
                        }
                    }
                );
            }
        },
        methods: {
            async search([ term, resolve ], fetch) {
                const items = await fetch({ term });

                resolve(items);
            },

            ...mapActions('autocomplete', {
                async fetchWithParams() {
                    return this.fetch(this.$props.params);
                },

                fetch: async function (dispatch, params) {
                    if (this.$props.disabled) {
                        return;
                    }

                    const { id, url } = this.$props;

                    const { items } = await this.$taskManager.run(
                        `autocomplete@${id}.fetch`,
                        dispatch('fetch', { id, url, params })
                    );

                    this.$emit('fetched', items);

                    return items;
                }
            })
        }
    };
</script>