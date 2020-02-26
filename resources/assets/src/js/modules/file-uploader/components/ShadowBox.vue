<template>
    <el-dialog
        :visible="show"
        :title="$t('Preview')"
        width="auto"
        top="auto"
        class="c-shadow-box"
        @close="close"
    >
        <img
            v-if="image"
            :src="image.url"
            width="100%"
        >
    </el-dialog>
</template>

<script>
    import { mapState, mapActions } from 'vuex';

    export default {
        data: () => ({
            show: false
        }),
        computed: {
            ...mapState('file-uploader/preview', [ 'image' ])
        },
        watch: {
            image(value) {
                if (value) {
                    this.$data.show = true;
                }
            }
        },
        methods: {
            ...mapActions('file-uploader/preview', {
                async close(dispatch) {
                    this.$data.show = false;

                    setTimeout(() => {
                        dispatch('close');
                    }, 300);
                }
            })
        }
    };
</script>
