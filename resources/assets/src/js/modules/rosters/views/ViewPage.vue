<template>
    <v-sidebar-layout :locked="locked">
        <template #header>
            <v-sidebar-layout-header
                :title="$t('Roster detail page')"
                :back-route="backRoute"
            >

            </v-sidebar-layout-header>
        </template>
        <template #body>

            <v-form-preview v-if="$can('tours.list')"
                :label="noLabels ? null : $t('Tour')">
                <router-link :to="{name: 'tours.view', params: {tourId: model.tour.id}}"
                    :key="model.tour.id"
                    :style="'margin-left: 0px'"
                    exact>
                        {{ model.tour.name }}
                </router-link>
            </v-form-preview>
            <v-form-preview v-else
                :label="$t('Tour')"
                :value="model.tour.name"
            />

            <v-form-preview v-if="!isGuide"
                :label="$t('Guide')"
                :value="model.user.full_name"
            />

            <v-form-preview
                v-if="model.tour.tour_options.length"
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
                :label="$t('Options')"
                :noValue="$t('No additional options')">
            </v-form-preview>


            <v-divider />

            <v-form-preview
                v-for="option in ticketsOptionGroups"
                :key="option"
                :label="!option ? noOptionsTitle : option ">

                <el-row class="pl-5">
                    <template v-for="ticket in model.tickets_by_types">
                        <el-col
                            :key="option + ticket.id"
                            v-if="ticket.option_name == option"
                            :span="8">

                            <v-form-preview
                                :label="ticket.name"
                                class="mb-10"
                                >
                                {{ ticket.quantity }} {{ $t('tickets') }}
                            </v-form-preview>
                        </el-col>
                    </template>
                </el-row>
            </v-form-preview>

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
            </el-row>

            <v-nav-list-group>
                <v-nav-list>
                    <v-nav-list-item
                        :route="{
                            name: 'bookings.roster-bookings',
                            params: {
                                tourId: $routeParam('rosterId')
                            }
                        }"
                        :title="$t('Bookings')"
                    />
                </v-nav-list>
            </v-nav-list-group>

            <v-divider />

            <v-form
                ref="form"
                :data="formData"
                #default="{ form, submit }"
                name="roster"
                @submit="update"
            >
                <process-form
                    :form="form"
                    @submit="submit"
                    >
                </process-form>
            </v-form>

            <el-collapse>
                <el-collapse-item name="1">
                    <template slot="title" >
                        <label class="el-form-item__label">Receipts</label>
                    </template>

                    <v-form
                        class="ml-0 mr-0"
                        ref="form"
                        :data="formData"
                        #default="{ form, submit}"
                        name="roster_receipts"
                        @submit="update"
                    >
                        <receipts
                            :form="form"
                            @submit="submit"
                            >
                        </receipts>
                    </v-form>

                </el-collapse-item>
                <el-collapse-item name="2" v-if="comments.length">
                    <template slot="title" >
                        <p>Comments <b>({{ comments.length }})</b></p>
                    </template>
                    <v-comments-list
                        :items="comments"
                        ></v-comments-list>
                </el-collapse-item>
            </el-collapse>

        </template>

        <template #footer>


        </template>
    </v-sidebar-layout>
</template>
<script>

    import { mapActions } from 'vuex';
    import store from '@store';
    import { page, styles } from '@mixins';
    import { mapStatuses } from '@plugins/taskManager';
    import ProcessForm from '../components/ProcessForm';
    import Receipts from '../components/Receipts'
    import VCommentsList from '../../../components/VCommentsList';

    export default {
        mixins: [ page, styles ],
        components: {
            ProcessForm, VCommentsList, Receipts
        },
        data() {
            return {
                model: null,
                comments: [],
                isProcessing: false,
                formData: null,
                backRoute: {name: 'rosters.list'},
                images: []
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
            noOptionsTitle() {
                if (this.model.tour.no_options_titile) {
                    return this.model.tour.no_options_titile;
                }

                return this.$t('Without options');
            },
            ticketsOptionGroups() { //get unuque options name
                let grouped = []

                this.model.tickets_by_types.forEach((item) => {
                    if (!grouped.includes(item.option_name)) {
                        grouped.push(item.option_name);
                    }
                })

                return grouped;
            }
        },

        methods: {
            isOptionActive(tourOption) {
                if (!tourOption.is_active) {
                    return false;
                }

                if (this.model.disabled_options.includes(tourOption.id)) {
                    return false;
                }

                return true;
            },
            toggleProcessMode() {
                this.isProcessing = !this.isProcessing;
            },
            ...mapActions('rosters', {
                async view(dispatch, rosterId = null) {

                    const data = await dispatch('view', {
                        rosterId: rosterId || this.$routeParam('rosterId')
                    });

                    this.$data.model = data.view;
                    this.formData = data.view;
                    this.comments = data.comments;
                    this.images = [];
                },

                async update(dispatch, $form) {
                    const data = await dispatch('update', {
                        rosterId: this.$routeParam('rosterId'),
                        form: $form
                    });

                    await $form.setData(data.view);
                    this.$data.model = data.view;
                    this.comments = data.comments;
                    this.images = [];

                    this.$ee.emit('rosters@list.fetch');
                }
            })
        }
    }
</script>