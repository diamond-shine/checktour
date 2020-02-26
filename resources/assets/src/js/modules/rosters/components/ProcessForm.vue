<template>
    <el-form
        label-position="top"
        @submit.native.prevent="onSubmit"
    >
        <el-row>
            <el-col :span="12">
                <!-- <el-form-item
                    v-if="$can('rosters.process')"
                    :label="$t('Enquire closing')"
                    :error="form.formatErrors('is_enquired')"
                >
                    <el-switch
                        :disabled="form.data.is_recruited"
                        v-model="form.data.is_enquired" />
                </el-form-item>
                <v-form-preview
                    v-else
                    :label="$t('Enquire closing')"
                    :value="form.data.is_enquired"
                /> -->

                <el-form-item
                    v-if="$can('rosters.permit')"
                    :label="$t('Roster recruited')"
                    :error="form.formatErrors('is_recruited')"
                >
                    <el-switch
                        :disabled="form.data.is_finished"
                        v-model="form.data.is_recruited" />
                </el-form-item>
                <v-form-preview
                    v-else
                    :label="$t('Roster recruited')"
                    :value="form.data.is_recruited"
                />

                <el-form-item
                    v-if="$can('rosters.process')"
                    :label="$t('Completed')"
                    :error="form.formatErrors('is_finished')"
                >
                    <el-switch
                        :disabled="!form.data.is_recruited"
                        v-model="form.data.is_finished" />
                </el-form-item>
                <v-form-preview
                    v-else
                    :label="$t('Completed')"
                    :value="form.data.is_finished"
                />
            </el-col>
            <el-col :span="12" v-if="$can('rosters.permit') || $can('rosters.process')">
                 <el-form-item
                    v-if="form.data.tour.tour_options.length"
                    :label="$t('Options')">
                    <el-checkbox-group v-model="enabledOptions" >
                        <div v-for="item in form.data.tour.tour_options" :key="item.id" class="mt-10">
                            <el-checkbox
                                v-model="enabledOptions"
                                :disabled="form.data.is_recruited"
                                :label="item.id">{{ item.name }}</el-checkbox>
                        </div>
                    </el-checkbox-group>
                </el-form-item>
            </el-col>
        </el-row>

        <template v-if="isChanged">
            <el-form-item
                :error="form.formatErrors('comment')">
                <el-input
                    type="textarea"
                    :autosize="{ minRows: 2, maxRows: 4}"
                    placeholder="Insert comment"
                    v-model="form.data.comment">
                </el-input>
            </el-form-item>

            <el-button
                class="mb-20"
                    type="success"
                    native-type="submit"
                >{{ $t('Apply') }}
            </el-button>
        </template>

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
                return this.$props.form.changed('is_recruited')
                    || this.$props.form.changed('is_enquired')
                    || this.$props.form.changed('is_finished')
                    || this.$props.form.changed('tour')
                    || this.$props.form.changed('comment')
                    || this.$props.form.changed('disabled_options');
            },
            enabledOptions: {
                get() {
                    let allOptions = this.form.data.tour.tour_options.map(option => option.id);

                    return allOptions.filter((optionId) => {
                        return !this.form.data.disabled_options.includes(optionId);
                    });
                },
                set(values) {
                    let allOptions = this.form.data.tour.tour_options.map(option => option.id);

                    this.form.data.disabled_options = allOptions.filter((optionId) => {
                        return !values.includes(optionId);
                    });
                }
            }
        },
        methods: {
            onSubmit() {
                this.$emit('submit', this.$props.form);
            }
        }
    }
</script>