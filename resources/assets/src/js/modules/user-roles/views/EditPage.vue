<template>
    <v-sidebar-layout :locked="locked">
        <template #header>
            <v-sidebar-layout-header
                :title="$t('Редагування ролі користувача')"
                :back-route="backRoute"
            />
        </template>

        <template #body>
            <v-form
                ref="form"
                :data="formData"
                #default="{ form, submit }"
                name="user-role"
                @submit="update"
            >
                <model-form
                    :disabled="psEditUpdate"
                    :form="form"
                    :permissions="permissions"
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
    import { mapStatuses } from '@plugins/taskManager';
    import { mapActions } from 'vuex';
    import store from '@store';
    import { page } from '@mixins';

    import ModelForm from '../components/ModelForm';

    export default {
        components: {
            ModelForm
        },
        mixins: [ page ],
        data: () => ({
            formData: null,
            permissions: []
        }),
        computed: {
            backRoute() {
                return {
                    name: 'user-roles.view',
                    params: {
                        userRoleId: this.$routeParam('userRoleId')
                    }
                };
            },

            ...mapStatuses([
                {
                    name: 'edit.update',
                    process: 'user-roles@edit.update'
                }
            ])
        },
        beforeRouteEnter: (to, from, next) => next(
            vm => vm.lock(
                vm.edit()
            )
        ),
        beforeRouteUpdate(to, from, next) {
            if (to.params.userRoleId !== from.params.userRoleId) {
                this.lock(
                    this.edit()
                );
            }

            next();
        },
        methods: {
            onClickCancel() {
                this.$refs.form.restore();

                this.$router.push(this.backRoute);
            },

            ...mapActions('user-roles', {
                async edit(dispatch) {
                    const { form, permissions } = await dispatch('edit', {
                        userRoleId: this.$routeParam('userRoleId')
                    });

                    this.$data.formData = form;
                    this.$data.permissions = permissions;
                },
                async update(dispatch, $form) {
                    const { form } = await dispatch('update', {
                        userRoleId: this.$routeParam('userRoleId'),
                        form: $form
                    });

                    await $form.setData(form);

                    this.$ee.emit('user-roles@list.fetch');

                    this.$router.push(this.backRoute);
                }
            })
        }
    };
</script>
