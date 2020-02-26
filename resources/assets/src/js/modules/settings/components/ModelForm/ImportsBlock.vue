<template>
    <v-panel
        :title="$t('Booking import')"
        :description="$t('Manual booking import')"
        annotated
    >
        <v-panel>
            <div class="c-user-roles">
                <v-form
                    ref="form"
                    :data="formData"
                    #default="{ form, submit }"
                    name="import"
                    @submit="update"
                >
                    <el-row>
                        <el-col :span="12">
                            <label class="el-form-item__label">{{ $t('Days ahead') }}</label>
                            <el-input-number
                                :label="$t('Days ahead')"
                                v-model="form.data.days"
                                size="small"
                                :min="1"
                                :max="10"
                                ></el-input-number>
                        </el-col>
                        <el-col :span="12" class="u-text-right">
                            <el-button
                                size="small"
                                class="mt-40"
                                type="success"
                                @click="submit"
                                >{{ $t('Import bookings') }}</el-button>
                        </el-col>
                    </el-row>
                </v-form>
                <v-divider />
                <el-table
                    :data="importList"
                    style="width: 100%">
                    <el-table-column
                        prop="created_at"
                        label="Date"
                        width="180">
                    </el-table-column>
                    <el-table-column
                        prop="created"
                        label="Created"
                        width="80">
                    </el-table-column>
                    <el-table-column
                        prop="updated"
                        label="Updated"
                        width="80">
                    </el-table-column>
                    <el-table-column
                        prop="status"
                        label="status">
                    </el-table-column>
                    <el-table-column
                        prop="initiator"
                        label="Initiator">
                    </el-table-column>

                </el-table>

            </div>

        </v-panel>
    </v-panel>
</template>

<script>
    import { mapActions } from 'vuex';
    import store from '@store';

    export default {
        data: () => ({
            formData: {days: 1},
            importList: []
        }),
        beforeMount() {
            this.fetchImportsList();
        },
        destroyed() {

        },
        methods: {
            initiator({row, rowIndex}) {
                if (row.user) {
                    return 'importList.user';
                }

                return 'type';
            },
            async fetchImportsList() {
                const { items } = await store.dispatch('settings/importsList');
                this.importList = items.map((item) => {
                    item.initiator = (item.user) ? item.user.full_name : item.type;
                    return item;
                });
            },
            ...mapActions('settings', {
                async update(dispatch, $form) {

                    await dispatch('makeImport', { form: $form });

                    this.fetchImportsList();
                }
            })
        }
    };
</script>
