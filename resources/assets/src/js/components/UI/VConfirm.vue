<template>
    <el-dialog
        :visible="data.show"
        :title="question"
        width="500px"
        @close="cancel"
    >
        <div class="el-message-box__content">
            <div
                v-if="type"
                class="el-message-box__status"
            >
                <div :class="`el-message-box__status el-icon-${type}`" />
            </div>

            <div class="el-message-box__message">{{ text }}</div>
        </div>

        <span
            slot="footer"
            class="dialog-footer"
        >
            <el-button
                :type="cancelButtonType"
                size="small"
                @click="cancel"
            >{{ cancelButtonText }}</el-button>
            <el-button
                ref="submit"
                :type="confirmButtonType"
                size="small"
                autofocus
                @click="confirm"
            >{{ confirmButtonText }}</el-button>
        </span>
    </el-dialog>
</template>

<script>
    export default {
        props: {
            data: {
                type: Object,
                default: null
            }
        },
        computed: {
            question() {
                return this.$props.data.payload?.question;
            },

            type() {
                return this.$props.data.payload?.type;
            },

            text() {
                return this.$props.data.payload?.text;
            },

            cancelButtonText() {
                return this.$props.data.payload?.cancelButtonText || 'Close';
            },

            confirmButtonText() {
                return this.$props.data.payload?.confirmButtonText || 'Confirm';
            },

            cancelButtonType() {
                return this.$props.data.payload?.cancelButtonType || 'default';
            },

            confirmButtonType() {
                return this.$props.data.payload?.confirmButtonType || 'primary';
            },

            distinguishCancelAndClose() {
                return !!this.$props.data.payload?.distinguishCancelAndClose;
            }
        },
        watch: {
            'data.show': {
                handler(value) {
                    if (value) {
                        this.$refs.submit.$el.blur();
                        this.$nextTick(
                            () => this.$refs.submit.$el.focus()
                        );
                    }
                },
                immediate: true
            }
        },
        methods: {
            close() {
                this.$emit('close');
            },

            cancel() {
                if (!this.distinguishCancelAndClose) {
                    this.$props.data.payload.reject('cancel');
                }

                this.close();
            },

            confirm() {
                this.$props.data.payload.resolve();

                this.close();
            }
        }
    };
</script>
