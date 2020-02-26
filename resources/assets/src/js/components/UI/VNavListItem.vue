<template>
    <router-link
        :to="route"
        tag="li"
        class="c-nav-list__item"
    >
        <div class="c-nav-list__item-title">
            <slot name="title">{{ title }}</slot>
        </div>

        <div
            v-show="description || $slots.description"
            class="c-nav-list__item-description"
        >
            <slot name="description">{{ description }}</slot>
        </div>

        <i class="icon c-icon-arrow-right" />
    </router-link>
</template>

<script>
    export default {
        props: {
            route: {
                required: true,
                type: Object
            },
            title: {
                type: String,
                default: ''
            },
            description: {
                type: String,
                default: null
            }
        }
    };
</script>
