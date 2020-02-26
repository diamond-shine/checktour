<template>
    <v-sidebar-layout :locked="locked">
        <template #header>
            <v-sidebar-layout-header
                :title="$t('Edit tour')"
                :back-route="backRoute"
            />
        </template>
        <template #body>
            <v-form
                ref="form"
                :data="formData"
                #default="{ form, submit }"
                name="tours"
                @submit="update"
            >
                <model-form
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
                formData: null
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
            if (to.params.tourId !== from.params.tourId) {
                this.lock(
                    this.edit()
                );
            }

            next();
        },
        computed: {
            backRoute() {
                return {
                    name: 'tours.view',
                    params: {
                        tourId: this.$routeParam('tourId')
                    }
                };
            },
            ...mapStatuses([
                {
                    name: 'tours.update',
                    process: 'tours@edit.update'
                }
            ])
        },
        methods: {
            onClickCancel() {
                this.$refs.form.restore();

                this.$router.push(this.backRoute);
            },
            ...mapActions('tours', {
                async edit(dispatch) {

                    const { form } = await dispatch('edit', {
                        tourId: this.$routeParam('tourId')
                    });

                    this.$data.formData = form;
                },
                async update(dispatch, $form) {
                    const { form } = await dispatch('update', {
                        tourId: this.$routeParam('tourId'),
                        form: $form
                    });

                    await $form.setData(form);

                    this.$ee.emit('tours@list.fetch');

                    this.$router.push(this.backRoute);
                }
            })
        }

    }
</script>