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
                        <div class="pt-20">
                            <v-search-panel
                                v-model="$filters('roster-bookings').data.term"
                                :allowClearing="true"
                                @search="list"
                            />
                        </div>
                    </div>
                </div>

                <bookings-list
                    :items="items"
                    :pagination="pagination"
                    :change-page="onChangePage"
                    :view-page-route="'bookings.roster-bookings.view'"
                    :unassign="unassignBooking"
                />
            </v-scroll>

            <div class="col-xl-6 d-none d-xl-flex u-border-left">
                <div class="c-aside-icon">
                    <div class="c-aside-icon__inner">
                        <i class="icon fas fa-user-tie" />
                        <div class="title">{{ $t('Roster bookings') }}</div>
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

import breadcrumbs from '../breadcrumbs/rosterBookings';
import BookingsList from '../components/RosterBookingsList';

export default {
    mixins: [ page ],
    components: {
        BookingsList
    },
    form: {
        current: 'roster-bookings',
        clearable: true
    },
    data() {
        return {
            items: [],
            pagination: null
        }
    },
    computed: {
        breadcrumbs() {
            if (this.locked) {
                return;
            }

            return breadcrumbs.generate(
                'bookings.roster-bookings',
                { name: this.$t('Roster detail'), id: this.$routeParam('rosterId') }
            );
        }
    },
    created() {
        this.$listenCycle('bookings@rosterBookings.fetch', this.list);
    },
    beforeRouteEnter: (to, from, next) => next(async vm => {
        await vm.$filters('bookings-rostered').restoreFromUrl(to.name);
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
                name: 'bookings.roster-bookings',
                rosterId: this.$routeParam('rosterId')
            });
        },
        onChangePage(page) {
            this.$filters('bookings-rostered').data.page = page;

            this.list();
        },
        ...mapActions('bookings', {
            async list(dispatch) {
                const data = await dispatch('rosterBookings', {
                    rosterId: this.$routeParam('rosterId'),
                });

                this.fill(data);
            },
            async unassignBooking(dispatch, booking) {
                this.$thisForm().setData({
                   arrived_waiting_room: true,
                    schedule_id: null
                });
                const data = await dispatch('process', {
                    bookingId: booking.id,
                    form: this.$thisForm()
                });

                this.$ee.emit('bookings@rosterBookings.fetch');
            }
        })
    }
}

</script>