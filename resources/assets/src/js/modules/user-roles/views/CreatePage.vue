<template>
    <v-sidebar-layout :locked="locked">
        <template #header>
            <v-sidebar-layout-header
                :title="$t('Нова роль користувача')"
                :back-route="backRoute"
            />
        </template>

        <template #body>
            <v-form
                ref="form"
                :data="formData"
                #default="{ form, submit }"
                name="user-role"
                @submit="store"
            >
                <model-form
                    :disabled="psCreateStore"
                    :form="form"
                    :permissions="permissions"
                    @submit="submit"
                >
                    <el-button
                        type="success"
                        native-type="submit"
                    >{{ $t('Створити') }}
                    </el-button>

                    <el-button
                        @click="onClickCancel"
                    >{{ $t('Скасувати') }}
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
                    name: 'user-roles.list'
                };
            },

            ...mapStatuses([
                {
                    name: 'create.store',
                    process: 'user-roles@create.store'
                }
            ])
        },
        beforeRouteEnter: (to, from, next) => next(
            vm => vm.lock(
                vm.create()
            )
        ),
        methods: {
            onClickCancel() {
                this.$refs.form.restore();

                this.$router.push(this.backRoute);
            },

            ...mapActions('user-roles', {
                async create(dispatch) {
                    const { form, permissions } = await dispatch('create');

                    this.$data.formData = form;
                    this.$data.permissions = permissions;
                },
                async store(dispatch, $form) {
                    const { form } = await dispatch('store', {
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
