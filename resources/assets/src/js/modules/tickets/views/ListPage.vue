<template>
    <v-layout
        :locked="locked"
        :breadcrumbs="breadcrumbs"
        @close-sidebar="close"
    >
        <template #actions>
            <div class="o-layout__header-actions">
                <div
                    v-if="$can('tickets.create')"
                    class="o-layout__header-group"
                >
                    <el-button
                        size="small"
                        type="primary"
                        @click="onClickCreate"
                    >{{ $t('Create ticket') }}
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
                            v-model="$filters('tickets').data.term"

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
                                name: 'tickets.view',
                                params: {
                                    ticketId: item.id
                                }
                            }"
                            title-by="name"
                            description-by="bookeo_id"
                        >
                            <template #aside>
                                <div class="c-list__aside-item">
                                    <div class="c-list__aside-text">{{ $t('Options') }}:</div>
                                </div>

                                <div class="c-list__aside-item">
                                    <el-tag size="small">{{ item.ticket_opions_count }}</el-tag>
                                    <!-- <el-tag size="small">{{ item.values_count }}</el-tag> -->
                                </div>
                            </template>
                        </v-list-item>
                    </template>
                </v-content-list>
            </v-scroll>
            <div class="col-xl-6 d-none d-xl-flex u-border-left">
                <div class="c-aside-icon">
                    <div class="c-aside-icon__inner">
                        <i class="icon fal fa-landmark" />
                        <div class="title">{{ $t('Tickets') }}</div>
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
                'tickets.list',
                this.$data.tour

            );
        },
        ...mapStatuses([
            {
                name: 'list.fetch',
                process: 'tickets@list.fetch'
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
        this.$listenCycle('tickets@list.fetch', this.list);
    },
    beforeRouteEnter: (to, from, next) => next(async vm => {
        await vm.$filters('tickets').restoreFromUrl(to.name);
        vm.lock(
            vm.list(), vm.view()
        );
    }),
    methods: {
        onClickCreate() {
            this.$router.push({
                name: 'tickets.create'
            });
        },
        fill(data) {
            this.items = data.items;
            this.pagination = data.pagination;
        },
        close() {
            this.$router.push({
                name: 'tickets.list'
            });
        },
        onChangePage(page) {
            this.$filters('tickets').data.page = page;

            this.list();
        },
        ...mapActions('tickets', {
            async list(dispatch) {
                this.$filters('tickets').data.tour_id = this.$routeParam('tourId')

                const data = await dispatch('list');

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