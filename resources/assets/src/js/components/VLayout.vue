<template>
    <div
        :class="{ 'is-nav-hide': !show_sidebar, 'is-nav-mobile': show_sidebar_mobile }"
        class="o-layout"
    >
        <transition name="fade">
            <span
                v-if="show_sidebar_mobile"
                class="o-layout__nav-bg"
                @click="toggleSidebarMobile()"
            />
        </transition>

        <page-sidebar />

        <div class="o-layout__body">
            <header class="o-layout__header">
                <div class="o-layout__header-inner">
                    <button
                        type="button"
                        class="c-burger"
                        @click="toggleSidebarMobile()"
                    >
                        <i class="fal fa-bars" />
                    </button>

                    <div class="o-layout__header-breadcrumbs">
                        <page-breadcrumbs
                            v-if="breadcrumbs"
                            :breadcrumbs="breadcrumbs"
                        />
                    </div>
                </div>

                <slot
                    :locked="locked"
                    name="actions"
                />
            </header>

            <page-content
                v-loading="locked"
                :class="contentClass"
                :locked="locked"
                @close="onCloseSidebar"
            >
                <slot
                    v-if="!locked"
                    :locked="locked"
                    name="content"
                />

                <template v-slot:sidebar>
                    <slot name="page-sidebar" />
                </template>
            </page-content>

            <el-footer
                class="o-layout__footer d-lg-none d-xl-none"
                style="border-top: 0">

                <div
                    class="u-border-top d-flex"
                    style="z-index: 10; margin-left: -20px; background-color: var(--nav-bg);position: fixed; bottom: 0px; height: 60px; width: 100%; font-size: 2.15rem;">

                        <router-link
                            style="flex: 1 0 20%"
                            class="d-flex justify-content-center align-items-center flex-grow-1"
                            :active-class="'bottom-link'"
                            :to="{name: 'rosters.list'}"
                            tag="div"
                        >
                            <i class="icon far fa-user-edit"></i>
                        </router-link>

                        <router-link
                            style="flex: 1 0 20%"
                            class="d-flex justify-content-center align-items-center flex-grow-1"
                            :active-class="'bottom-link'"
                            :to="{name: 'waiting-room.list'}"
                            tag="div"
                        >
                            <i class="icon far fa-hourglass"></i>
                        </router-link>

                        <router-link
                            style="flex: 1 0 20%"
                            class="d-flex justify-content-center align-items-center flex-grow-1"
                            :active-class="'bottom-link'"
                            :to="{name: 'forecasting.list'}"
                            tag="div"
                        >
                            <i class="icon far fa-file-medical-alt"></i>
                        </router-link>
                </div>
                <!-- &copy; IDEIL 2020 -->
            </el-footer>
        </div>

        <image-shadow-box />
    </div>

</template>

<script>
    import { Breadcrumbs } from '@plugins/breadcrumbs';
    import { mapActions, mapState } from 'vuex';
    import { styles } from '@mixins';

    import PageBreadcrumbs from './VLayout/PageBreadcrumbs';
    import PageSidebar from './VLayout/PageSidebar';
    import PageContent from './VLayout/PageContent';

    import ImageShadowBox from '../modules/file-uploader/components/ShadowBox';

    import notifications from '../modules/notifications/mixins/index';

    export default {
        mixins: [ notifications, styles ],
        components: {
            PageSidebar,
            PageBreadcrumbs,
            PageContent,
            ImageShadowBox
        },
        data() {
            return {
                notificationsInterval: null
            }
        },
        props: {
            breadcrumbs: {
                type: Breadcrumbs,
                default: undefined
            },
            locked: {
                type: Boolean,
                default: false
            },
            contentClass: {
                type: [ Array, Object, String ],
                default: ''
            }
        },
        computed: {
            ...mapState('system', [ 'show_sidebar', 'show_sidebar_mobile', 'user' ])
        },
        beforeMount() {
            this.setGuideMobileFontBase();
        },
        mounted() {
            this.markAsInitialized();
            this.retreiveNotifications();

            if (document.notificationsInterval) {
                clearInterval(document.notificationsInterval);
            }

            document.notificationsInterval = setInterval(() => {
                this.retreiveNotifications();
            }, 30000);
        },
        methods: {
            ...mapActions('system', ['markAsInitialized', 'toggleSidebarMobile']),

            onCloseSidebar() {
                this.$emit('close-sidebar');
            }
        }
    };
</script>
