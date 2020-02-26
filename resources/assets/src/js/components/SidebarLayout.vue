<template>
    <v-scroll class="o-layout__sidebar-scroll">
        <div
            v-loading="loading"
            class="o-layout__sidebar-inner"
        >
            <template v-if="$slots.header">
                <div class="c-sidebar__heading">
                    <slot name="header" />
                </div>
            </template>

            <template v-if="$slots.body">
                <div class="c-sidebar__main">
                    <slot name="body" />
                </div>
            </template>

            <template v-if="$slots.footer">
                <v-divider />

                <div class="c-sidebar__main">
                    <slot name="footer" />
                </div>
            </template>
        </div>
    </v-scroll>
</template>

<script>
    export default {
        props: {
            loading: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            sizeClass() {
                return this.$props.size ?
                    `o-layout__sidebar--${this.$props.size}` :
                    '';
            }
        }
    };
</script>
