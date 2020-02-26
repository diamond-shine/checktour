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
                :label="$t('Option')"
                :error="form.formatErrors('tour_option_id')"
            >


                <v-autocomplete
                    id="game-game-subtypes"
                    :url="fetchTourOptionsUrl"
                >
                    <template slot-scope="{ items, fetching }">
                        <el-select v-model="form.data.tour_id" :placeholder="$t('Option')">
                            <el-option v-for="tourOption in items"
                                :key="tourOption.id"
                                :label="tourOption.name"
                                :value="tourOption.id"
                            />
                        </el-select>
                    </template>
                </v-autocomplete>

            </el-form-item>

            <el-form-item
                :label="$t('Price')"
                :error="form.formatErrors('price')"
            >
                <el-input
                    v-model="form.data.price"
                    :maxlength="255"
                />
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
    import { taskManager } from '@plugins/taskManager';
    import http from '@utils/http';

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
            // tour: {
            //     type: Object,
            //     required: true
            // }
        },
        data() {
            return {

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
                if (this.$props.tour) {
                    return `tour-options/${this.$props.formData.ticket_id}/options-autocomplete`
                }

                return '';
            }
        },
        boforeMount() {
            let response = taskManager.run(
                'bookings@options.fetch',
                http.get(`bookings/options/${ticketId}`)
            );

        },
        methods: {
            onClickCancel() {
                this.$emit('cancel');
            },
            onSubmit($form, storeAndContinue = false) {
                this.$emit('submit', $form, storeAndContinue);
            },
            tourOptionsProvider(term) {
                this.$data.tour_options = term;
            },
        }
    };
</script>
