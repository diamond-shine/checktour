<template>
    <v-sidebar-layout :locked="locked">
        <template #header>
            <v-sidebar-layout-header
                :title="$t('Booking detail page')"
                :back-route="backRoute"
            >
                <template #right>
                    <router-link
                        v-if="editMode && $can('bookings.edit')"
                        :to="{
                            name: 'bookings.edit',
                            params: {
                                bookingId: $routeParam('bookingId')
                            }
                        }"
                        :title="$t('Edit')"
                        :aria-label="$t('Edit')"
                        class="btn"
                    ><i class="icon fal fa-edit" /></router-link>
                </template>
            </v-sidebar-layout-header>
        </template>
        <template #body>

            <v-form-preview :label="noLabels ? null : $t('Tour')" class="mb-15">
                <router-link :to="{name: 'tours.view', params: {tourId: model.tour.id}}"
                    :key="model.tour.id"
                    :style="'margin-left: 0px'"
                    exact>
                        {{ model.tour.name }}
                </router-link>
            </v-form-preview>

            <v-form-preview v-if="!isGuide"
                class="mb-15"
                :label="$t('Booking number')"
                :value="model.booking_number"
            />


            <v-form-preview
                v-if="model.tour.tour_options.length"
                class="mb-15"
                :label="noLabels ? null : $t('Options')">

                <div
                    v-for="item in model.tour.tour_options"
                    :key="model.id + '_' + item.id"
                >
                    <i :class="[ isOptionActive(item) ? 'u-color-success fal fa-check' : 'u-color-danger fal fa-times' ]"
                        style="min-width: 15px;"
                    />&nbsp;
                    {{ item.name }}
                </div>
            </v-form-preview>
            <v-form-preview v-else
                class="mb-15"
                :label="noLabels ? null : $t('Options')"
                :noValue="$t('No additional options')">
            </v-form-preview>

            <v-form-preview
                class="mb-15"
                :label="noLabels ? null : $t('Event time')"
                :value="model.start_at_full"
            />

            <!-- <v-divider hrClass="mb-10 mt-0"/> -->

            <el-collapse class="mb-10">
                <el-collapse-item name="1">
                    <template slot="title" >
                        <label class="el-form-item__label">{{model.first_name}} {{model.last_name}}</label>
                    </template>

                    <el-row :gutter="20" style="margin-bottom: -30px">
                        <el-col :span="12">
                            <v-form-preview
                                class="mb-15"
                                :label="$t('Phone')"
                                :value="model.phone"
                            />
                        </el-col>

                        <el-col :span="12">
                            <v-form-preview
                                class="mb-15"
                                :label="$t('Email')"
                                :value="model.email"
                            />
                        </el-col>
                    </el-row>
                </el-collapse-item>
            </el-collapse>



            <el-row class="pl-5">
                <el-col
                    v-for="booking_ticket in model.booking_tickets"
                    :key="model.id + '_' + booking_ticket.id"
                    :span="8">
                    <v-form-preview
                        :label="booking_ticket.ticket.name"
                        class="mb-10"
                        >
                        {{ booking_ticket.quantity }} {{ $t('tickets') }}
                    </v-form-preview>
                </el-col>
            </el-row>

            <template v-if="!isGuide">
                <v-divider hrClass="mb-10 mt-0"/>
                <el-row>
                    <el-col :span="8">
                        <span class="c-form-preview__label"
                            style="display: contents">{{ $t('Total price') }}</span>
                    </el-col>
                    <el-col :span="8">
                        <v-form-preview class="mb-10">
                            <b>{{ model.total_price }} {{model.tour.currency}}</b>
                        </v-form-preview>
                    </el-col>

                    <el-col :span="8" v-if="model.start_total_price != model.total_price">
                        <v-form-preview class="mb-10 c-form-preview__label">
                            <b><strike>{{ model.start_total_price }} {{model.tour.currency}}</strike></b>
                        </v-form-preview>
                    </el-col>
                </el-row>
            </template>


            <v-divider hrClass="mb-10 mt-0"/>

            <v-form
                ref="form"
                :data="formData"
                #default="{ form, submit }"
                name="booking"
                @submit="processBooking"
            >
                <process-form
                    :form="form"
                    @submit="submit"
                    >

                </process-form>
            </v-form>
        </template>

        <template #footer>
            <v-comments-list
                v-if="comments.length"
                :items="comments"
                ></v-comments-list>

        </template>
    </v-sidebar-layout>
</template>
<script>

    import { mapActions } from 'vuex';
    import store from '@store';
    import { page, styles } from '@mixins';
    import { mapStatuses } from '@plugins/taskManager';
    import ProcessForm from '../components/ProcessForm';
    import VCommentsList from '../../../components/VCommentsList';

    export default {
        mixins: [ page, styles ],
        components: {
            ProcessForm, VCommentsList
        },
        data() {
            return {
                model: null,
                comments: [],
                isProcessing: false,
                formData: null
            }
        },
        beforeRouteEnter: (to, from, next) => next(
            vm => vm.lock(
                vm.view()
            )
        ),
        beforeRouteUpdate(to, from, next) {
            if (to.params.bookingId !== from.params.bookingId) {
                this.lock(
                    this.view(to.params.bookingId)
                );
            }

            next();
        },
        computed: {
            backRoute() {
                let route = {
                    name: 'bookings.list'
                };

                switch (this.$router.currentRoute.name) {
                    case 'waiting-room.view':
                        route.name = 'waiting-room.list';
                        break;

                    case 'forecasting.view':
                        route.name = 'forecasting.list';
                        break;

                    case 'bookings.roster-bookings.view':
                        route.name = 'bookings.roster-bookings';
                        break;

                    case 'processed.view':
                        route.name = 'processed.list';
                        break;

                    case 'rostered.view':
                        route.name = 'rostered.list';
                        break;
                }

                return route;
            },

            editMode() {
                return !this.model.schedule_id;
            }
        },

        methods: {
            isOptionActive(tourOption) {
                if (!this.model.ticket_options.length) {
                    return false;
                }

                if (this.model.schedule && this.model.schedule.disabled_options) {
                    if (this.model.schedule.disabled_options.includes(tourOption.id)) {
                        return false;
                    }
                }

                return !!this.model.ticket_options.find((item) => {
                    return item.tour_option.id == tourOption.id && item.tour_option.is_active
                });
            },
            toggleProcessMode() {
                this.isProcessing = !this.isProcessing;
            },
            ...mapActions('bookings', {
                async view(dispatch, bookingId = null) {

                    const data = await dispatch('view', {
                        bookingId: bookingId || this.$routeParam('bookingId')
                    });

                    this.$data.model = data.view;
                    this.formData = data.view;
                    this.comments = data.comments;
                },

                async processBooking(dispatch, $form) {
                    const data = await dispatch('process', {
                        bookingId: this.$routeParam('bookingId'),
                        form: $form
                    });

                    await $form.setData(data.view);
                    this.formData = data.view
                    this.$data.model = data.view;
                    this.comments = data.comments;
                    this.$ee.emit('bookings@list.fetch');
                }
            })
        }
    }
</script>