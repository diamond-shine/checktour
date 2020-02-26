<template>
    <vue-scroll
        v-loading="locked"
        ref="scroll"
        :class="className"
        :ops="config"
        class="o-scroll"
    >
        <slot v-if="!locked" />
    </vue-scroll>
</template>

<script>
    export default {
        props: {
            className: {
                type: String,
                default: null
            },
            locked: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                defaultConfig: {
                    scrollPanel: {
                        scrollingX: false
                    },
                    bar: {
                        background: 'var(--ps-color)'
                    }
                }
            };
        },
        computed: {
            config() {
                return this.$data.defaultConfig;
            }
        },
        methods: {
            scrollTo(data, anim = true) {
                this.$refs.scroll.scrollTo(data, anim);
            }
        }
    };
</script>
