<template>
    <v-sidebar-layout :locked="locked">
        <template #header>
            <v-sidebar-layout-header
                :title="$t('Tour detail page')"
                :back-route="backRoute"
            >
                <template #right>
                    <router-link
                        v-if="$can('tours.edit')"
                        :to="{
                            name: 'tours.edit',
                            params: {
                                tourId: $routeParam('tourId')
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

            <v-form-preview :label="$t('Bookeo identifier')">
                <div v-for="identifier in model.bookeo_id" :key="identifier">
                    {{ identifier }}
                </div>
            </v-form-preview>

            <v-form-preview
                :label="$t('Title «No options»')"
                :value="model.no_options_title"
            />


            <v-form-preview
                :label="$t('Active')"
                :value="model.is_active"
            />

            <v-nav-list-group v-if="$can('tours-users.view')">
                <v-nav-list>
                    <v-nav-list-item
                        :route="{
                            name: 'tours.users',
                            params: {
                                tourId: $routeParam('tourId')
                            }
                        }"
                        :title="$t('Users') + ' (' + model.users_count + ')'"
                    />
                </v-nav-list>
            </v-nav-list-group>

            <v-nav-list-group v-if="$can('tour-options.list')">
                <v-nav-list>
                    <v-nav-list-item
                        :route="{
                            name: 'tour-options.list',
                            params: {
                                tourId: $routeParam('tourId')
                            }
                        }"
                        :title="$t('Options') + ' (' + model.tour_options_count + ')'"
                    />
                </v-nav-list>
            </v-nav-list-group>

            <v-nav-list-group v-if="$can('tickets.list')">
                <v-nav-list>
                    <v-nav-list-item
                        :route="{
                            name: 'tickets.list',
                            params: {
                                tourId: $routeParam('tourId')
                            }
                        }"
                        :title="$t('Tickets')"
                    />
                </v-nav-list>
            </v-nav-list-group>

            <v-nav-list-group v-if="$can('excursions.list')">
                <v-nav-list>
                    <v-nav-list-item
                        :route="{
                            name: 'tours.excursions',
                            params: {
                                tourId: $routeParam('tourId')
                            }
                        }"
                        :title="$t('Schedule')  + ' (' + model.excursions_count + ')'"
                    />
                </v-nav-list>
            </v-nav-list-group>
            <v-divider />
        </template>


<!--         <template #footer>
            <button
                v-if="$can('tours.delete')"
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
        </template> -->
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

            if (to.params.tourId !== from.params.tourId) {
                this.lock(
                    this.view(to.params.tourId)
                );
            }

            next();
        },
        computed: {
            backRoute() {
                return {
                    name: 'tours.list'
                };
            }
        },

        methods: {
            ...mapActions('tours', {
                async view(dispatch, tourId = null) {
                    const { view } = await dispatch('view', {
                        tourId: tourId || this.$routeParam('tourId')
                    });

                    this.$data.model = view;
                },
                async destroy(dispatch) {
                    this.$vConfirmDelete(
                        this.$t('Delete tour?'),
                    ).then(() => {
                        dispatch('destroy', {
                            tourId: this.$data.model.id
                        });

                        this.$ee.emit('tours@list.fetch');

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