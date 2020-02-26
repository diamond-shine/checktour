<template>
    <div
        v-loading="loading"
    >
        <div
            v-if="items.length"
            :class="{
                'c-list--strip-last': stripLast,
                'c-list--unbox': unbox
            }"
            class="c-list"
        >
            <template v-for="(item, index) in items">
                <slot
                    :item="item"
                    :index="index"
                />
            </template>
        </div>

        <v-empty
            v-else
            :title="$t('Empty list…')"
            icon="c-icon-question"
            size="lg"
        />

        <v-pagination
            v-if="pagination"
            :data="pagination"
            @change-page="onChangePage"
        />
    </div>
</template>

<script>
    export default {
        props: {
            items: {
                required: true,
                type: Array
            },
            pagination: {
                type: Object,
                default: null
            },
            loading: {
                type: Boolean,
                default: false
            },
            stripLast: {
                type: Boolean,
                default: false
            },
            unbox: {
                type: Boolean,
                default: false
            }
        },
        methods: {
            onChangePage(page) {
                this.$emit('change-page', page);
            }
        }
    };
</script>
