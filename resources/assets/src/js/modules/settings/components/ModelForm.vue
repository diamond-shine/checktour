<template>
    <div class="u-page">
        <el-form
            label-position="top"
            @submit.native.prevent="onSubmit"
        >
            <options-block :form="form" />


            <el-button
                v-show="false"
                native-type="submit"
            />
        </el-form>

        <imports-block />
    </div>
</template>

<script>
    import OptionsBlock from './ModelForm/OptionsBlock';
    import ImportsBlock from './ModelForm/ImportsBlock';
    import { mapState } from 'vuex';

    export default {
        components: {
            OptionsBlock,
            ImportsBlock
        },
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
        methods: {
            onSubmit() {
                this.$emit('submit', this.$props.form);
            }
        }
    };
</script>
