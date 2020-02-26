<template>
    <v-sidebar-layout :locked="locked">
        <template #header>
            <v-sidebar-layout-header
                :title="$t('Tour option detail page')"
                :back-route="backRoute"
            >
                <template #right>
                    <router-link
                        v-if="$can('tour-options.edit')"
                        :to="{
                            name: 'tour-options.edit',
                            params: {
                                tourOptionId: $routeParam('tourOptionId')
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
                v-if="tour"
                :label="$t('Tour')" >
                <router-link :to="{name: 'tours.view', params: {tourId: tour.id}}"
                    :key="tour.id"
                    :style="'margin-left: 0px'"
                    exact>
                        {{ tour.name }}
                </router-link>
            </v-form-preview>

            <v-form-preview :label="$t('Bookeo identifier')">
                <div v-for="identifier in model.bookeo_id" :key="identifier">
                    {{ identifier }}
                </div>
            </v-form-preview>

            <v-form-preview
                :label="$t('Active')"
                :value="model.is_active"
            />
        </template>

        <template #footer>
            <button
                v-if="$can('tour-options.delete')"
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
                model: null,
                tour: null
            }
        },
        beforeRouteEnter: (to, from, next) => next(
            vm => vm.lock(
                vm.view()
            )
        ),
        beforeRouteUpdate(to, from, next) {
            if (to.params.tourOptionId !== from.params.tourOptionId) {
                this.lock(
                    this.view(to.params.tourOptionId)
                );
            }

            next();
        },
        computed: {
            backRoute() {
                return {
                    name: 'tour-options.list'
                };
            }
        },

        methods: {
            ...mapActions('tour-options', {
                async view(dispatch, tourOptionId = null) {
                    const { view } = await dispatch('view', {
                        tourId: this.$routeParam('tourId'),
                        tourOptionId: tourOptionId || this.$routeParam('tourOptionId')
                    });

                    this.$data.model = view;

                    if (!Array.isArray(this.$data.model.bookeo_id)) {
                        this.$data.model.bookeo_id = [this.$data.model.bookeo_id]
                    }

                    this.viewTour()
                },
                async destroy(dispatch) {
                    this.$vConfirmDelete(
                        this.$t('Delete tour option?'),
                    ).then(() => {
                        dispatch('destroy', {
                            tourId: this.$routeParam('tourId'),
                            tourOptionId: this.$data.model.id
                        });

                        this.$ee.emit('tour-options@list.fetch');

                        this.$router.push(this.backRoute);
                    })
                    .catch( error => {
                        console.log('Deleting canceled')
                    });
                }
            }),

            ...mapActions('tours', {
                async viewTour(dispatch, tourId = null) {
                    const { view } = await dispatch('view', {
                        tourId: tourId || this.$routeParam('tourId')
                    });

                    this.$data.tour = view;
                }
            })
        }
    }
</script>