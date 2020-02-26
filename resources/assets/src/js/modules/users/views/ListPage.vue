<template>
    <v-layout
        :breadcrumbs="breadcrumbs"
        @close-sidebar="close"
    >
        <template #actions>
            <div class="o-layout__header-actions">
                <div
                    v-if="$can('users.create')"
                    class="o-layout__header-group"
                >
                    <el-button
                        size="small"
                        type="primary"
                        @click="onClickCreate"
                    >{{ $t('Create user') }}
                    </el-button>
                </div>
            </div>
        </template>

        <template #content>
            <v-scroll class="col-xl-6">
                <div class="col-12">
                    <div class="pt-20">
                        <v-search-panel
                            v-model="$filters('users').data.term"
                            @search="list"
                        />
                    </div>
                </div>

                <v-list
                    :items="items"
                    :pagination="pagination"
                    :loading="psList"
                    class="col-inner"
                    @change-page="onChangePage"
                >
                    <template slot-scope="{ item }">
                        <v-list-item
                            :key="item.id"
                            :data="item"
                            :route="{
                                name: 'users.view',
                                params: {
                                    userId: item.id
                                }
                            }"
                            title-by="full_name"
                            description-by="email"
                        >
                             <template #aside>
                                <div v-if="rolesViewing(item)" class="c-list__aside-item">
                                    <el-tag size="small">{{ rolesViewing(item) }}</el-tag>
                                </div>
                            </template>
                        </v-list-item>

                    </template>
                </v-list>
            </v-scroll>

            <div class="col-xl-6 d-none d-xl-flex u-border-left">
                <div class="c-aside-icon">
                    <div class="c-aside-icon__inner">
                        <i class="icon c-icon-user" />
                        <div class="title">{{ $t('Users') }}</div>
                    </div>
                </div>
            </div>
        </template>
    </v-layout>
</template>

<script>
    import { mapActions } from 'vuex';
    import store from '@store';
    import filters from '@utils/filters';

    import { mapStatuses } from '@plugins/taskManager';

    import breadcrumbs from '../breadcrumbs';

    export default {
        data: () => ({
            items: [],
            pagination: null
        }),
        computed: {
            breadcrumbs: breadcrumbs.make('users.list'),

            ...mapStatuses([
                {
                    name: 'list',
                    process: 'users@list',
                    deep: true
                }
            ])
        },
        beforeRouteEnter: async (to, from, next) => {
            await filters('users').restoreFromUrl(to.name);

            const data = await store.dispatch('users/list');

            next(
                vm => vm.fill(data)
            );
        },
        created() {
            this.$ee.on('users@list.fetch', this.list);

            this.$once(
                'hook:destroyed',
                () => this.$ee.off('users@list.fetch')
            );
        },
        destroyed() {
            this.$filters('users').clear();
        },
        methods: {
            rolesViewing(user) {
                let roles = user.roles.map(role => role.title);

                if (user.is_admin) {
                    roles.unshift(this.$t('Admin'));
                }

                return roles.join(' | ');
            },
            fill(data) {
                this.$data.items = data.items;
                this.$data.pagination = data.pagination;
            },
            close() {
                this.$router.push({
                    name: 'users.list'
                });
            },

            onClickCreate() {
                this.$router.push({
                    name: 'users.create'
                });
            },
            onChangePage(page) {
                this.$filters('users').data.page = page;

                this.list();
            },

            ...mapActions('users', {
                async list(dispatch) {
                    this.fill(
                        await dispatch('list')
                    );
                }
            })
        }
    };
</script>
