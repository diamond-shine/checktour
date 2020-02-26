<template>
    <v-layout
        :locked="locked"
        :breadcrumbs="breadcrumbs"
        @close-sidebar="close"
    >
        <template #actions>
            <div class="o-layout__header-actions">
                <div
                    v-if="$can('user-roles.create')"
                    class="o-layout__header-group"
                >
                    <el-button
                        size="small"
                        type="primary"
                        @click="onClickCreate"
                    >{{ $t('Створити роль користувача') }}
                    </el-button>
                </div>
            </div>
        </template>

        <template #content="{ locked }">
            <v-scroll
                :locked="locked"
                class="col-xl-6"
            >
                <div class="col-12">
                    <div class="pt-20">
                        <v-search-panel
                            v-model="$filters('user-roles').data.term"
                            :disabled="psListFetch"
                            @search="list"
                        />
                    </div>
                </div>

                <v-content-list
                    :loading="psListFetch"
                    :items="items"
                    :pagination="pagination"
                    class="col-inner"
                    @change-page="onChangePage"
                >
                    <template slot-scope="{ item }">
                        <v-list-item
                            :key="item.id"
                            :data="item"
                            :route="{
                                name: 'user-roles.view',
                                params: {
                                    userRoleId: item.id
                                }
                            }"
                            title-by="name"
                        />
                    </template>
                </v-content-list>
            </v-scroll>

            <div class="col-xl-6 d-none d-xl-flex u-border-left">
                <div class="c-aside-icon">
                    <div class="c-aside-icon__inner">
                        <i class="icon c-icon-team" />
                        <div class="title">{{ $t('Ролі користувачів') }}</div>
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
    import { page } from '@mixins';
    import { mapStatuses } from '@plugins/taskManager';

    import breadcrumbs from '../breadcrumbs';

    export default {
        mixins: [ page ],
        data: () => ({
            items: [],
            pagination: null
        }),
        computed: {
            breadcrumbs: breadcrumbs.make('user-roles.list'),

            ...mapStatuses([
                {
                    name: 'list.fetch',
                    process: 'user-roles@list.fetch'
                }
            ])
        },
        beforeRouteEnter: (to, from, next) => next(async vm => {
            await vm.$filters('user-roles').restoreFromUrl(to.name);

            vm.lock(
                vm.list()
            );
        }),
        created() {
            this.$listenCycle('user-roles@list.fetch', this.list);
        },
        destroyed() {
            this.$filters('user-roles').clear();
        },
        methods: {
            fill(data) {
                this.$data.items = data.items;
                this.$data.pagination = data.pagination;
            },
            close() {
                this.$router.push({
                    name: 'user-roles.list'
                });
            },
            onClickCreate() {
                this.$router.push({
                    name: 'user-roles.create'
                });
            },
            onChangePage(page) {
                this.$filters('user-roles').data.page = page;

                this.list();
            },

            ...mapActions('user-roles', {
                async list(dispatch) {
                    const data = await dispatch('list');

                    this.fill(data);
                }
            })
        }
    };
</script>
