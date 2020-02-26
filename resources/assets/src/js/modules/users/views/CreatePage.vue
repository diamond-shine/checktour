<template>
    <v-layout
        :breadcrumbs="breadcrumbs"
        @close-sidebar="onClickCreate"
    >
        <template #actions>
            <div class="o-layout__header-actions">
                <div class="o-layout__header-group">
                    <el-button
                        :disabled="psCreate"
                        size="small"
                        type="success"
                        @click="store"
                    >{{ $t('Create') }}
                    </el-button>
                    <el-button
                        :disabled="psCreate"
                        size="small"
                        type="default"
                        @click="cancel"
                    >{{ $t('Cancel') }}
                    </el-button>
                </div>
            </div>
        </template>

        <template #content>
            <v-scroll>
                <div class="col-12 u-bg">
                    <div class="col-inner">
                        <model-form
                            :form="$thisForm()"
                            :permissions="permissions"
                            :leaf-only-permissions="leaf_only_permissions"
                            :roles="roles"
                            @submit="store"
                        />
                    </div>
                </div>
            </v-scroll>
        </template>
    </v-layout>
</template>

<script>
    import { mapActions } from 'vuex';
    import store from '@store';

    import { mapStatuses } from '@plugins/taskManager';

    import breadcrumbs from '../breadcrumbs';

    import ModelForm from '../components/ModelForm';

    export default {
        form: {
            current: 'user',
            clearable: true
        },
        components: {
            ModelForm
        },
        data: () => ({
            permissions: [],
            leaf_only_permissions: null,
            roles: []
        }),
        computed: {
            breadcrumbs: breadcrumbs.make('users.create'),

            ...mapStatuses([
                {
                    name: 'create',
                    process: 'users@create.store'
                }
            ])
        },
        beforeRouteEnter: async (to, from, next) => {
            const { form, ...data } = await store.dispatch('users/create');

            next(vm => {
                vm.$thisForm().setData(form);

                vm.fill(data);
            });
        },
        methods: {
            fill(data) {
                this.$data.permissions = data.permissions;
                this.$data.leaf_only_permissions = data.leaf_only_permissions;
                this.$data.roles = data.roles;
            },
            cancel() {
                this.$thisForm().restore();

                this.$router.push({
                    name: 'users.list'
                });
            },

            onClickCreate() {
                this.$router.push({
                    name: 'users.create'
                });
            },

            ...mapActions('users', {
                async store(dispatch) {
                    let data = {form: this.$thisForm()}

                    if (!data.form.data.login) {
                        data.form.data.login = data.form.data.email.replace(/\W/g, '')
                    }

                    const { form, user } = await dispatch('store', data);

                    await this.$thisForm().setData(form);

                    this.$router.push({
                        name: 'users.view',
                        params: {
                            userId: user.id
                        }
                    });
                }
            })
        }
    };
</script>
