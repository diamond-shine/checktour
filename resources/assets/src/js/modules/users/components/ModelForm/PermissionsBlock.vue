<template>
    <v-panel
        :title="$t('Access rights')"
        :description="$t('Available rights list')"
        annotated
        last
    >
        <v-panel>
            <el-form-item>
                <el-tree
                    v-if="permissions.length"
                    ref="permissions"
                    :data="permissions"
                    :props="{
                        label: 'title',
                        children: 'permissions'
                    }"
                    :default-checked-keys="selected"
                    node-key="name"
                    show-checkbox
                    @check="syncPermissions"
                />

                <v-empty
                    v-else
                    :title="$t('No rights available')"
                    icon="fal fa-gavel"
                    size="md"
                    static-height
                />
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
            permissions: {
                type: Array,
                default: () => ([])
            },
            leafOnlyPermissions: {
                type: Object,
                default: null
            }
        },
        computed: {
            selected() {
                return this.$props.form.data.permissions.filter(
                    permission => this.$props.leafOnlyPermissions.hasOwnProperty(permission)
                );
            }
        },
        methods: {
            syncPermissions() {
                this.$props.form.fill({
                    permissions: this.$lodash.concat(
                        this.$refs.permissions.getCheckedKeys(),
                        this.$refs.permissions.getHalfCheckedKeys()
                    )
                });
            }
        }
    };
</script>
