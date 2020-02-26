<template>
    <el-form
        :disabled="disabled"
        label-position="top"
        @submit.native.prevent="onSubmit"
    >
        <el-form-item
            :label="$t('Booking number')"
            :error="form.formatErrors('booking_number')"
            required

        >
            <el-input v-model="form.data.booking_number" :disabled="true"/>
        </el-form-item>


        <template v-if="createMode">
            <el-row :gutter="10">
                <el-col :span="14">
                    <el-form-item
                        :label="$t('Tour')"
                        :error="form.formatErrors('tour_id')"
                        required
                    >
                        <el-select v-model="form.data.tour_id"
                            @change="onChangeTour"
                            :placeholder="$t('Tour')">
                            <el-option v-for="tour in tours"
                                :key="tour.id"
                                :label="tour.name"
                                :value="tour.id"
                            >
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="10">
                    <el-form-item
                        :label="$t('Event time')"
                        :error="form.formatErrors('tour_option_id')"
                    >
                        <el-time-select
                            style="width: 100%"
                            v-model="form.data.start_at"
                            :picker-options="{
                            start: '08:30',
                            step: '00:15',
                            end: '22:30'
                            }"
                            placeholder="Select time">
                        </el-time-select>
                    </el-form-item>
                </el-col>
            </el-row>
        </template>
        <template v-else>
            <el-form-item
                :label="$t('Tour')"
                :error="form.formatErrors('tour_id')"
                required
            >
                <el-select v-model="form.data.tour_id"
                    @change="onChangeTour"
                    :placeholder="$t('Tour')">
                    <el-option v-for="tour in tours"
                        :key="tour.id"
                        :label="tour.name"
                        :value="tour.id"
                    >
                    </el-option>
                </el-select>
            </el-form-item>
        </template>




        <el-form-item :label="$t('Options')"
            :error="form.formatErrors('options_list')">
            <el-checkbox-group v-if="form.data.options_list" v-model="form.data.options_list">
                <div v-for="option in options" :key="option.id" class="mb-20">
                    <el-checkbox
                        v-model="form.data.options_list"
                        :label="option.id">{{ option.name }}</el-checkbox>
                </div>
            </el-checkbox-group>
        </el-form-item>

        <v-divider />

        <el-form-item
            :label="$t('First name')"
            :error="form.formatErrors('first_name')"
            required
        >
            <el-input v-model="form.data.first_name" />
        </el-form-item>

        <el-form-item
            :label="$t('Last name')"
            :error="form.formatErrors('last_name')"
            required
        >
            <el-input v-model="form.data.last_name" />
        </el-form-item>

        <el-form-item
            :label="$t('Phone')"
            :error="form.formatErrors('phone')"
            required
        >
            <el-input v-model="form.data.phone" />
        </el-form-item>

        <el-form-item
            :label="$t('Email')"
            :error="form.formatErrors('email')"
        >
            <el-input v-model="form.data.email" />
        </el-form-item>

        <v-divider />


        <el-form-item :label="$t('Tickets')"
            :error="form.formatErrors('options_list')">


            <el-row v-for="ticket in form.data.tickets_list"
                :key="ticket.id"
                :gutter="20"
                type="flex"
                align="middle"
                class="mb-20"
                style="margin-bottom: 10px;">
                <el-col :span="8">{{ ticket.name }}</el-col>
                <el-col :span="16">
                    <el-input-number
                        :label="ticket.name"
                        v-model="ticket.quantity"
                        size="small"
                        :min="0"
                        :max="getMaxQuantity(ticket)"
                        ></el-input-number>
                </el-col>
            </el-row>
        </el-form-item>

        <v-form-preview :label="$t('Total price')" v-if="!createMode">
                <b>{{ form.data.total_price }} {{form.data.tour.currency}}</b>
        </v-form-preview>

        <v-form-preview
            :label="$t('New price')">
                <b>{{ calculatePrice(form.data, tickets) }} {{form.data.tour.currency}}</b>
        </v-form-preview>

        <v-divider />

         <el-form-item
            :error="form.formatErrors('comment')">
            <el-input
                type="textarea"
                :autosize="{ minRows: 2, maxRows: 4}"
                placeholder="Insert comment"
                v-model="form.data.comment">
            </el-input>
        </el-form-item>

        <slot />
    </el-form>
</template>

<script>

    import uuidV4 from 'uuid/v4';
    import http from '@utils/http';
    import { taskManager } from '@plugins/taskManager';
    import { booking } from '@mixins';

    export default {
        mixins: [ booking ],
        props: {
            form: {
                type: Object,
                required: true
            },
            disabled: {
                type: Boolean,
                default: false
            },
            createMode: {
                type: Boolean,
                default: false
            }
        },
        data: () => ({
            lock: true,
            tickets: [],
            tours: [],
            options: [],
            initialTicketsList: []
        }),

        computed: {
            iconClass() {
                return this.lock ?
                    'fal fa-lock-alt' :
                    'fal fa-unlock-alt';
            }
        },

        methods: {
            getMaxQuantity(ticket) {
                if (this.$can('bookings.create')) {
                    return Infinity;
                }

                let originalItem = this.initialTicketsList.find((item) => {
                    return item.ticket_id == ticket.ticket_id;
                });

                return originalItem.quantity;
            },
            onChangeTour() {
                this.fetchOptions();
                this.fetchTickets();
            },
            fetchTours: async function () {
                let { data:{ data }} = await this.$taskManager.run(
                    `autocomplete@tours.fetch`,
                    http.get(`bookings/tours-autocomplete`)
                );

                this.tours = data.items;
            },

            fetchOptions: async function () {
                let { data:{ data }} = await this.$taskManager.run(
                    `autocomplete@options.fetch`,
                    http.get(`bookings/options-autocomplete/${this.form.data.tour_id}`)
                );

                this.options = data.items;
            },

            fetchTickets: async function () {
                let { data:{ data }} = await this.$taskManager.run(
                    `autocomplete@tickets.fetch`,
                    http.get(`bookings/tickets-autocomplete/${this.form.data.tour_id}`)
                );

                this.form.data.tickets_list = data.items.map((ticket) => {
                    let attachedTicket = this.form.data.booking_tickets.find((element) => {
                        return element.ticket_id == ticket.id;
                    });

                    return {
                        ticket_id: ticket.id,
                        name: ticket.name,
                        quantity: !attachedTicket ? 0 : attachedTicket.quantity,
                        price: parseFloat(ticket.price),
                        is_active: ticket.is_active
                    };
                });

                this.initialTicketsList = this.form.data.tickets_list.map((item) => {
                    return Object.assign({}, item);
                });

                this.tickets = data.items;
            },

            onSubmit() {
                this.$emit('submit', this.$props.form);
            },
            toggle() {
                this.$data.lock = !this.$data.lock;
            }
        },

        beforeMount() {
            this.fetchTours();
            this.fetchOptions();
            this.fetchTickets();

            this.form.data.options_list = [];

            this.form.data.ticket_options.forEach((option) => {
                if (!this.form.data.options_list.includes(option.tour_option_id)) {
                    this.form.data.options_list.push(option.tour_option_id);
                }
            });
        }
    };
</script>
