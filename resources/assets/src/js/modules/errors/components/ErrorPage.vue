<template>
    <v-layout>
        <template slot="content">
            <main class="o-layout__main">
                <div class="o-layout__content row">
                    <div class="c-aside-icon">
                        <div class="c-aside-icon__inner"><i :class="`icon ${data.icon}`" />
                            <div class="title">{{ data.message }}</div>
                        </div>
                    </div>
                </div>
            </main>
        </template>
    </v-layout>
</template>

<script>
    export default {
        data() {
            return {
                '403': {
                    icon: 'c-icon-lock',
                    message: this.$t('You do not have permissions to view this page')
                },
                '404': {
                    icon: 'fal fa-frog',
                    message: this.$t('Nothing found at this link')
                },
                '500': {
                    icon: 'fal fa-exclamation',
                    message: this.$t('Server error')
                }
            };
        },
        computed: {
            httpErrorCode() {
                return this.$routeParam('httpCode');
            },

            data() {
                if (!this.$lodash.has(this.$data, this.httpErrorCode)) {
                    throw new Error('Unexpected error code');
                }

                return this.$data[this.httpErrorCode];
            }
        }
    };
</script>
