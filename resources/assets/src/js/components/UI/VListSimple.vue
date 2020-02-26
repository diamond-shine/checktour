<template>
    <ul class="c-list-simple">
        <li class="c-list-simple__item">
            <div class="c-list-simple__row">
                <div class="c-list-simple__row-main">
                    <slot name="title" />

                    <template v-if="!$slots.title">
                        <span class="u-fw-bold">{{ title }}</span>
                    </template>
                </div>

                <div
                    v-if="$slots.aside"
                    class="c-list-simple__row-aside"
                >
                    <slot name="aside" />
                </div>
            </div>
        </li>

        <slot />
    </ul>
</template>

<script>
    export default {
        props: {
            title: {
                type: String,
                required: false,
                default: () => null
            }
        }
    };
</script>
