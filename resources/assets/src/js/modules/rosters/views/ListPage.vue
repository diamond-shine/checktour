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
                        <v-multi-search-panel
                            v-model="$filters('rosters').data"
                            :fetchUrl="'bookings/tours-autocomplete'"
                            :placeholder="$t('Select tour')"
                            :allowClearing="true"
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
                                name: 'rosters.view',
                                params: {
                                    rosterId: item.id
                                }
                            }"
                            :title-by="noLabels ? null : 'user.full_name'"
                            description-by="tour.name"
                        >

                            <template #aside>
                                <div class="c-list__main">
                                    <div class="c-list__aside-text pr-10">{{ item.assigned_at_formated }}</div>
                                </div>

                                <div class="c-list__aside-item">
                                    <el-tag
                                        size="small">{{ item.total_price }} {{ item.currency}} | {{ item.tickets_count }} {{ $t('tickets') }}</el-tag>

                                    <!-- <div class="pt-10 pl-5">
                                        {{ item.assigned_at_formated }}
                                    </div> -->
                                </div>
                            </template>
                        </v-list-item>
                    </template>
                </v-content-list>

            </v-scroll>
            <div class="col-xl-6 d-none d-xl-flex u-border-left">
                <div class="c-aside-icon">
                    <div class="c-aside-icon__inner">
                        <i class="icon far fa-user-edit" />
                        <div class="title">{{ $t('Rosters') }}</div>
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
import { page, styles } from '@mixins';
import { mapStatuses } from '@plugins/taskManager';

import breadcrumbs from '../breadcrumbs';


export default {
    mixins: [ page, styles ],
    // components: {
    //     BookingsList
    // },
    computed: {
        breadcrumbs: breadcrumbs.make('rosters.list'),
        ...mapStatuses([
            {
                name: 'list.fetch',
                process: 'rosters@list.fetch'
            }
        ])
    },
    data() {
        return {
            items: [],
            pagination: null
        }
    },
    created() {
        this.$listenCycle('rosters@list.fetch', this.list);
    },
    destroyed() {
        this.$filters('rosters').clear();
    },
    beforeRouteEnter: (to, from, next) => next(async vm => {
        await vm.$filters('rosters').restoreFromUrl(to.name);
        vm.lock(
            vm.list()
        );
    }),
    methods: {
        fill(data) {
            this.items = data.items;
            this.pagination = data.pagination;
        },
        close() {
            this.$router.push({
                name: 'rosters.list'
            });
        },
        onChangePage(page) {
            this.$filters('rosters').data.page = page;

            this.list();
        },
        ...mapActions('rosters', {
            async list(dispatch) {
                const data = await dispatch('list');

                this.fill(data);
            }
        })
    }
}

</script>