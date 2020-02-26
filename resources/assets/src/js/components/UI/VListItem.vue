<template>
    <componenet
        :to="route"
        :is="component"
        :class="{ 'c-list__item--hovered': component === 'router-link' }"
        class="c-list__item"
    >
        <slot name="prepend" />

        <slot :data="data">
            <div class="c-list__main">
                <slot
                    :data="data"
                    name="main"
                >
                    <div
                        v-if="imageBy"
                        :class="{ 'c-list__main-img': imageRound }"
                        class="c-list__main-img"
                    >
                        <img
                            v-if="$lodash.has(data, imageBy)"
                            :src="$lodash.get(data, imageBy)"
                        >

                        <div
                            v-else
                            class="c-list__main-icon"
                        >
                            <i class="fal fa-image" />
                        </div>
                    </div>

                    <div class="c-list__main-inner">
                        <div class="title">
                            <slot
                                :title="$lodash.get(data, titleBy)"
                                name="title"
                            >
                            {{
                                $lodash.isFunction(titleBy) ?
                                    titleBy(data) :
                                    $lodash.get(data, titleBy)
                            }}
                            </slot>
                        </div>

                        <div
                            v-show="descriptionBy"
                            class="description"
                        >
                            <slot
                                :description="$lodash.get(data, descriptionBy)"
                                name="description"
                            >{{
                                $lodash.isFunction(descriptionBy) ?
                                    descriptionBy(data) :
                                    $lodash.get(data, descriptionBy)
                            }}
                            </slot>
                        </div>
                    </div>
                </slot>
            </div>

            <div class="c-list__aside">
                <slot
                    :data="data"
                    name="aside"
                />
            </div>
        </slot>
    </componenet>
</template>

<script>
    export default {
        props: {
            data: {
                required: true,
                type: Object
            },
            route: {
                type: Object,
                default: null
            },
            titleBy: {
                type: [ String, Function ],
                default: 'title'
            },
            descriptionBy: {
                type: [ String, Function ],
                default: null
            },
            imageBy: {
                type: String,
                default: null
            },
            imageRound: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            component() {
                return this.$props.route ? 'router-link' : 'div';
            }
        }
    };
</script>
