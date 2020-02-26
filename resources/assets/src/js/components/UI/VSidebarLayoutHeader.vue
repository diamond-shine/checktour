<template>
    <div class="c-sidebar__heading-inner" style="font-size: 1.8rem;">
        <div
            v-show="$slots.left || backRoute"
            class="left"
        >
            <slot name="left">
                <router-link
                    v-if="backRoute"
                    :to="backRoute"
                    class="btn  pr-30"
                ><i class="c-icon-arrow2-left" /></router-link>
            </slot>
        </div>

        <h4 class="title">{{ title }}</h4>

        <div
            v-show="$slots.right"
            class="right"
        >
            <slot name="right" />
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            title: {
                required: true,
                type: String
            },
            backRoute: {
                type: Object,
                default: null
            }
        }
    };
</script>
