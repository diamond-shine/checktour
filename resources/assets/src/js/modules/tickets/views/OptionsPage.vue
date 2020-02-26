<template>
    <v-sidebar-layout :locked="locked">
        <template #header>
            <v-sidebar-layout-header
                :title="model.name + ' ' + $t('options')"
                :back-route="backRoute"
            >
                <template #right>
                    <button
                        v-if="!isCreating && $can('tickets.edit')"
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
                    <div
                        v-if="editingValues.includes(item.id)"
                        class="p-20 u-border-bottom"
                    >
                        <model-form
                            :form-name="`edit-value${item.id}`"
                            :form-data="item"
                            @submit="update"
                            @cancel="toggleEditMode(item.id)"
                        />
                    </div>

                    <v-list-item
                        v-else
                        :key="item.id"
                        :data="item"
                        title-by="tour_option.name"
                        description-by="price"
                    >
                        <template #aside>
                            <div class="c-list__aside">
                                <div class="c-list__aside-item">
                                    <el-button
                                        v-if="$can('ticket-options.edit')"
                                        type="text"
                                        icon="fal fa-pencil"
                                        @click="toggleEditMode(item.id)"
                                    />
                                </div>

                                <div class="c-list__aside-item">
                                    <el-button
                                        v-if="$can('tickets.edit')"

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

    import ModelForm from '../components/OptionsPage/ModelForm';

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
            await vm.$filters('ticket-options').restoreFromUrl(to.name);

            vm.lock(
                vm.list()
            );
        }),
        computed: {
            backRoute() {
                return {
                    name: 'tickets.view',
                    params: {
                        ticketId: this.$routeParam('ticketId')
                    }
                }
            },
            ...mapStatuses([
                {
                    name: 'list.fetch',
                    process: 'ticket-options@list.fetch'
                },
                {
                    name: 'create.store',
                    process: 'ticket-options@create.store'
                },
                {
                    name: 'create.update',
                    process: 'ticket-options@create.update'
                },
                {
                    name: 'delete.destroy',
                    process: 'ticket-options@delete.destroy'
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

            toggleEditMode(ticketOptionId) {
                const index = this.editingValues.indexOf(ticketOptionId);

                if (index > -1) {
                    this.editingValues.splice(index, 1);
                } else {
                    this.editingValues.push(ticketOptionId);
                }
            },

            onChangePage(page) {
                this.$filters('ticket-options').data.page = page;

                this.list();
            },

            ...mapActions('tickets/options', {
                async list(dispatch) {
                    const {
                        items,
                        pagination,
                        ticket
                    } = await dispatch('list', {
                        ticketId: this.$routeParam('ticketId')
                    });

                    this.$data.model = ticket;
                    this.$data.items = items;
                    this.$data.pagination = pagination;
                },
                async store(dispatch, $form, storeAndContinue = false) {
                    await dispatch('store', {
                        ticketId: this.$routeParam('ticketId'),
                        form: $form
                    });

                    await this.list();

                    this.$ee.emit('tickets@list.fetch');

                    if (!storeAndContinue) {
                        this.toggleCreateMode();
                    }
                },

                async update(dispatch, $form) {
                    await dispatch('update', {
                        ticketId: this.$routeParam('ticketId'),
                        optionId: $form.data.id,
                        form: $form
                    });

                    await this.list();

                    this.toggleEditMode($form.data.id);
                },

                async create(dispatch) {
                    let responce = await dispatch('create', {
                        ticketId: this.$routeParam('ticketId')
                    });
                    this.formData = responce.form;
                },

                async destroy(dispatch, optionId) {
                    await this.$vConfirmDelete(
                        this.$t('Do you confirm deleting option?'),
                    );

                    await dispatch('destroy', {
                        ticketId: this.$routeParam('ticketId'),
                        optionId
                    });

                    this.list();

                    this.$ee.emit('tickets@list.fetch');
                }
            })
        }
    }
</script>