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
                            v-model="$filters('processed').data"
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
                    :view-page-route="'processed.view'"
                />

            </v-scroll>
            <div class="col-xl-6 d-none d-xl-flex u-border-left">
                <div class="c-aside-icon">
                    <div class="c-aside-icon__inner">
                        <i class="icon far fa-clipboard-check" />
                        <div class="title">{{ $t('Processed') }}</div>
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

import breadcrumbs from '../breadcrumbs/processed';
import BookingsList from '../components/BookingsList';

export default {
    mixins: [ page ],
    components: {
        BookingsList
    },
    computed: {
        breadcrumbs: breadcrumbs.make('processed.list'),
        ...mapStatuses([
            {
                name: 'list.fetch',
                process: 'bookings@list.fetch'
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
        this.$listenCycle('bookings@list.fetch', this.list);
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
                name: 'bookings.create'
            });
        },
        fill(data) {
            this.items = data.items;
            this.pagination = data.pagination;
        },
        close() {
            this.$router.push({
                name: 'processed.list'
            });
        },
        onChangePage(page) {
            this.$filters('list-waiting-room').data.page = page;

            this.list();
        },
        ...mapActions('bookings', {
            async list(dispatch) {
                const data = await dispatch('listProcessed');

                this.fill(data);
            }
        })
    }
}

</script>