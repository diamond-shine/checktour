<template>
    <div class="u-page">
        <el-form
            label-position="top"
            @submit.native.prevent="onSubmit"
        >
            <general-block :form="form" />

            <security-block :form="form" />

            <roles-block v-if="user.is_admin"
                :form="form"
                :roles="roles"
            />

            <!-- <permissions-block
                :form="form"
                :permissions="permissions"
                :leaf-only-permissions="leafOnlyPermissions"
            /> -->

            <el-button
                v-show="false"
                native-type="submit"
            />
        </el-form>
    </div>
</template>

<script>
    import GeneralBlock from './ModelForm/GeneralBlock';
    import SecurityBlock from './ModelForm/SecurityBlock';
    import RolesBlock from './ModelForm/RolesBlock';
    import PermissionsBlock from './ModelForm/PermissionsBlock';
    import { mapState } from 'vuex';

    export default {
        components: {
            GeneralBlock,
            SecurityBlock,
            RolesBlock,
            PermissionsBlock
        },
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
            },
            roles: {
                type: Array,
                default: () => ([])
            }
        },
        computed: {
            ...mapState('system', [
                'user'
            ])
        },
        methods: {
            onSubmit() {
                this.$emit('submit', this.$props.form);
            }
        }
    };
</script>
