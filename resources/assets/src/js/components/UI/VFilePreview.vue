<template>
    <div :class="{ 'c-gallery': multiple }">
        <template v-if="multiple">
            <template v-if="data.length">
                <div
                    v-for="item in data"
                    :key="item.id"
                    class="c-gallery__item"
                >
                    <v-file
                        :file="item"
                        :remove="false"
                        status="preview"
                        @preview="preview(item)"
                    />
                </div>
            </template>

            <div
                v-else
                class="c-form-preview__value text-muted"
            ><em>({{ noValue }})</em></div>
        </template>

        <template v-if="!multiple">
            <v-file
                v-if="data"
                :file="data"
                :remove="false"
                size="md"
                status="preview"
                @preview="preview(data)"
            />

            <div
                v-else
                class="c-form-preview__value text-muted"
            ><em>({{ noValue }})</em></div>
        </template>
    </div>
</template>

<script>
    import { mapActions } from 'vuex';

    export default {
        props: {
            data: {
                type: [ Object, Array ],
                default: null
            },
            multiple: {
                type: Boolean,
                default: false
            },
            noValue: {
                type: String,
                default() {
                    return this.$t('Value not set');
                }
            }
        },
        methods: {
            ...mapActions('file-uploader/preview', {
                preview(dispatch, file) {
                    return dispatch('open', file);
                }
            })
        }
    };
</script>
