<template>
    <el-breadcrumb
        v-if="items.length"
        separator=">"
    >
        <span
            v-for="(item, index) in items"
            :key="item.route.name"
            class="el-breadcrumb__item"
        >
            <router-link
                :to="item.route"
                role="link"
                class="el-breadcrumb__inner is-link"
            >{{ item.title }}</router-link><!--
            --><span
                v-if="index < items.length"
                role="presentation"
                class="el-breadcrumb__separator"
            >&gt;</span>
        </span>
    </el-breadcrumb>
</template>

<script>
    import { Breadcrumbs } from '@plugins/breadcrumbs';

    export default {
        props: {
            breadcrumbs: {
                type: Breadcrumbs,
                default: undefined
            }
        },
        computed: {
            items() {
                return this.$props.breadcrumbs.build();
            }
        }
    };
</script>
