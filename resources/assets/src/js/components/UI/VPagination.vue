<template>
    <el-pagination
        v-if="data.last_page > 1"
        :disabled="disabled"
        :current-page="data.current_page"
        :page-size="data.per_page"
        :total="data.total"
        layout="prev, pager, next"
        :pager-count="5"
        @current-change="onChangePage"
    />
</template>

<script>
    export default {
        props: {
            data: {
                required: true,
                type: Object
            },
            disabled: {
                type: Boolean,
                default: false
            }
        },
        methods: {
            onChangePage(page) {
                if (page === this.$props.data.current_page) {
                    return;
                }

                this.$emit('change-page', page);
            }
        }
    };
</script>
