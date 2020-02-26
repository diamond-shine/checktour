<template>
    <v-sidebar-layout :loading="psView">
        <template #header>
            <v-sidebar-layout-header
                :title="$t('User profile')"
                :back-route="{ name: 'users.list' }"
            >
                <template #right>
                    <router-link
                        v-if="$can('users.edit')"
                        :to="{
                            name: 'users.edit',
                            params: {
                                userId: $routeParam('userId')
                            }
                        }"
                        :title="$t('Edit')"
                        :aria-label="$t('Edit')"
                        :disabled="psView"
                        class="btn"
                    ><i class="icon fal fa-edit" /></router-link>
                </template>
            </v-sidebar-layout-header>
        </template>

        <template
            v-if="view"
            #body
        >
            <v-form-preview
                :label="$t('First name, last name')"
                :value="view.full_name"
            />

            <v-form-preview
                :label="$t('Email')"
            >{{ view.email }}<a :href="`mailto:${view.email}`"><i class="c-icon-letter" /></a>
            </v-form-preview>

            <!-- <v-form-preview
                :label="$t('Телефон')"
            >
                <template v-if="view.telephone">
                    {{ view.telephone.international }}<a :href="view.telephone.rfc"><i class="c-icon-iphone" /></a>
                </template>
                <div
                    v-else
                    class="c-form-preview__value text-muted"
                ><em>({{ $t('Value not set') }})</em>
                </div>
            </v-form-preview> -->

<!--             <v-form-preview :label="$t('Photo')">
                <v-form-file
                    v-if="view.image"
                    :value="view.image"
                    size="md"
                    disabled
                />
                <div
                    v-else
                    class="c-form-preview__value text-muted"
                ><em>({{ $t('Not set') }})</em>
                </div>
            </v-form-preview> -->

            <v-form-preview
                :label="$t('Roles')"
                :value="view.roles || null"
                :no-value="$t('Not set')"
            />
        </template>

        <template #footer>
            <button
                v-if="$can('users.delete')"
                type="button"
                class="c-delete-btn"
                @click="destroy"
            >
                <i
                    class="icon fal fa-trash-alt"
                    aria-hidden="true"
                />
                <span class="title">{{ $t('Delete user') }}</span>
            </button>
        </template>

    </v-sidebar-layout>
</template>

<script>
    import { mapStatuses } from '@plugins/taskManager';
    import { mapActions } from 'vuex';
    import store from '@store';

    export default {
        data: () => ({
            user: null,
            view: null
        }),
        computed: {
            ...mapStatuses([
                {
                    name: 'view',
                    process: 'users@view',
                    deep: true
                }
            ])
        },
        beforeRouteEnter: async (to, from, next) => {
            const data = await store.dispatch('users/view', {
                userId: to.params.userId
            });

            next(
                vm => vm.fill(data)
            );
        },
        methods: {
            fill(data) {
                this.$data.user = data.user;
                this.$data.view = data.view;
            },

            ...mapActions('users', {
                async destroy(dispatch) {
                    await this.$vConfirmDelete(
                        this.$t('Delete user?'),
                    );

                    await dispatch('destroy', {
                        userId: this.$routeParam('userId')
                    });

                    this.$ee.emit('users@list.fetch');

                    this.$router.push({
                        name: 'users.list'
                    });
                }
            })
        }
    };
</script>
