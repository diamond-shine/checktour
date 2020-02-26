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
                description-by="tour.name"
            >
                <template #aside>
                    <!-- <div class="c-list__aside-item">
                        <div class="c-list__aside-text">{{ item.start_at }}</div>
                    </div> -->

                    <div class="c-list__aside-item">
                        <div class="pt-10">
                            <el-tag v-if="isGuide"
                                :type="statusColor(item)"
                                size="small">{{ item.tickets_count }} {{ $t('tickets') }}
                            </el-tag>
                            <el-tag v-else
                                :type="statusColor(item)"
                                size="small">{{ item.total_price }} {{ item.currency}} | {{ item.tickets_count }} {{ $t('tickets') }}
                            </el-tag>
                        </div>

                        <div class="pt-10 u-text-right">{{ item.start_at }}</div>
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
    }
}

</script>