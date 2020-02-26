<template>
    <el-form
        :disabled="disabled"
        label-position="top"
        @submit.native.prevent="onSubmit"
    >
        <el-form-item
            :label="$t('Назва')"
            :error="form.formatErrors('title')"
        >
            <el-input v-model="form.data.title" />
        </el-form-item>

        <el-form-item
            :label="$t('Ключ')"
            :error="form.formatErrors('name')"
        >
            <v-slugify
                v-model="form.data.name"
                :from="form.data.title"
            />
        </el-form-item>

        <v-divider />

        <el-form-item :label="$t('Права')">
            <el-tree
                ref="permissions"
                :data="permissions"
                :props="{ label: 'title', children: 'permissions' }"
                :default-checked-keys="form.data.permissions"
                :default-expanded-keys="form.data.permissions"
                node-key="name"
                show-checkbox
                @check="onCheckPermissions"
            />
        </el-form-item>

        <v-divider />

        <slot />
    </el-form>
</template>

<script>
    export default {
        props: {
            form: {
                type: Object,
                required: true
            },
            disabled: {
                type: Boolean,
                default: false
            },
            permissions: {
                type: Array,
                required: false,
                default: () => ([])
            }
        },
        methods: {
            onSubmit() {
                this.$emit('submit', this.$props.form);
            },
            onCheckPermissions() {
                this.$props.form.fill({
                    permissions: this.$refs.permissions.getCheckedKeys(true)
                });
            }
        }
    };
</script>
