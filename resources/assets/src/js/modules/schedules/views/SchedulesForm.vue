<template>
    <v-sidebar-layout :locked="locked">
        <template #header>
            <v-sidebar-layout-header
                :title="$t('Schedule')"
                :back-route="backRoute"
            >
                <template #right>
                    <button
                        v-if="!isCreating && $can('tours.excursions.create')"
                        :aria-label="$t('Add')"
                        type="button"
                        class="btn"
                        @click="toggleCreateMode"
                    >
                        <i class="c-icon-add2" />
                    </button>
                </template>
            </v-sidebar-layout-header>
        </template>
        <template #body>
            <div
                v-if="isCreating && formData"
                class="mb-20"
            >
                <model-form
                    :is-creating="true"
                    :form-data="formData"
                    :tour="tour"
                    form-name="new-value"
                    @submit="store"
                    @cancel="toggleCreateMode"
                />
            </div>

            <v-content-list

                :items="items"
                :pagination="pagination"
                unbox
                @change-page="onChangePage"
            >
                <template #default="{ item }">
                    <div
                        v-if="editingValues.includes(item.id)"
                        class="p-20 u-border-bottom"
                    >
                        <model-form
                            :form-name="`edit-value${item.id}`"
                            :form-data="item"
                            :tour="tour"
                            @submit="update"
                            @cancel="toggleEditMode(item.id)"
                        />
                    </div>

                    <v-list-item
                        v-else
                        :key="item.id"
                        :data="item"
                        title-by="day_title"
                        description-by="time"
                    >
                        <template #aside>
                            <div class="c-list__aside">
                                <div class="c-list__aside-item">
                                    <el-button
                                        v-if="$can('tours.excursions.edit')"
                                        type="text"
                                        icon="fal fa-pencil"
                                        @click="toggleEditMode(item.id)"
                                    />
                                </div>

                                <div class="c-list__aside-item">
                                    <el-button
                                        v-if="$can('tours.excursions.delete')"

                                        type="text"
                                        icon="fal fa-trash-alt"
                                        @click="destroy(item.id)"
                                    />
                                </div>
                            </div>
                        </template>
                    </v-list-item>

                </template>
            </v-content-list>
        </template>
    </v-sidebar-layout>
</template>

<script>
    import { mapStatuses } from '@plugins/taskManager';
    import { mapActions } from 'vuex';
    import store from '@store';
    import { page } from '@mixins';

    import ModelForm from '../components/ExcursionsPage/ModelForm';

    export default {
        components: {
            ModelForm
        },
        mixins: [ page ],
        data() {
            return {
                isCreating: false,
                editingValues: [],
                pagination: null,
                tour: null,
                items: [],
                formData: null,
            }
        },
        beforeRouteEnter: (to, from, next) => next(async vm => {
            await vm.$filters('excursions').restoreFromUrl(to.name);

            vm.lock(
                vm.list()
            );
        }),
        computed: {
            backRoute() {
                return {
                    name: 'tours.view',
                    params: {
                        ticketId: this.$routeParam('tourId')
                    }
                }
            },
            ...mapStatuses([
                {
                    name: 'list.fetch',
                    process: 'excursions@list.fetch'
                },
                {
                    name: 'create.store',
                    process: 'excursions@create.store'
                },
                {
                    name: 'create.update',
                    process: 'excursions@create.update'
                },
                {
                    name: 'delete.destroy',
                    process: 'excursions@delete.destroy'
                }
            ])
        },
        methods: {
            toggleCreateMode() {
                this.$data.isCreating = !this.$data.isCreating;
                if (this.$data.isCreating) {
                    this.create()
                }
            },

            toggleEditMode(excursionId) {
                const index = this.editingValues.indexOf(excursionId);

                if (index > -1) {
                    this.editingValues.splice(index, 1);
                } else {
                    this.editingValues.push(excursionId);
                }
            },

            onChangePage(page) {
                this.$filters('excursions').data.page = page;

                this.list();
            },

            ...mapActions('tours/excursions', {
                async list(dispatch) {
                    const {
                        items,
                        pagination,
                        tour
                    } = await dispatch('list', {
                        tourId: this.$routeParam('tourId')
                    });

                    this.$data.tour = tour;
                    this.$data.items = items;
                    this.$data.pagination = pagination;
                },
                async store(dispatch, $form, storeAndContinue = false) {
                    await dispatch('store', {
                        tourId: this.$routeParam('tourId'),
                        form: $form
                    });

                    await this.list();

                    this.$ee.emit('tours@list.fetch');

                    if (storeAndContinue) {
                        $form.setData({});
                    } else {
                        this.toggleCreateMode();
                    }
                },

                async update(dispatch, $form) {

                    await dispatch('update', {
                        tourId: this.$routeParam('tourId'),
                        excursionId: $form.data.id,
                        form: $form
                    });

                    await this.list();

                    this.toggleEditMode($form.data.id);
                },

                async create(dispatch) {
                    let responce = await dispatch('create', {
                        tourId: this.$routeParam('tourId')
                    });
                    this.formData = responce.form;
                },

                async destroy(dispatch, excursionId) {
                    await this.$vConfirmDelete(
                        this.$t('Do you confirm deleting excursion?'),
                    );

                    await dispatch('destroy', {
                        tourId: this.$routeParam('tourId'),
                        excursionId: excursionId
                    });

                    this.list();

                    this.$ee.emit('tours@list.fetch');
                }
            })
        }
    }
</script>