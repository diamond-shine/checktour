<template>
    <v-panel
        :title="$t('Roles')"
        :description="$t('Available roles list')"
        annotated
    >
        <v-panel>
            <el-form-item :error="form.formatErrors('roles')">
                <div class="c-user-roles">
                    <template v-if="roles.length">
                        <el-checkbox-group v-model="form.data.roles">
                            <el-checkbox
                                v-for="role in roles"
                                :key="role.id"
                                :label="role.id"
                            >{{ role.title }}</el-checkbox>
                        </el-checkbox-group>
                    </template>

                    <v-empty
                        v-else
                        :title="$t('No roles available')"
                        icon="icon c-icon-team"
                        size="md"
                        static-height
                    />
                </div>
            </el-form-item>
        </v-panel>
    </v-panel>
</template>

<script>
    export default {
        props: {
            form: {
                required: true,
                type: Object
            },
            roles: {
                type: Array,
                default: () => ([])
            }
        }
    };
</script>
