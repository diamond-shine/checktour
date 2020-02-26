<template>
    <v-content-list
        :items="items"
        :pagination="pagination"
        class="col-inner"
        @change-page="changePage"
    >
        <template slot-scope="{ item }">
            <v-list-item
                :key="item.id"
                :data="item"
                :route="{
                    name: viewPageRoute,
                    params: {
                        bookingId: item.id
                    }
                }"
                :title-by="clientName"
                :description-by="shortInfo"
            >
                <template #aside>
                    <div class="c-list__aside-item">
                        <div class="pt-10">
                            <el-button
                                type="danger"
                                native-type="submit"
                                v-on:click.prevent="unassign(item)"
                            >{{ $t('Unassign') }}
                            </el-button>
                        </div>
                    </div>
                </template>
            </v-list-item>
        </template>
    </v-content-list>
</template>

<script>
import { booking, styles } from '@mixins';

export default {
    mixins: [ booking, styles ],
    props: {
        items: {
            required: true,
            type: Array
        },
        changePage: {
            type: Function
        },
        unassign: {
            required:false,
            type: Function
        },
        pagination: {
            required: true,
            type: Object
        },
        viewPageRoute: {
            required: true,
            type: String
        }
    },
    methods: {
        clientName(booking) {
            return `${booking.first_name} ${booking.last_name}`
        },
        shortInfo(booking) {
            return booking.start_at +' / ' + booking.tickets_count + ' ' + this.$t('tickets');
        }
    }
}

</script>