<template>
    <v-panel
        :title="$t('Tour options')"
        :description="$t('Tour options management')"
        class="c-user-general"
        annotated
    >
        <v-panel>
            <div class="u-unwrap-bottom">

                <el-form-item v-for="(item, index) in form.data.options" :key="index"
                    :label="item.name"
                    :error="form.formatErrors('options')"
                    class="el-form-item--inline"
                >
                    <el-switch v-model="item.is_active" />
                </el-form-item>
            </div>
        </v-panel>
    </v-panel>
</template>

<script>
    import { mapState } from 'vuex';

    export default {
        props: {
            form: {
                required: true,
                type: Object
            }
        },
        computed: {
            ...mapState('system', [
                'user'
            ])
        },
        data: () => ({
        }),
        mounted() {

        },
        destroyed() {

        },
        methods: {

        }
    };
</script>
