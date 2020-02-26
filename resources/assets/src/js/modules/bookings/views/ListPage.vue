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
                            v-model="$filters('bookings').data.term"
                            @search="onChangePage(null)"
                        />
                    </div>
                </div>

                <bookings-list
                    :items="items"
                    :pagination="pagination"
                    :change-page="onChangePage"
                    :view-page-route="'bookings.view'"
                />

            </v-scroll>
            <div class="col-xl-6 d-none d-xl-flex u-border-left">
                <div class="c-aside-icon">
                    <div class="c-aside-icon__inner">
                        <i class="icon far fa-book" />
                        <div class="title">{{ $t('All bookings') }}</div>
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

import breadcrumbs from '../breadcrumbs/index';
import BookingsList from '../components/BookingsList';

export default {
    mixins: [ page ],
    components: {
        BookingsList
    },
    computed: {
        breadcrumbs: breadcrumbs.make('bookings.list'),
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
    destroyed() {
        this.$filters('bookings').clear();
    },
    beforeRouteEnter: (to, from, next) => next(async vm => {
        await vm.$filters('bookings').restoreFromUrl(to.name);
        vm.lock(
            vm.list()
        );
    }),
    methods: {
        clientName(booking) {
            return `${booking.first_name} ${booking.last_name}`
        },
        fill(data) {
            this.items = data.items;
            this.pagination = data.pagination;
        },
        close() {
            this.$router.push({
                name: 'bookings.list'
            });
        },
        onChangePage(page) {
            this.$filters('bookings').data.page = page;

            this.list();
        },
        ...mapActions('bookings', {
            async list(dispatch) {
                const data = await dispatch('list');

                this.fill(data);
            }
        })
    }
}

</script>