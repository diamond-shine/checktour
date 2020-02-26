<template>
    <v-layout
        :locked="locked"
        :breadcrumbs="breadcrumbs"
        @close-sidebar="close"
    >
        <template #actions>
            <div class="o-layout__header-actions">
                <div
                    v-if="$can('bookings.create')"
                    class="o-layout__header-group"
                >
                    <el-button
                        size="small"
                        type="primary"
                        @click="onClickCreate"
                    >{{ $t('Create booking') }}
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
                        <v-multi-search-panel
                            v-model="$filters('bookings-waiting-room').data"
                            :fetchUrl="'bookings/tours-autocomplete'"
                            :placeholder="$t('Select tour')"
                            :allowClearing="true"
                            @search="list"
                        />
                    </div>
                </div>

                <bookings-list
                    :items="items"
                    :pagination="pagination"
                    :change-page="onChangePage"
                    :view-page-route="'waiting-room.view'"
                />

            </v-scroll>
            <div class="col-xl-6 d-none d-xl-flex u-border-left">
                <div class="c-aside-icon">
                    <div class="c-aside-icon__inner">
                        <i class="icon far fa-hourglass" />
                        <div class="title">{{ $t('Waiting room') }}</div>
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

import breadcrumbs from '../breadcrumbs/waitingRoom';
import BookingsList from '../components/BookingsList';

export default {
    mixins: [ page ],
    components: {
        BookingsList
    },
    computed: {
        breadcrumbs: breadcrumbs.make('waiting-room.list'),
        ...mapStatuses([
            {
                name: 'list.fetch',
                process: 'bookings@listWaitingRoom.fetch'
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
        this.$listenCycle('bookings@listWaitingRoom.fetch', this.list);
    },
    beforeRouteEnter: (to, from, next) => next(async vm => {
        await vm.$filters('list-waiting-room').restoreFromUrl(to.name);
        vm.lock(
            vm.list()
        );
    }),
    methods: {
        clientName(booking) {
            return `${booking.first_name} ${booking.last_name}`
        },
        onClickCreate() {
            this.$router.push({
                name: 'waiting-room.create'
            });
        },
        fill(data) {
            this.items = data.items;
            this.pagination = data.pagination;
        },
        close() {
            this.$router.push({
                name: 'waiting-room.list'
            });
        },
        onChangePage(page) {
            this.$filters('bookings-waiting-room').data.page = page;

            this.list();
        },
        ...mapActions('bookings', {
            async list(dispatch) {
                const data = await dispatch('listWaitingRoom');

                this.fill(data);
            }
        })
    }
}

</script>