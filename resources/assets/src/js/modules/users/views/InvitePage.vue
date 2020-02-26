<template>
    <v-sidebar-layout :loading="psSend">
        <template #header>
            <v-sidebar-layout-header
                :title="$t('Invite user')"
                :back-route="{ name: 'users.list' }"
            />
        </template>

        <template #body>
            <el-form label-position="top">
                <el-form-item
                    :label="$t('E-mail')"
                    :error="$thisForm().formatErrors('email')"
                >
                    <el-input
                        v-model="$thisForm('data').email"
                        type="email"
                    />
                </el-form-item>

                <el-form-item
                    :label="$t('Login')"
                    :error="$thisForm().formatErrors('login')"
                >
                    <el-input v-model="$thisForm('data').login" />
                </el-form-item>

                <el-form-item
                    v-if="$can('users.invite.as-admin')"
                    :label="$t('Provide admin rights')"
                    :error="$thisForm().formatErrors('as_admin')"
                    class="el-form-item--inline"
                >
                    <el-switch v-model="$thisForm('data').as_admin" />
                </el-form-item>

                <v-divider />

                <el-form-item
                    :label="$t('Roles')"
                    :error="$thisForm().formatErrors('roles')"
                >
                    <div class="c-user-roles">
                        <template v-if="roles.length">
                            <el-checkbox-group v-model="$thisForm('data').roles">
                                <el-checkbox
                                    v-for="role in roles"
                                    :key="role.id"
                                    :label="role.id"
                                >{{ role.title }}
                                </el-checkbox>
                            </el-checkbox-group>
                        </template>

                        <v-empty
                            v-else
                            :title="$t('No available roles')"
                            icon="icon c-icon-team"
                            size="md"
                            static-height
                        />
                    </div>
                </el-form-item>

                <v-divider />

                <el-form-item
                    :label="$t('Permissions')"
                    :error="$thisForm().formatErrors('permissions')"
                >
                    <el-tree
                        v-if="permissions"
                        ref="permissions"
                        :data="permissions"
                        :props="{
                            label: 'title',
                            children: 'permissions'
                        }"
                        :default-checked-keys="$thisForm('data').permissions"
                        node-key="name"
                        show-checkbox
                        @check="syncPermissions"
                    />

                    <v-empty
                        v-else
                        :title="$t('No available permissions')"
                        icon="fal fa-gavel"
                        size="md"
                        static-height
                    />
                </el-form-item>

                <v-divider />

                <el-button
                    size="small"
                    type="success"
                    @click="invite"
                >{{ $t('Send invitation') }}
                </el-button>

                <el-button
                    size="small"
                    @click="cancel"
                >{{ $t('Cancel') }}
                </el-button>
            </el-form>
        </template>
    </v-sidebar-layout>
</template>

<script>
    import { mapStatuses } from '@plugins/taskManager';
    import { mapActions, mapState } from 'vuex';
    import store from '@store';

    export default {
        form: {
            current: 'user-invite',
            clearable: true
        },
        data: () => ({
            roles: [],
            permissions: []
        }),
        computed: {
            ...mapStatuses([
                {
                    name: 'send',
                    process: 'users@invite.send'
                }
            ])
        },
        beforeRouteEnter: async (to, from, next) => {
            const data = await store.dispatch('users/invite/fetch');

            next(vm => {
                vm.fill(data);

                vm.$thisForm().setData({
                    roles: [],
                    permissions: []
                });
            });
        },
        methods: {
            fill(data) {
                this.$data.roles = data.roles;
                this.$data.permissions = data.permissions;
            },

            cancel() {
                this.$router.push({
                    name: 'users.list'
                });
            },

            syncPermissions() {
                this.$thisForm().fill({
                    permissions: this.$refs.permissions.getCheckedKeys(true)
                });
            },

            ...mapActions('users/invite', {
                async invite(dispatch) {
                    await dispatch('send', {
                        form: this.$thisForm()
                    });

                    this.$router.push({
                        name: 'users.list'
                    });
                }
            })
        }
    };
</script>
