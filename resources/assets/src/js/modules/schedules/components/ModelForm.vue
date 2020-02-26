<template>
    <el-form
        :disabled="disabled"
        label-position="top"
        @submit.native.prevent="onSubmit"
    >
        <el-form-item
            :label="$t('Tour')"
            :error="form.formatErrors('tour_id')"
        >
            <v-autocomplete
                id="tours"
                :url="fetchToursUrl"

            >
                <template slot-scope="{ items, fetching }">
                    <el-select
                        v-model="form.data.tour_id"
                        :placeholder="$t('Tour')"
                        :disabled="true">
                        <el-option v-for="tour in items"
                            :key="'tour_' + tour.id"
                            :label="tour.name"
                            :value="tour.id"
                        />
                    </el-select>
                </template>
            </v-autocomplete>
        </el-form-item>


        <el-form-item
            :label="$t('User')"
            :error="form.formatErrors('user_id')"
        >
            <el-select
                v-model="form.data.user_id"
                :placeholder="$t('User')"
                :disabled="true"
            >
                <el-option v-for="user in users"
                    :key="'user_' + user.id"
                    :label="`${user.first_name} ${user.last_name}`"
                    :value="user.id"
                />
            </el-select>
        </el-form-item>

        <el-form-item
            :label="$t('Day')"
            :error="form.formatErrors('excursion_id')"
        >
            <v-autocomplete
                id="excursion_list"
                :url="fetchExcursionrUrl"
                :disabled="!form.data.tour_id"
            >
                <template slot-scope="{ items, fetching }">
                    <el-select
                        v-model="form.data.day"
                        :placeholder="$t('Select')"
                        :disabled="!form.data.tour_id"
                        @change="onChangeDay"
                    >
                        <el-option v-for="excursion in items"
                            :key="'e_' + excursion[0].id"
                            :label="excursion[0].day_title"
                            :value="excursion[0].day"
                        />
                    </el-select>
                </template>
            </v-autocomplete>
        </el-form-item>



        <el-form-item
            :label="$t('Time')"
            :error="form.formatErrors('assigned_at')"
        >
            <v-autocomplete
                id="excursion_time"
                :url="fetchExcursionrUrl"
                :disabled="!form.data.tour_id"
            >
                <template slot-scope="{ items, fetching }">
                    <el-select
                        v-model="form.data.excursion_id"
                        :placeholder="$t('Select')"
                        :disabled="!form.data.tour_id"
                        @change="onChangeTime(items.find((el) => el[0].day == form.data.day ))"
                    >
                        <el-option v-for="excursion in items.find((el) => el[0].day == form.data.day )"
                            :key="'time_' + excursion.id"
                            :label="excursion.time"
                            :value="excursion.id"
                        />
                    </el-select>
                </template>
            </v-autocomplete>
        </el-form-item>

        <v-divider />

        <slot />
    </el-form>
</template>

<script>
    export default {
        props: {
            users: {
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
            fetchToursUrl() {
                return `schedules/tours-autocomplete`;
            },
            // fetchUserUrl() {
            //     return `schedules/${this.form.data.tour_id}/users-autocomplete`;
            // },
            fetchExcursionrUrl() {
                return `schedules/${this.form.data.tour_id}/date-autocomplete`;
            },
        },

        methods: {
            onChangeDay() {
                this.form.data.assigned_at = null
            },

            onChangeTime(items) {
                let element = items.find(el => el.id == this.form.data.excursion_id);

                this.form.data.assigned_at = element.date_time;
            },
            onSubmit() {
                const storeAndContinue = true;
                this.$emit('submit', this.$props.form, storeAndContinue);
            },
            toggle() {
                this.$data.lock = !this.$data.lock;
            }
        }
    };
</script>
