<template>
    <v-form
        ref="form"
        :data="formData"
        :name="formName"
        #default="{ form, submit }"
        @submit="onSubmit"
    >
        <el-form
            :disabled="form.processing"
            label-position="top"
            @submit.native.prevent="submit"
        >
            <el-form-item
            :label="$t('User')"
            :error="form.formatErrors('user_id')"
            >
                <v-autocomplete
                    id="users-list"
                    :url="fetchUserUrl"
                >
                    <template slot-scope="{ items, fetching }">
                        <el-select
                            v-model="form.data.user_id"
                            :placeholder="$t('User')"
                        >
                            <el-option v-for="user in items"
                                :key="user.id"
                                :label="user.user_name"
                                :value="user.id"
                            />
                        </el-select>
                    </template>
                </v-autocomplete>
            </el-form-item>

            <el-button
                type="success"
                native-type="submit"
            >{{ $t('Save') }}
            </el-button>

            <el-button
                v-if="isCreating"
                type="primary"
                @click="onSubmit(form, true)"
            >{{ $t('Save and continue') }}
            </el-button>

            <el-button
                @click="onClickCancel"
            >{{ $t('Cancel') }}
            </el-button>
        </el-form>
    </v-form>
</template>

<script>
    export default {
        props: {
            formName: {
                type: String,
                required: true
            },
            isCreating: {
                type: Boolean,
                required: false,
                default: false
            },
            formData: {
                type: Object,
                required: false,
                default: () => {}
            },
            // tour: {
            //     type: Object,
            //     required: true
            // }
        },
        data() {
            return {

            }
        },
        computed: {
            cwd() {
                return [{
                    name: this.$t('Параметри'),
                    mark: 'parameters'
                }];
            },
            fetchUserUrl() {
                return 'tours/users-autocomplete';
            }
        },

        methods: {
            onClickCancel() {
                this.$emit('cancel');
            },
            onSubmit($form, storeAndContinue = false) {
                this.$emit('submit', $form, storeAndContinue);
            }
        }
    };
</script>
