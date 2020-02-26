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
            roles: [],
            tourOptions: [],
            grouped: []
        }),
        computed: {
            breadcrumbs() {

                return breadcrumbs.generate('settings.edit');
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
            const { items } = await store.dispatch('settings/optionsList');

            next(vm => {

                let grouped = vm.groupOptions(items);
                vm.$thisForm().setData({
                    options: grouped
                });

                vm.fill({grouped: grouped, tourOptions: items});
            });
        },
        methods: {
            groupOptions(options) {
                let grouped = [];

                options.forEach((item) => {
                    let index = grouped.findIndex(e => e.name == item.name);

                    if (index < 0) {
                        grouped.push({name: item.name, is_active: item.is_active})
                    } else {
                        grouped[index].is_active = grouped[index].is_active || item.is_active;
                    }
                })
                return grouped;
            },
            fill(data) {
                this.tourOptions = data.tourOptions;
                this.grouped = data.grouped;
            },

            cancel() {
                this.$thisForm().restore();

                this.$router.push({
                    name: 'users.list'
                });
            },

            ...mapActions('settings', {
                async update(dispatch) {
                    const { form } = await dispatch('optionsUpdate', {
                        form: this.$thisForm()
                    });

                    let grouped = this.groupOptions(form);

                    this.fill({grouped: grouped, tourOptions: form});
                }
            })
        }
    };
</script>
