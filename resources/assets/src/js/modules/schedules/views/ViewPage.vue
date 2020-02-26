<template>
    <v-sidebar-layout :locked="locked">
        <template #header>
            <v-sidebar-layout-header
                :title="$t('Session detail page')"
                :back-route="backRoute"
            >
                <template #right>
                    <button
                        v-if="!isCreating && $can('schedules.create')"
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
            <v-form-preview
                :label="$t('User')"
                :value="model.first_name + ' ' + model.last_name"
            />

            <div
                v-if="isCreating"
                class="mb-20"
            >
                <v-form
                    ref="form"
                    :data="formData"

                    #default="{ form, submit }"
                    name="schedules"
                    @submit="store"
                >
                    <model-form
                        :users="[model]"
                        :form="form"
                        @submit="submit">
                        <el-button
                            type="success"
                            native-type="submit"
                        >{{ $t('Create') }}
                        </el-button>

                        <el-button
                            @click="toggleCreateMode"
                        >{{ $t('Cancel') }}
                        </el-button>
                    </model-form>
                </v-form>
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
                        :title-by="assignedAt"
                        description-by="tour.name"
                    >
                        <template #aside>
                            <div class="c-list__aside">
                                <div class="c-list__aside-item">
                                    <el-button
                                        v-if="$can('schedules.delete')"

                                        type="text"
                                        icon="fal fa-trash-alt"
                                        @click="destroy(item)"
                                    />
                                </div>
                            </div>
                        </template>
                    </v-list-item>

                </template>
            </v-content-list>

        </template>

        <template #footer>
            <!-- <button
                v-if="$can('schedules.delete')"
                type="button"
                class="c-delete-btn"
                @click="destroy"
            >
                <i
                    class="icon fal fa-trash-alt"
                    aria-hidden="true"
                />
                <span class="title">{{ $t('Delete') }}</span>
            </button> -->
        </template>
    </v-sidebar-layout>
</template>
<script>

    import { mapStatuses } from '@plugins/taskManager';
    import { mapActions } from 'vuex';
    import store from '@store';
    import { page } from '@mixins';
    import ModelForm from '../components/ModelForm'


    export default {
        mixins: [ page ],
        components: {
            ModelForm
        },
        data() {
            return {
                isCreating: false,
                model: null,
                items: [],
                pagination: null,
                formData: null
            }
        },
        beforeRouteEnter: (to, from, next) => next(
            vm => vm.lock(
                vm.view()
            )
        ),
        beforeRouteUpdate(to, from, next) {
            if (to.params.scheduleId !== from.params.scheduleId) {
                this.lock(
                    this.view(to.params.scheduleId)
                );
            }

            next();
        },
        computed: {
            backRoute() {
                return {
                    name: 'schedules.list'
                };
            }
        },

        methods: {
            toggleCreateMode() {
                this.$data.isCreating = !this.$data.isCreating;
                // if (this.$data.isCreating) {
                //     this.create()
                // }
            },
            onChangePage(page) {
                this.$filters('schedules').data.page = page;

                this.view();
            },
            assignedAt(item) {
                return item.assigned_at + ' ' + item.excursion.time;
            },
            ...mapActions('schedules', {
                async view(dispatch, scheduleId = null) {
                    const { view, items, pagination } = await dispatch('view', {
                        scheduleId: scheduleId || this.$routeParam('scheduleId')
                    });

                    this.$data.model = view;
                    this.$data.items = items;
                    this.$data.pagination = pagination;
                    this.$data.formData = {
                        user_id: view.id,
                        tour_id: parseInt(this.$routeParam('tourId'))
                    };
                },
                async destroy(dispatch, schedule) {
                    this.$vConfirmDelete(
                        this.$t('Do you confirm deleting session?'),
                    ).then(() => {
                        dispatch('destroy', {
                            scheduleId: schedule.id
                        });

                        this.view();
                        this.$ee.emit('schedules@list.fetch');
                        this.$ee.emit('schedules@view.fetch');
                    })
                    .catch( error => {
                        console.log('Deleting canceled')
                    });
                },
                async store(dispatch, $form, storeAndContinue = false) {
                    const { form } = await dispatch('store', {
                        form: $form
                    });

                    await $form.setData(form);

                    if (storeAndContinue) {
                        $form.setData({});
                    } else {
                        this.toggleCreateMode();
                    }

                    await this.view();

                    this.$ee.emit('schedules@list.fetch');
                    this.$ee.emit('schedules@view.fetch');
                }
            })
        }
    }
</script>