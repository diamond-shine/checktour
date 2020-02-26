<template>
    <v-layout
        :locked="locked"
        :breadcrumbs="breadcrumbs"
        @close-sidebar="close"
    >

        <template #content="{ locked }">
            <v-scroll
                :locked="locked"
                class="col-xl-6"
            >
                <div class="col-12">
                    <div class="pt-20">
                        <v-search-panel
                            v-model="$filters('schedules').data.term"
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
                                name: 'schedules.view',
                                params: {
                                    scheduleId: item.id
                                },
                                query: {tour_id: $filters('schedules').data.tour_id}
                            }"
                            :title-by="userTitle"
                            description-by="tour.name"
                        >
                            <template #aside>
                                <div class="c-list__aside-item">
                                    <div class="c-list__aside-text">{{ $t('Today') }}:</div>
                                </div>
                                <div class="c-list__aside-item">
                                    <el-tag size="small">{{ item.today_schedules_count }}</el-tag>
                                </div>

                                <div class="c-list__aside-item">
                                    <div class="c-list__aside-text">{{ $t('Tomorrow') }}:</div>
                                </div>
                                <div class="c-list__aside-item">
                                    <el-tag size="small">{{ item.tomorrow_schedules_count }}</el-tag>
                                </div>
                            </template>
                        </v-list-item>
                    </template>
                </v-content-list>
            </v-scroll>
            <div class="col-xl-6 d-none d-xl-flex u-border-left">
                <div class="c-aside-icon">
                    <div class="c-aside-icon__inner">
                        <i class="icon fal fa-calendar-edit" />
                        <div class="title">{{ $t('Sessions') }}</div>
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
            return breadcrumbs.generate('schedules.list');
        },
        ...mapStatuses([
            {
                name: 'list.fetch',
                process: 'schedules@list.fetch'
            }
        ])
    },
    data() {
        return {
            items: [],
            pagination: null,
            tourId: parseInt(this.$routeParam('tourId')),
            tour: null
        }
    },
    created() {
        this.$listenCycle('schedules@list.fetch', this.list);
    },
    beforeRouteEnter: (to, from, next) => next(async vm => {
        await vm.$filters('schedules').restoreFromUrl(to.name);
        vm.$filters('schedules').data.tour_id = to.params.tourId;

        vm.lock(
            vm.list(), vm.view()
        );
    }),
    beforeRouteUpdate (to, from, next) {
        this.$filters('schedules').data.tour_id = to.params.tourId;

        this.list();
        this.view();
        next()
    },
    methods: {
        userTitle(item) {
            return item.full_name;
        },
        onClickCreate() {
            this.$router.push({
                name: 'schedules.create'
            });
        },
        fill(data) {
            this.items = data.items;
            this.pagination = data.pagination;
        },
        close() {
            this.$router.push({
                name: 'schedules.list'
            });
        },
        onChangePage(page) {
            this.$filters('schedules').data.page = page;

            this.list();
        },
        ...mapActions('schedules', {
            async list(dispatch) {

                const data = await dispatch('list');

                this.fill(data);
            }
        }),
        ...mapActions('tours', {
            async view(dispatch, tourId = null) {
                const { view } = await dispatch('view', {
                    tourId: this.$filters('schedules').data.tour_id
                });

                this.$data.tour = view;
            }
        })
    }
}

</script>
