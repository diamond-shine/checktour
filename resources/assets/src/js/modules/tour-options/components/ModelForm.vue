<template>
    <el-form
        :disabled="disabled"
        label-position="top"
        @submit.native.prevent="onSubmit"
    >
        <el-form-item
            :label="$t('Tour')"
            :error="form.formatErrors('tour_id')"
            required
        >
            <el-input :disabled="true" v-model="tour.name" />
        </el-form-item>

        <el-form-item
            :label="$t('Name')"
            :error="form.formatErrors('name')"
            required
        >
            <el-input v-model="form.data.name" />
        </el-form-item>

        <el-form-item
            class="mb-0"
            :label="$t('Bookeo identifier 2')"
            :error="form.formatErrors('bookeo_id')"
            required
        >
            <template v-for="(item, n) in form.data.bookeo_id">
                <el-input v-model="form.data.bookeo_id[n]" :key="n" class="mb-20">
                    <i v-if="n == form.data.bookeo_id.length - 1"
                        :class="'fal fa-plus'"
                        slot="suffix"
                        @click.prevent="addItem()"
                        class="el-input__icon"
                    />
                    <i v-else
                        :class="'fal fa-times'"
                        slot="suffix"
                        @click.prevent="removeItem(item)"
                        class="el-input__icon"
                    />
                </el-input>
            </template>

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
            form: {
                type: Object,
                required: true
            },
            disabled: {
                type: Boolean,
                default: false
            },
            tour: {
                type: Object,
                required: true
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
            addItem() {
                this.form.data.bookeo_id.push('');
            },
            removeItem(identifier) {

                let index = this.form.data.bookeo_id.findIndex((element) => {
                    return identifier == element;
                });

                if (index >= 0) {
                    this.form.data.bookeo_id.splice(index, 1);
                }
            },
            onSearch(term) {
                this.$data.term = term;
            },
            onSubmit() {
                this.$props.form.tour_id = this.tour.id
                this.$emit('submit', this.$props.form);
            },
            toggle() {
                this.$data.lock = !this.$data.lock;
            }
        },
        beforeMount() {
            if (!this.form.data.bookeo_id) {
                this.form.data.bookeo_id = [''];
            }
        }
    };
</script>
