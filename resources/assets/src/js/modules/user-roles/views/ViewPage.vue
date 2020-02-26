<template>
    <v-sidebar-layout :locked="locked">
        <template #header>
            <v-sidebar-layout-header
                :title="$t('Картка ролі користувача')"
                :back-route="backRoute"
            >
                <template #right>
                    <router-link
                        v-if="$can('user-roles.edit')"
                        :to="{
                            name: 'user-roles.edit',
                            params: {
                                userRoleId: $routeParam('userRoleId')
                            }
                        }"
                        :title="$t('Редагувати')"
                        :aria-label="$t('Редагувати')"
                        class="btn"
                    ><i class="icon fal fa-edit" /></router-link>
                </template>
            </v-sidebar-layout-header>
        </template>

        <template #body>
            <v-form-preview
                :label="$t('Назва')"
                :value="model.title"
            />

            <v-form-preview
                :label="$t('Ключ')"
                :value="model.name"
            />
        </template>

        <template #footer>
            <button
                v-if="$can('user-roles.delete')"
                type="button"
                class="c-delete-btn"
                @click="destroy"
            >
                <i
                    class="icon fal fa-trash-alt"
                    aria-hidden="true"
                />
                <span class="title">{{ $t('Видалити') }}</span>
            </button>
        </template>

    </v-sidebar-layout>
</template>

<script>
    import { mapStatuses } from '@plugins/taskManager';
    import { mapActions } from 'vuex';
    import store from '@store';
    import { page } from '@mixins';

    export default {
        mixins: [ page ],
        data: () => ({
            model: null
        }),
        computed: {
            backRoute() {
                return {
                    name: 'user-roles.list'
                };
            },

            ...mapStatuses([
                {
                    name: 'view.fetch',
                    process: 'user-roles@view.fetch'
                }
            ])
        },
        beforeRouteEnter: (to, from, next) => next(
            vm => vm.lock(
                vm.view()
            )
        ),
        beforeRouteUpdate(to, from, next) {
            if (to.params.userRoleId !== from.params.userRoleId) {
                this.lock(
                    this.view()
                );
            }

            next();
        },
        methods: {
            ...mapActions('user-roles', {
                async view(dispatch) {
                    const { view } = await dispatch('view', {
                        userRoleId: this.$routeParam('userRoleId')
                    });

                    this.$data.model = view;
                },
                async destroy(dispatch) {
                    await this.$vConfirmDelete(
                        this.$t('Видалити роль користувача?'),
                    );

                    await dispatch('destroy', {
                        userRoleId: this.$data.model.id
                    });

                    this.$ee.emit('user-roles@list.fetch');

                    this.$router.push(this.backRoute);
                }
            })
        }
    };
</script>
