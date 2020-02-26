<template>
    <v-sidebar-layout :locked="locked">
        <template #header>
            <v-sidebar-layout-header
                :title="$t('Ticket detail page')"
                :back-route="backRoute"
            >
                <template #right>
                    <router-link
                        v-if="$can('tickets.edit')"
                        :to="{
                            name: 'tickets.edit',
                            params: {
                                ticketId: $routeParam('ticketId')
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
            <v-form-preview
                :label="$t('Name')"
                :value="model.name"
            />

            <v-form-preview
                :label="$t('Bookeo type identifier')"
                :value="model.bookeo_type"
            />

            <v-form-preview :label="$t('Tour')" >
                <router-link :to="{name: 'tours.view', params: {tourId: model.tour.id}}"
                    :key="model.tour.id"
                    :style="'margin-left: 0px'"
                    exact>
                        {{ model.tour.name }}
                </router-link>
            </v-form-preview>


            <v-form-preview
                :label="$t('Base ticket price')"
                :value="model.price"
            />

            <v-form-preview
                :label="$t('Total price')"
                :value="model.total_price"
            />

            <v-form-preview
                :label="$t('Active')"
                :value="model.is_active"
            />

            <v-nav-list-group>
                <v-nav-list>
                    <v-nav-list-item
                        v-if="$can('tickets.view')"
                        :route="{
                            name: 'tickets.options',
                            params: {
                                ticketId: $routeParam('ticketId')
                            }
                        }"
                        :title="$t('Options')"
                    />
                </v-nav-list>
            </v-nav-list-group>

        </template>

        <template #footer>
            <button
                v-if="$can('tickets.delete')"
                type="button"
                class="c-delete-btn"
                @click="destroy"
            >
                <i
                    class="icon fal fa-trash-alt"
                    aria-hidden="true"
                />
                <span class="title">{{ $t('Delete') }}</span>
            </button>
        </template>
    </v-sidebar-layout>
</template>
<script>

    import { mapStatuses } from '@plugins/taskManager';
    import { mapActions } from 'vuex';
    import store from '@store';
    import { page } from '@mixins';

    export default {
        mixins: [ page ],
        data() {
            return {
                model: null
            }
        },
        beforeRouteEnter: (to, from, next) => next(
            vm => vm.lock(
                vm.view()
            )
        ),
        beforeRouteUpdate(to, from, next) {
            if (to.params.ticketId !== from.params.ticketId) {
                this.lock(
                    this.view(to.params.ticketId)
                );
            }

            next();
        },
        computed: {
            backRoute() {
                return {
                    name: 'tickets.list'
                };
            }
        },

        methods: {
            ...mapActions('tickets', {
                async view(dispatch, ticketId = null) {
                    const { view } = await dispatch('view', {
                        ticketId: ticketId || this.$routeParam('ticketId')
                    });

                    this.$data.model = view;
                },
                async destroy(dispatch) {
                    this.$vConfirmDelete(
                        this.$t('Delete ticket?'),
                    ).then(() => {
                        dispatch('destroy', {
                            ticketId: this.$data.model.id
                        });

                        this.$ee.emit('tickets@list.fetch');

                        this.$router.push(this.backRoute);
                    })
                    .catch( error => {
                        console.log('Deleting canceled')
                    });
                }
            })
        }
    }
</script>