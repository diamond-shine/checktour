<template>
    <v-form
        ref="form"
        :data="formData"
        :name="formName"
        #default="{ form, submit }"
        @submit="onSubmit"
    >
        <el-form
            :disabled="form.processing"
            label-position="top"
            @submit.native.prevent="submit"
        >
            <el-form-item
                :label="$t('Tour')"
                :error="form.formatErrors('tour_id')"
            >
                <el-input
                    v-model="tour.name"
                    :maxlength="255"
                    :disabled="true"
                />
            </el-form-item>

            <el-form-item
                :label="$t('Day')"
                :error="form.formatErrors('tour_option_id')"
            >
                <el-select
                    v-model="form.data.day"
                    style="width: 49%"

                    placeholder="Select">
                    <el-option
                      v-for="item in days"
                      :key="item.value"
                      :label="item.label"
                      :value="item.value">
                    </el-option>
                </el-select>

                <el-time-select

                    style="width: 49%"
                    v-model="form.data.time"
                    :picker-options="{
                    start: '08:30',
                    step: '00:15',
                    end: '22:30'
                    }"
                    placeholder="Select time">
                </el-time-select>
            </el-form-item>

            <el-form-item>

            </el-form-item>

            <el-button
                type="success"
                native-type="submit"
            >{{ $t('Save') }}
            </el-button>

            <el-button
                v-if="isCreating"
                type="primary"
                @click="onSubmit(form, true)"
            >{{ $t('Save and continue') }}
            </el-button>

            <el-button
                @click="onClickCancel"
            >{{ $t('Cancel') }}
            </el-button>
        </el-form>
    </v-form>
</template>

<script>
    export default {
        props: {
            formName: {
                type: String,
                required: true
            },
            isCreating: {
                type: Boolean,
                required: false,
                default: false
            },
            formData: {
                type: Object,
                required: false,
                default: () => {}
            },
            tour: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                days: [
                    {value: 1, label: 'Monday'},
                    {value: 2, label: 'Tuesday'},
                    {value: 3, label: 'Wednesday'},
                    {value: 4, label: 'Thursday'},
                    {value: 5, label: 'Friday'},
                    {value: 6, label: 'Saturday'},
                    {value: 7, label: 'Sunday'},
                ]
            }
        },
        computed: {
            cwd() {
                return [{
                    name: this.$t('Параметри'),
                    mark: 'parameters'
                }];
            },
            fetchTourOptionsUrl() {
                return `tour-options/${this.$props.formData.ticket_id}/options-autocomplete`
            },
        },
        // boforeMount() {
        //     let response = taskManager.run(
        //         'ticket-options@four',
        //         http.get(`view/${ticketId}/tour`)
        //     );
        // },
        methods: {
            onClickCancel() {
                this.$emit('cancel');
            },
            onSubmit($form, storeAndContinue = false) {
                $form.data.tour_id = this.tour.id;

                this.$emit('submit', $form, storeAndContinue);
            },
            tourOptionsProvider(term) {
                this.$data.tour_options = term;
            },
        }
    };
</script>
