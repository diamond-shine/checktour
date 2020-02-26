<template>
    <v-layout
        :locked="locked"
        :breadcrumbs="breadcrumbs"
        @close-sidebar="close"
    >
        <template #actions>
            <div class="o-layout__header-actions">
                <div
                    v-if="$can('tour-options.create')"
                    class="o-layout__header-group"
                >
                    <el-button
                        size="small"
                        type="primary"
                        @click="onClickCreate"
                    >{{ $t('Create tour option') }}
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
                            v-model="$filters('tour-options').data.term"

                            @search="list"
                        />
                    </div>
                </div>

                <v-content-list

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
                                name: 'tour-options.view',
                                params: {
                                    tourOptionId: item.id
                                }
                            }"
                            title-by="name"
                            :description-by="formatIdentifiers"

                        />
                    </template>
                </v-content-list>
            </v-scroll>
            <div class="col-xl-6 d-none d-xl-flex u-border-left">
                <div class="c-aside-icon">
                    <div class="c-aside-icon__inner">
                        <i class="icon fal fa-landmark" />
                        <div class="title">{{ $t('Tour options') }}</div>
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
    computed: {
        breadcrumbs() {
            if (!this.$data.tour) {
                return;
            }

            return breadcrumbs.generate(
                'tour-options.list',
                this.$data.tour

            );
        },

        ...mapStatuses([
            {
                name: 'list.fetch',
                process: 'tour-options@list.fetch'
            }
        ])
    },
    data() {
        return {
            items: [],
            pagination: null,
            tour: null
        }
    },
    created() {
        this.$listenCycle('tour-options@list.fetch', this.list);
    },
    beforeRouteEnter: (to, from, next) => next(async vm => {
        await vm.$filters('tour-options').restoreFromUrl(to.name);
        vm.lock(
            vm.list(), vm.view()
        );
    }),
    methods: {
        formatIdentifiers(item) {
            if (!item.bookeo_id) {
                return '';
            }

            if (Array.isArray(item.bookeo_id)) {
                return item.bookeo_id.join('|');
            }

            return item.bookeo_id;
        },
        onClickCreate() {
            this.$router.push({
                name: 'tour-options.create'
            });
        },
        fill(data) {
            this.items = data.items;
            this.pagination = data.pagination;
        },
        close() {
            this.$router.push({
                name: 'tour-options.list'
            });
        },
        onChangePage(page) {
            this.$filters('tour-options').data.page = page;

            this.list();
        },
        ...mapActions('tour-options', {
            async list(dispatch) {
                const data = await dispatch('list', {tourId: this.$routeParam('tourId')});
                this.fill(data);
            }
        }),

        ...mapActions('tours', {
            async view(dispatch, tourId = null) {
                const { view } = await dispatch('view', {
                    tourId: tourId || this.$routeParam('tourId')
                });

                this.$data.tour = view;
            }
        })
    }
}

</script>
