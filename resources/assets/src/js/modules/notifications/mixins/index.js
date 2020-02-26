import { mapActions } from 'vuex';
export default {
    data() {
        return {
            notifications: [],
            showed: [],
            duration: 60000 * 15,
            notyList: []
        }
    },
    methods: {
        onClick(notification) {
            if (notification.entity_type == 'App\\Models\\Schedule') {
                this.$router.push({
                    name: 'rosters.view',
                    params: {
                        rosterId: notification.entity_id
                    }
                });
            }

            if (notification.entity_type == 'App\\Models\\Booking') {
                this.$router.push({
                    name: 'rostered.view',
                    params: {
                        bookingId: notification.entity_id
                    }
                });
            }
        },
        onClose(notification) {
            this.destroyNotification(notification);
        },
        getTitle(notification) {
            let title;

            switch(notification.entity_type) {
                case 'App\\Models\\Schedule':
                    title = 'Roster updated';
                break;

                case 'App\\Models\\TourOption':
                    title = 'Tour option updated';
                break;
            }

            return title;
        },
        isShowed(notification) {
            if (document.notificationsShowed) {
                return document.notificationsShowed.includes(notification.id);
            }

            return false;
        },
        pushToShowed(notification) {
            if (document.notificationsShowed) {
                document.notificationsShowed.push(notification.id);
            } else {
                document.notificationsShowed = [notification.id];
            }
        },
        showNotifications() {
            this.notifications.forEach((notification) => {

                if (this.isShowed(notification)) {
                    return;
                }

                let noty = this.$notify.info({
                    title: this.getTitle(notification),
                    message: notification.message,
                    duration: this.duration,
                    onClick: () => {
                        noty.close();
                        this.onClick(notification);
                    },
                    onClose: () => {
                        this.onClose(notification);
                    }
                });

                this.pushToShowed(notification);
            })

        },
        ...mapActions('notifications', {
            async retreiveNotifications(dispatch) {

                const data = await dispatch('list');

                this.notifications = data.items;
                this.showNotifications()
            },
            async destroyNotification(dispatch, notification) {

                await dispatch('destroy', {
                    notificationId: notification.id
                });
            },
        })
    }
}
