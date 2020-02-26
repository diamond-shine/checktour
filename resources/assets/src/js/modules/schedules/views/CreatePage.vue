<template>
    <v-sidebar-layout :locked="locked">
        <template #header>
            <v-sidebar-layout-header
                :title="$t('Add schedule')"
                :back-route="backRoute"
            />
        </template>

        <template #body>
            <v-form
                ref="form"
                :data="formData"
                #default="{ form, submit }"
                name="schedules"
                @submit="store"
            >
                <model-form
                    :form="form"
                    @submit="submit">
                    <el-button
                        type="success"
                        native-type="submit"
                    >{{ $t('Create') }}
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
    import breadcrumbs from '../breadcrumbs';
    import store from '@store';
    import { page } from '@mixins';
    import ModelForm from '../components/ModelForm'

    export default {
        components: {
            ModelForm
        },
        mixins: [ page ],
        data() {
            return {
                formData: null
            }
        },
        beforeRouteEnter: (to, from, next) => next(
            vm => vm.lock(
                vm.create()
            )
        ),
        computed: {
            backRoute() {
                return {
                    name: 'schedules.list'
                };
            }
        },
        methods: {
            onClickCancel() {
                this.$refs.form.restore();

                this.$router.push(this.backRoute);
            },
            ...mapActions('schedules', {
                async create(dispatch) {
                    // const { form } = await dispatch('create');

                    // this.$data.formData = form;
                },

                async store(dispatch, $form) {
                    const { form } = await dispatch('store', {
                        form: $form
                    });

                    await $form.setData(form);

                    // this.$ee.emit('schedules@list.fetch');

                    this.$router.push(this.backRoute);
                }
            })
        }
    }
</script>