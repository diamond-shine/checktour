<template>
    <v-panel
        :title="$t('Personal information')"
        :description="$t('General information about user')"
        class="c-user-general"
        annotated
    >
        <v-panel>
            <div class="u-unwrap-bottom">
                <!-- <el-form-item>
                    <v-form-file
                        v-model="form.data.image"
                        :scope="$routeParam('userId')"
                        :placeholder-url="gravatar"
                        icon="fal fa-dizzy"
                    />
                </el-form-item> -->

                <el-form-item
                    :label="$t('First name')"
                    :error="form.formatErrors('first_name')"
                >
                    <el-input v-model="form.data.first_name" />
                </el-form-item>

                <el-form-item
                    :label="$t('Last name')"
                    :error="form.formatErrors('last_name')"
                >
                    <el-input v-model="form.data.last_name" />
                </el-form-item>

                <!-- <el-form-item
                    :label="$t('Login')"
                    :error="form.formatErrors('login')"
                >
                    <el-input
                        v-model="form.data.login"
                        :class="{ 'is-disabled': isEditPage }"
                        :readonly="isEditPage"
                    />
                </el-form-item> -->

                <el-form-item
                    :label="$t('E-mail')"
                    :error="form.formatErrors('email')"
                >
                    <el-input
                        v-model="form.data.email"
                        type="email"
                    />
                </el-form-item>

                <!-- <el-form-item
                    :label="$t('Phone number')"
                    :error="form.formatErrors('telephone.number')"
                >
                    <v-tel-input
                        v-model="form.data.telephone"
                        :preferred-countries="[ 'ua' ]"
                        placeholder=""
                    />
                </el-form-item> -->

                <el-form-item v-if="user.is_admin"
                    :label="$t('Banned')"
                    :error="form.formatErrors('is_banned')"
                    class="el-form-item--inline"
                >
                    <el-switch v-model="form.data.is_banned" />
                </el-form-item>

                <el-form-item v-if="user.is_admin"
                    :label="$t('Active')"
                    :error="form.formatErrors('is_active')"
                    class="el-form-item--inline"
                >
                    <el-switch v-model="form.data.is_active" />
                </el-form-item>

                <el-form-item v-if="user.is_admin"
                    :label="$t('Administrator')"
                    :error="form.formatErrors('is_admin')"
                    class="el-form-item--inline"
                >
                    <el-switch v-model="form.data.is_admin" />
                </el-form-item>
            </div>
        </v-panel>
    </v-panel>
</template>

<script>
    import vocaLowerCase from 'voca/lower_case';
    import vocaTrim from 'voca/trim';
    import md5 from 'md5';
    import _ from 'lodash';
    import { mapState } from 'vuex';

    export default {
        props: {
            form: {
                required: true,
                type: Object
            },
            isEditPage: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            ...mapState('system', [
                'user'
            ])
        },
        data: () => ({
            gravatar: null,
            formWatcher: null
        }),
        mounted() {
            this.$data.formWatcher = this.$props.form.watch(
                'email',
                email => this.fetchGravatar(email)
            );

            this.fetchGravatar(
                this.$props.form.data.email
            );
        },
        destroyed() {
            this.$data.formWatcher();

            this.$data.formWatcher = null;
        },
        methods: {
            debounceFetchGravatar: _.debounce(function (email) {
                    this.fetchGravatar(email);
                },
                1000
            ),

            fetchGravatar(email = null) {
                if (email === null) {
                    return null;
                } else if (email === '') {
                    return this.gravatar = null;
                }

                const hash = md5(
                    vocaLowerCase(
                        vocaTrim(email)
                    )
                );

                const img = document.createElement('img');

                img.src = `https://www.gravatar.com/avatar/${hash}?s=120&d=404`;

                img.onload = () => this.gravatar = img.src;
                img.onerror = () => this.gravatar = null;
            }
        }
    };
</script>
