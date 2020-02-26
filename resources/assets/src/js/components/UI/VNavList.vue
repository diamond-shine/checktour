<template>
    <div v-if="$slots.default">
        <v-divider v-if="divider" />

        <ul class="c-nav-list">
            <slot />
        </ul>
    </div>
</template>

<script>
    export default {
        props: {
            divider: {
                type: Boolean,
                default: false
            }
        }
    };
</script>
