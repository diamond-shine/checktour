<template>
    <el-tooltip
        :content="copyText"
        class="item"
        effect="dark"
        placement="right"
    >
        <el-button
            icon="fal fa-copy"
            @click="onCopy"
        />
    </el-tooltip>
</template>

<script>
    import { $t } from '@utils/i18n';

    export default {
        props: {
            resolve: {
                required: true,
                type: Function
            }
        },
        data: () => ({
            copyText: $t('Скопіювати')
        }),
        methods: {
            onCopy() {
                this.$props.resolve().select();

                document.execCommand('copy');

                this.$data.copyText = this.$t('Скопійовано');

                window.setTimeout(() => {
                    this.$data.copyText = this.$t('Скопіювати');
                }, 1500);
            }
        }
    };
</script>
