<template>
    <div>
        <span
            class="c-file__sizer"
            aria-hidden="true"
        />
        <div
            class="c-file__upload c-file__inner"
            @click="choose"
        >
            <img
                v-if="placeholderUrl"
                :src="placeholderUrl"
            >

            <i
                v-else-if="icon"
                :class="icon"
                class="icon"
            />
        </div>
    </div>
</template>

<script>
    import { mapActions } from 'vuex';

    export default {
        props: {
            icon: {
                type: String,
                default: null
            },
            placeholderUrl: {
                type: String,
                default: null
            },
            disabled: {
                type: Boolean,
                default: false
            },
            multiple: {
                type: Boolean,
                default: false
            },
            cwd: {
                type: [ Array, String, Object ],
                default: null
            },
            accept: {
                type: Array,
                default: () => ([])
            }
        },
        methods: {
            ...mapActions('file-manager', {
                async choose(dispatch) {
                    if (this.$props.disabled) {
                        return;
                    }

                    const data = await dispatch('open', {
                        multiple: this.$props.multiple,
                        accept: this.$props.accept,
                        cwd: this.$props.cwd
                    });

                    this.$emit('choose', data);
                }
            })
        }
    };
</script>
