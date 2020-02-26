<template>
    <div
        v-loading="loading"
    >
        <v-list
            v-if="items.length"
            :items="items"
            :strip-first="stripFirst"
            :strip-last="stripLast"
            :unbox="unbox"
            :class="listClass"
            :top="top"
        >
            <template v-slot="{ item, index }">
                <slot
                    :item="item"
                    :index="index"
                />
            </template>
        </v-list>

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
            stripFirst: {
                type: Boolean,
                default: false
            },
            unbox: {
                type: Boolean,
                default: false
            },
            top: {
                type: Boolean,
                default: false
            },
            listClass: {
                type: [String, Object, Array],
                default: null
            }
        },
        methods: {
            onChangePage(page) {
                this.$emit('change-page', page);
            }
        }
    };
</script>
