<template>
    <el-form
        label-position="top"
        @submit.native.prevent="onSubmit"
    >
        <v-form-gallery-list
            v-model="form.data.images"
            :size="'md'"
            :cwd="cwd"
            ></v-form-gallery-list>

        <el-button v-if="isChanged"
            class="mt-20"
            type="success"
            native-type="submit"
        >
            {{ $t('Apply') }}
        </el-button>

    </el-form>
</template>

<script>
export default {
    props: {
        form: {
            type: Object,
            required: true
        }
    },
    computed: {
        isChanged() {
            return this.form.changed('images');
        },
        cwd() {
            return [{
                name: this.$t('Roster'),
                mark: 'rosters_receipts'
            }, {
                name: 'receipt',
                mark: this.$routeParam('rosterId')
            }];
        }
    },
    methods: {
        onSubmit() {
            this.$emit('submit', this.$props.form);
        }
    }
}
</script>