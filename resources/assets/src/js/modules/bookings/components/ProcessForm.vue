<template>
    <el-form
        label-position="top"
        @submit.native.prevent="onSubmit"
    >
        <el-form-item
            :error="form.formatErrors('tour_id')"
        >
            <el-select
                v-model="form.data.schedule_id"
                :placeholder="$t('Assign guide / time')"
                :disabled="isProcessed || !enabledForRostering">

                <el-option :label="$t('Not selected')" :value="null" />

                <el-option v-if="scheduleId && attachSelected(schedules)"
                    :label="form.data.schedule.user.full_name + ' ' + form.data.schedule.excursion.time"
                    :value="form.data.schedule.id"
                />

                <el-option v-for="schedule in schedules"
                    :key="schedule.id"
                    :label="schedule.user.full_name + ' ' + schedule.excursion.time"
                    :value="schedule.id"
                />
            </el-select>
        </el-form-item>

        <el-row :gutter="20">
            <el-col :span="12">
                <el-form-item
                    class="mb-0"
                    :label="$t('Check in')"
                    for="check_in"
                    :error="form.formatErrors('arrived_waiting_room')"
                >
                    <el-switch
                        id="check_in"
                        :disabled="!!form.data.schedule_id"
                        @change="autoAssign"
                        v-model="form.data.arrived_waiting_room" />
                </el-form-item>
            </el-col>
            <el-col :span="12">
                <el-form-item
                    class="mb-0"
                    v-if="isChanged"
                    :label="$t('Add comment')"
                >
                    <el-switch
                        v-model="commentForm" />
                </el-form-item>
            </el-col>
        </el-row>

        <template v-if="isChanged">
            <el-form-item
                v-if="commentForm"
                :error="form.formatErrors('comment')"
                class="mt-20">
                <el-input
                    type="textarea"
                    :autosize="{ minRows: 2, maxRows: 4}"
                    placeholder="Insert comment"
                    v-model="form.data.comment">
                </el-input>
            </el-form-item>

            <v-divider />

            <el-button
                    type="success"
                    native-type="submit"
                >{{ $t('Apply') }}
            </el-button>
        </template>

    </el-form>
</template>

<script>
    import { mapState } from 'vuex';
    import http from '@utils/http';
    import { taskManager } from '@plugins/taskManager';

    export default {
        props: {
            form: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                forms: null,
                selected: null,
                scheduleId: null,
                commentForm: false,
                schedules: []
            }
        },
        computed: {
            ...mapState('system', [
                'user'
            ]),
            isChanged() {
                return this.$props.form.changed('schedule_id')
                    || this.$props.form.changed('arrived_waiting_room');
            },
            enabledForRostering() {
                if (!this.$props.form.data || !this.$props.form.data.arrived_waiting_room) {
                    return false;
                }

                if (!this.$props.form.data.schedule) {
                    return true;
                }


                if (this.$props.form.data.schedule.user_id == this.user.id) {
                    return true;
                }

                return this.$can('rosters.permit');
            },
            isProcessed() {
                if (!this.$props.form.data || !this.$props.form.data.schedule) {
                    return false;
                }

                return this.$props.form.data.schedule.is_finished;
            }
        },
        methods: {
            fetchSchedules: async function () {
                let { data:{ data }} = await this.$taskManager.run(
                    `autocomplete@schedule.fetch`,
                    http.get('bookings/autocomplete?tour_id=' + this.form.data.tour_id)
                );

                this.schedules = data.items;
            },
            autoAssign() {
                if (this.schedules.length && this.form.data.arrived_waiting_room && !this.$props.form.data.schedule_id) {
                    this.$props.form.data.schedule_id = this.schedules[0].id;
                }
            },
            onSubmit() {
                this.$emit('submit', this.$props.form);
            },

            attachSelected(items) {
                if (!this.$props.form.data) {
                    return false;
                }

                if (!this.$props.form.data.schedule_id) {
                    return false;
                }

                let exist = items.find((item) =>  {
                    return item.id == this.$props.form.data.schedule_id
                })

                if (!! exist) {
                    return false;
                }

                return true;
            },
        },
        beforeMount() {
            this.scheduleId = this.$props.form.data.schedule_id;
            this.fetchSchedules();
        }
    }
</script>
