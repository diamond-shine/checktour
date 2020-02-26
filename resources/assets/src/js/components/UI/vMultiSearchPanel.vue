<template>
    <div>
    <el-row :gutter="20" style="margin-bottom: 10px;">
        <el-col :span="24">
            <div class="el-input el-input-group el-input--suffix">
                <el-select v-model="tourId" class="border-right-radius"
                    :placeholder="currentPlaceholder"
                    >
                    <el-option v-for="item in selectItems"
                        :key="'search_' + item.id"
                        :label="item.name"
                        :value="item.id"
                    />
                </el-select>

                <div v-if="isFiltersSetted && allowClearing" class="el-input-group__append">
                    <button

                        style="border-right: 1px solid var(--input-border-color); border-radius: 0px;"
                        class="el-button el-button--default"
                        @click="onClear"
                    >
                        <i class="icon far fa-times"></i>
                    </button>
                </div>

                <div class="el-input-group__append">
                    <button
                        style="border-right: 1px solid var(--input-border-color); border-radius: 0px;"
                        class="el-button el-button--default"
                        @click="toggleFilters"
                    >
                        <i v-if="showAllFilters" class="el-icon-minus"></i>
                        <i v-else class="el-icon-plus"></i>

                    </button>
                </div>



            </div>
        </el-col>
    </el-row>
    <el-row
        v-if="showAllFilters"
        :gutter="20" style="margin-bottom: 10px;"
    >
        <el-col :span="12">
            <el-time-select
                placeholder="Start time"
                style="width: 100%"
                v-model="startTime"
                :picker-options="{
                  start: '08:30',
                  step: '00:30',
                  end: '22:30'
                }">
            </el-time-select>
        </el-col>
        <el-col :span="12">
             <el-time-select
                placeholder="End time"
                style="width: 100%"
                v-model="endTime"
                align="left"
                :picker-options="{
                  start: '08:30',
                  step: '00:30',
                  end: '22:30',
                  minTime: startTime
                }">
            </el-time-select>
        </el-col>
    </el-row>
    <el-row v-if="showAllFilters"
        :gutter="20"
    >
        <el-col :span="24">

            <input
                v-model="term"
                :placeholder="$t('Search…')"
                type="text"
                autocomplete="off"
                class="el-input__inner"
                @keypress.enter="onSearch"
            >

        </el-col>
    </el-row>
    </div>
</template>

<script>
    import { mapActions } from 'vuex';
    import { mapStatuses } from '@plugins/taskManager';
    import uuidV4 from 'uuid/v4';

    export default {
        props: {
            value: {
                type: Object
            },
            items: {
                type: Array
            },
            placeholder: {
                type: String
            },
            fetchUrl: String,
            allowClearing: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                showAllFilters: false,
                selectItems: []
            }
        },
        computed: {
            isFiltersSetted: {
                get() {
                    if (this.tourId || this.term || this.startTime || this.endTime) {
                        return true;
                    }

                    return false;
                }
            },
            startTime: {
                get() {
                    if (!this.$props.value.start_time) {
                        return null;
                    }

                    return this.$props.value.start_time;
                },

                set(value) {
                    this.$props.value.start_time = value;
                    this.$emit('search');
                }
            },
            endTime: {
                get() {
                    if (!this.$props.value.end_time) {
                        return null;
                    }

                    return this.$props.value.end_time;
                },

                set(value) {
                    this.$props.value.end_time = value;
                    this.$emit('search');
                }
            },
            tourId: {
                get() {
                    if (!this.$props.value.tour_id) {
                        return null;
                    }

                    return parseInt(this.$props.value.tour_id);
                },

                set(value) {
                    this.$props.value.tour_id = value;
                    this.$emit('search');
                }
            },
            term: {
                get() {
                    if (!this.$props.value.term) {
                        return '';
                    }

                    return this.$props.value.term;
                },

                set(value) {
                    this.$props.value.term = value;
                    this.$emit('search');
                }
            },
            currentPlaceholder() {
                if (this.placeholder) {
                    return this.placeholder;
                }
                return this.$t('Select');
            }
        },
        methods: {
            toggleFilters() {
                this.showAllFilters = !this.showAllFilters;
            },
            onSearch() {
                this.$emit('search');
            },

            onClear() {
                this.tourId = null;
                this.term = null;
                this.startTime = null;
                this.endTime = null;

                this.$emit('search');
            },

            ...mapActions('autocomplete', {
                fetch: async function (dispatch, params = {}) {

                    if (this.$props.disabled) {
                        return;
                    }

                    let id = uuidV4();
                    let url = this.$props.fetchUrl;

                    const { items } = await this.$taskManager.run(
                        `autocomplete@${id}.fetch`,
                        dispatch('fetch', { id, url, params })
                    );

                    this.selectItems = items;
                }
            })
        },
        mounted() {
            this.fetch();
        }
    };
</script>
