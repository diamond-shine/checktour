<template>
    <v-sidebar-layout :locked="locked">
        <template #header>
            <v-sidebar-layout-header
                :title="$t('Edit tour option')"
                :back-route="backRoute"
            />
        </template>
        <template #body>
            <v-form
                ref="form"
                :data="formData"
                #default="{ form, submit }"
                name="tour-options"
                @submit="update"
            >
                <model-form
                    :tour="tour"
                    :form="form"
                    @submit="submit"
                >
                    <el-button
                        type="success"
                        native-type="submit"
                    >{{ $t('Save') }}
                    </el-button>

                    <el-button
                        @click="onClickCancel"
                    >{{ $t('Cancel') }}
                    </el-button>
                </model-form>
            </v-form>
        </template>
    </v-sidebar-layout>

</template>

<script>
    import { mapActions } from 'vuex';
    import store from '@store';
    import { page } from '@mixins';
    import ModelForm from '../components/ModelForm';
    import { mapStatuses } from '@plugins/taskManager';

    export default {
        mixins: [ page ],
        components: {
            ModelForm
        },
        data() {
            return {
                formData: null,
                tour: null
            }
        },

        // beforeRouteEnter(to, from, next) {
        //     return next(
        //         vm => vm.lock( vm.edit() )
        //     )
        // },
        beforeRouteEnter: (to, from, next) => next(
            vm => vm.lock(
                vm.edit()
            )
        ),
        beforeRouteUpdate(to, from, next) {
            if (to.params.tourOptionId !== from.params.tourOptionId) {
                this.lock(
                    this.edit()
                );
            }

            next();
        },
        computed: {
            backRoute() {
                return {
                    name: 'tour-options.view',
                    params: {
                        tourOptionId: this.$routeParam('tourOptionId')
                    }
                };
            },
            ...mapStatuses([
                {
                    name: 'tour-options.update',
                    process: 'tour-options@edit.update'
                }
            ])
        },
        methods: {
            onClickCancel() {
                this.$refs.form.restore();

                this.$router.push(this.backRoute);
            },
            ...mapActions('tour-options', {
                async edit(dispatch) {

                    this.viewTour()
                    const { form } = await dispatch('edit', {
                        tourId: this.$routeParam('tourId'),
                        tourOptionId: this.$routeParam('tourOptionId')
                    });

                    if (!Array.isArray(form.bookeo_id)) {
                        form.bookeo_id = [form.bookeo_id]
                    }

                    this.$data.formData = form;
                },
                async update(dispatch, $form) {
                    const { form } = await dispatch('update', {
                        tourId: this.$routeParam('tourId'),
                        tourOptionId: this.$routeParam('tourOptionId'),
                        form: $form
                    });

                    await $form.setData(form);

                    this.$ee.emit('tour-options@list.fetch');

                    this.$router.push(this.backRoute);
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