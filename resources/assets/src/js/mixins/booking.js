export default {
    methods: {
        statusColor(item) {
            if (item.schedule && item.schedule.is_finished) {
                return 'success';
            }

            if (item.schedule_id) {
                return '';
            }

            if (item.arrived_waiting_room) {
                return 'warning';
            }

            return 'info';
        },

        calculatePrice(booking, tickets) {
            if (!tickets.length || !booking.tickets_list || !booking.tickets_list.length) {
                return 0;
            }

            let prices = booking.tickets_list.map((ticket) => {

                let result = ticket.quantity * this.ticketPrice(
                    booking,
                    tickets.find(item => item.id == ticket.ticket_id)
                );

                return result;
            });


            return prices.reduce((totalPrice, itemPrice) => {
                return totalPrice + itemPrice;
            });
        },

        ticketPrice(booking, ticket) {

            if (!ticket.is_active) {
                return 0;
            }

            let optionsPrice = this.getAllowedTicketOptionsPrice(booking, ticket.ticket_options);

            return optionsPrice + parseFloat(ticket.price);
        },

        getAllowedTicketOptionsPrice(booking, ticketOptions) {

            if (!ticketOptions.length) {
                return 0;
            }

            let pricesList = []

            pricesList = ticketOptions.map((item) => {
                if (!item.tour_option || !item.tour_option.is_active) {
                    return 0;
                }

                if (!booking.options_list.includes(item.tour_option.id)) {
                    return 0;
                }

                return parseFloat(item.price);
            });

            let result = pricesList.reduce((totalPrice, itemPrice) => {
                return totalPrice + itemPrice;
            });

            return result;
        }
    }
}
