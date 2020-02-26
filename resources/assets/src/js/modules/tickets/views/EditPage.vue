<template>
    <v-sidebar-layout :locked="locked">
        <template #header>
            <v-sidebar-layout-header
                :title="$t('Edit ticket')"
                :back-route="backRoute"
            />
        </template>
        <template #body>
            <v-form
                ref="form"
                :data="formData"
                #default="{ form, submit }"
                name="tickets"
                @submit="update"
            >
                <model-form
                    :form="form"
                    :tours="tours"
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
                tours: []
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
            if (to.params.ticketId !== from.params.ticketId) {
                this.lock(
                    this.edit()
                );
            }

            next();
        },
        computed: {
            backRoute() {
                return {
                    name: 'tickets.view',
                    params: {
                        ticketId: this.$routeParam('ticketId')
                    }
                };
            },
            ...mapStatuses([
                {
                    name: 'tickets.update',
                    process: 'tickets@edit.update'
                }
            ])
        },
        methods: {
            onClickCancel() {
                this.$refs.form.restore();

                this.$router.push(this.backRoute);
            },
            ...mapActions('tickets', {
                async edit(dispatch) {
                    let responce = await dispatch('edit', {
                        ticketId: this.$routeParam('ticketId')
                    });

                    this.tours  = responce.tours;

                    this.$data.formData = responce.form;
                },
                async update(dispatch, $form) {
                    const { form } = await dispatch('update', {
                        ticketId: this.$routeParam('ticketId'),
                        form: $form
                    });

                    await $form.setData(form);

                    this.$ee.emit('tickets@list.fetch');

                    this.$router.push(this.backRoute);
                }
            })
        }

    }
</script>