<template>
    <v-layout :breadcrumbs="breadcrumbs">
        <template #actions>
            <el-button
                :loading="psEdit"
                size="small"
                type="success"
                @click="update"
            >{{ $t('Save') }}
            </el-button>
            <el-button
                :loading="psEdit"
                size="small"
                type="default"
                @click="cancel"
            >{{ $t('Cancel') }}
            </el-button>
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
                            @submit="update"
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

    import breadcrumbs from '../breadcrumbs';
    import { mapStatuses } from '@plugins/taskManager';

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
            user: null,
            permissions: [],
            leaf_only_permissions: null,
            roles: []
        }),
        computed: {
            breadcrumbs() {
                if (!this.$data.user) {
                    return;
                }

                if (!this.$can('users.view')) {
                    return;
                }

                return breadcrumbs.generate('users.edit', {
                    user: this.$data.user
                });
            },

            ...mapStatuses([
                {
                    name: 'edit',
                    process: 'users@edit',
                    deep: true
                }
            ])
        },
        beforeRouteEnter: async (to, from, next) => {
            const {
                form: { telephone, ...form },

                ...data
            } = await store.dispatch('users/edit', {
                userId: to.params.userId
            });

            next(vm => {
                vm.$thisForm().setData({
                    ...form,

                    telephone: {
                        number: telephone?.international
                    }
                });

                vm.fill(data);
            });
        },
        methods: {
            fill(data) {
                this.$data.user = data.user;
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

            ...mapActions('users', {
                async update(dispatch) {
                    const {
                        form: { telephone, ...data }
                    } = await dispatch('update', {
                        userId: this.$data.user.id,
                        form: this.$thisForm()
                    });

                    this.$thisForm().setData({
                        ...data,

                        telephone: {
                            number: telephone?.international
                        }
                    });
                }
            })
        }
    };
</script>
