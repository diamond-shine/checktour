<template>
    <div
        :class="{ 'u-color-gray': !data.is_active }"
        class="el-tree-node__label"
    >
        <span v-html="data[labelKey]" />

        <slot
            v-if="!sorting"
            name="aside"
        />

        <i
            v-else
            class="edit fal fa-arrows"
        />
    </div>
</template>

<script>
    export default {
        props: {
            data: {
                required: true,
                type: Object
            },
            sorting: {
                type: Boolean,
                default: false
            },
            labelKey: {
                type: String,
                required: false,
                default: 'label'
            }
        },
        methods: {
            onClickView({ id }) {
                this.$emit('view', { id });
            }
        }
    };
</script>
