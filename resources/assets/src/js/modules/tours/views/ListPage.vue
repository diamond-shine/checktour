<template>
    <v-layout
        :locked="locked"
        :breadcrumbs="breadcrumbs"
        @close-sidebar="close"
    >
        <template #actions>
            <div class="o-layout__header-actions">
                <div
                    v-if="$can('tours.create')"
                    class="o-layout__header-group"
                >
                    <el-button
                        size="small"
                        type="primary"
                        @click="onClickCreate"
                    >{{ $t('Create tour') }}
                    </el-button>
                </div>
            </div>
        </template>
        <template #content="{ locked }">
            <v-scroll
                :locked="locked"
                class="col-xl-6"
            >
                <div class="col-12">
                    <div class="pt-20">
                        <v-search-panel
                            v-model="$filters('tours').data.term"

                            @search="list"
                        />
                    </div>
                </div>

                <v-content-list

                    :items="items"
                    :pagination="pagination"
                    class="col-inner"
                    @change-page="onChangePage"
                >
                    <template slot-scope="{ item }">
                        <v-list-item
                            :key="item.id"
                            :data="item"
                            :route="{
                                name: 'tours.view',
                                params: {
                                    tourId: item.id
                                }
                            }"
                            title-by="name"
                            :description-by="formatIdentifiers"
                        >
                            <template #aside>
                                <div class="c-list__aside-item">
                                    <div class="c-list__aside-text">{{ $t('Users') }}:</div>
                                </div>
                                <div class="c-list__aside-item">
                                    <el-tag size="small">{{ item.users_count }}</el-tag>
                                </div>

                                <div class="c-list__aside-item">
                                    <div class="c-list__aside-text">{{ $t('Options') }}:</div>
                                </div>
                                <div class="c-list__aside-item">
                                    <el-tag size="small">{{ item.tour_options_count }}</el-tag>
                                </div>
                            </template>
                        </v-list-item>

                    </template>
                </v-content-list>
            </v-scroll>
            <div class="col-xl-6 d-none d-xl-flex u-border-left">
                <div class="c-aside-icon">
                    <div class="c-aside-icon__inner">
                        <i class="icon fal fa-landmark" />
                        <div class="title">{{ $t('Tours') }}</div>
                    </div>
                </div>
            </div>
        </template>
    </v-layout>
</template>


<script>

import { mapActions } from 'vuex';
import store from '@store';
import filters from '@utils/filters';
import { page } from '@mixins';
import { mapStatuses } from '@plugins/taskManager';

import breadcrumbs from '../breadcrumbs';

export default {
    mixins: [ page ],
    computed: {
        breadcrumbs: breadcrumbs.make('tours.list'),
        ...mapStatuses([
            {
                name: 'list.fetch',
                process: 'tours@list.fetch'
            }
        ])
    },
    data() {
        return {
            items: [],
            pagination: null
        }
    },
    created() {
        this.$listenCycle('tours@list.fetch', this.list);
    },
    beforeRouteEnter: (to, from, next) => next(async vm => {
        await vm.$filters('tours').restoreFromUrl(to.name);
        vm.lock(
            vm.list()
        );
    }),
    methods: {
        formatIdentifiers(item) {

            if (!item.bookeo_id) {
                return '';
            }

            if (Array.isArray(item.bookeo_id)) {
                return item.bookeo_id.join('|');
            }

            return item.bookeo_id;
        },
        onClickCreate() {
            this.$router.push({
                name: 'tours.create'
            });
        },
        fill(data) {
            this.items = data.items;
            this.pagination = data.pagination;
        },
        close() {
            this.$router.push({
                name: 'tours.list'
            });
        },
        onChangePage(page) {
            this.$filters('tours').data.page = page;

            this.list();
        },
        ...mapActions('tours', {
            async list(dispatch) {
                const data = await dispatch('list');

                this.fill(data);
            }
        })
    }
}

</script>