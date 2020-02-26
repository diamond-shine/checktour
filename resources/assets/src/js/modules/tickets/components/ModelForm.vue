<template>
    <el-form
        :disabled="disabled"
        label-position="top"
        @submit.native.prevent="onSubmit"
    >
        <el-form-item
            :label="$t('Name')"
            :error="form.formatErrors('name')"
            required
        >
            <el-input v-model="form.data.name" />
        </el-form-item>


        <el-form-item
            :label="$t('Bookeo ticket type identifier')"
            :error="form.formatErrors('bookeo_type')"
            required
        >
            <el-input v-model="form.data.bookeo_type" />
        </el-form-item>

        <el-form-item
            :label="$t('Tour')"
            :error="form.formatErrors('tour_id')"
            required
        >
            <el-select v-model="form.data.tour_id"
                disabled
                :placeholder="$t('Tour')">
                <el-option v-for="tour in tours"
                    :key="tour.id"
                    :label="tour.name"
                    :value="tour.id"
                >
                </el-option>
            </el-select>
        </el-form-item>

        <el-form-item
            :label="$t('Price')"
            :error="form.formatErrors('price')"
            required
        >
            <el-input v-model="form.data.price" />
        </el-form-item>

        <el-form-item
            :label="$t('Active')"
            :error="form.formatErrors('is_active')"
        >
            <el-switch v-model="form.data.is_active" />
        </el-form-item>

        <v-divider />

        <slot />
    </el-form>
</template>

<script>
    export default {
        props: {
            tours: {
                type: Array,
                required: true
            },
            form: {
                type: Object,
                required: true
            },
            disabled: {
                type: Boolean,
                default: false
            }
        },
        data: () => ({
            lock: true,
            term: ''

        }),

        computed: {
            iconClass() {
                return this.lock ?
                    'fal fa-lock-alt' :
                    'fal fa-unlock-alt';
            }
        },

        methods: {
            onSearch(term) {
                this.$data.term = term;
            },
            onSubmit() {
                this.$emit('submit', this.$props.form);
            },
            toggle() {
                this.$data.lock = !this.$data.lock;
            }
        }
    };
</script>
