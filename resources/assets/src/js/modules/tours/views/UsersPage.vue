<template>
    <v-sidebar-layout :locked="locked">
        <template #header>
            <v-sidebar-layout-header
                :title="$t('Tour users')"
                :back-route="backRoute"
            >
                <template #right>
                    <button
                        v-if="!isCreating && $can('tours-users.edit')"
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


                    <v-list-item
                        :key="item.id"
                        :data="item"
                        :title-by="getUserName"
                        description-by="email"
                    >
                        <template #aside>
                            <div class="c-list__aside">

                                <div class="c-list__aside-item">
                                    <el-button
                                        v-if="$can('tours-users.edit')"

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

    import ModelForm from '../components/UsersPage/ModelForm';

    export default {
        components: {
            ModelForm
        },
        mixins: [ page ],
        data() {
            return {
                isCreating: false,
                editingValues: [],
                model: null,
                pagination: null,
                items: [],
                formData: null,
            }
        },
        beforeRouteEnter: (to, from, next) => next(async vm => {
            await vm.$filters('tour-users').restoreFromUrl(to.name);

            vm.lock(
                vm.list()
            );
        }),
        computed: {
            backRoute() {
                return {
                    name: 'tours.view',
                    params: {
                        tourId: this.$routeParam('tourId')
                    }
                }
            },
            ...mapStatuses([
                {
                    name: 'list.fetch',
                    process: 'tour-users@list.fetch'
                },
                {
                    name: 'create.store',
                    process: 'tour-users@create.store'
                },
                {
                    name: 'delete.destroy',
                    process: 'tour-users@delete.destroy'
                }
            ])
        },
        methods: {
            getUserName(user) {
                return `${user.first_name} ${user.last_name}`
            },
            toggleCreateMode() {
                this.$data.isCreating = !this.$data.isCreating;
                if (this.$data.isCreating) {
                    this.create()
                }
            },

            onChangePage(page) {
                this.$filters('tour-users').data.page = page;

                this.list();
            },

            ...mapActions('tours/users', {
                async list(dispatch) {
                    const {
                        items,
                        pagination,
                        tour
                    } = await dispatch('list', {
                        tourId: this.$routeParam('tourId')
                    });

                    this.$data.model = tour;
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

                async create(dispatch) {
                    this.formData = {tour_id: this.$routeParam('tourid')}
                },

                async destroy(dispatch, userId) {
                    await this.$vConfirmDelete(
                        this.$t('Do you confirm deleting user?'),
                    );

                    await dispatch('destroy', {
                        tourId: this.$routeParam('tourId'),
                        userId
                    });

                    this.list();

                    this.$ee.emit('tickets@list.fetch');
                }
            })
        }
    }
</script>