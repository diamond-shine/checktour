<template>
    <div :class="`c-panel${ annotated ? '-annotated' : '' } ${ annotated && last ? 'c-panel-annotated--last' : ''}`">
        <div
            v-show="title || $slots['heading-items']"
            :class="[
                `c-panel${ annotated ? '-annotated' : '' }__heading`,
                {
                    [ `c-panel${ annotated ? '-annotated' : '' }__heading--border` ]: bordered
                }
            ]"
        >
            <template
                v-if="annotated"
                class="c-panel__heading-item"
            >
                <h3
                    v-if="title"
                    class="c-panel-annotated__heading-title"
                >{{ title }}</h3>

                <p
                    v-if="description"
                    class="c-panel-annotated__heading-description"
                >{{ description }}</p>
            </template>

            <div
                v-else
                class="c-panel__heading-item"
            >
                <h3
                    v-if="title"
                    class="c-panel__heading-title"
                >{{ title }}</h3>

                <p
                    v-if="description"
                    class="c-panel__heading-description"
                >{{ description }}</p>
            </div>

            <slot name="heading-items" />
        </div>

        <div :class="[`c-panel${ annotated ? '-annotated' : '' }__main`, mainClass]">
            <slot />
        </div>

        <div
            v-show="$slots['footer']"
            :class="footerClass"
        >
            <slot name="footer" />
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            title: {
                type: String,
                default: null
            },
            description: {
                type: String,
                default: null
            },
            annotated: {
                type: Boolean,
                default: false
            },
            last: {
                type: Boolean,
                default: false
            },
            bordered: {
                type: Boolean,
                default: true
            },
            mainClass: {
                type: [ String, Object, Array ],
                default: ''
            },
            footerClass: {
                type: [ String, Object, Array ],
                default: 'c-panel__footer'
            }
        }
    };
</script>
