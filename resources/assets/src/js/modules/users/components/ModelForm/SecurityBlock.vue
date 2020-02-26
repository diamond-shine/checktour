<template>
    <v-panel
        :title="$t('Security')"
        :description="$t('Password changing')"
        annotated
    >
        <v-panel>
            <div class="u-unwrap-bottom">
                <div class="row row--sm">
                    <div class="col-6">
                        <el-form-item
                            :label="$t('Password')"
                            :error="form.formatErrors('password')"
                        >
                            <el-input
                                v-model="form.data.password"
                                type="password"
                            />
                        </el-form-item>
                    </div>

                    <div class="col-6">
                        <el-form-item
                            :label="$t('Confirm password')"
                            :error="form.formatErrors('password_confirmation')"
                        >
                            <el-input
                                v-model="form.data.password_confirmation"
                                type="password"
                            />
                        </el-form-item>
                    </div>
                </div>
            </div>
        </v-panel>
    </v-panel>
</template>

<script>
    export default {
        props: {
            form: {
                required: true,
                type: Object
            }
        }
    };
</script>
