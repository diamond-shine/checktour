<template>
    <main
        :class="{ 'is-sidebar': showFloatingSidebar }"
        class="o-layout__main"
    >
        <transition name="shadow">
            <div
                v-if="showFloatingSidebar"
                class="o-layout__shadow"
                aria-hidden="true"
                @click="handleClose"
            />
        </transition>

        <div
            :class="{
                'o-layout__content--locked': locked
            }"
            class="o-layout__content row"
        >
            <slot :locked="locked" />
        </div>

        <transition name="sidebar">
            <aside
                v-loading="floating_sidebar_status === 'loading'"
                v-if="showFloatingSidebar"
                :class="sizeClass"
                class="o-layout__sidebar c-sidebar"
            >
                <transition
                    :name="transitionName"
                >
                    <router-view />
                </transition>
            </aside>
        </transition>
    </main>
</template>

<script>
    import { mapState } from 'vuex';

    export default {
        props: {
            locked: {
                type: Boolean,
                default: false
            }
        },
        data: () => ({
            transitionName: 'sidebar-opacity'
        }),
        computed: {
            sizeClass() {
                const size = this.$lodash.get(this.$route.meta, 'sidebar.size');

                return size ? `o-layout__sidebar--${size}` : '';
            },

            showFloatingSidebar() {
                const status = this.floating_sidebar_status !== 'closed';

                if (status) {
                    document.documentElement.classList.add('is-overflow');
                } else {
                    document.documentElement.classList.remove('is-overflow');
                }

                return status;
            },

            ...mapState('system', [ 'floating_sidebar_status' ])
        },
        watch: {
            '$route'(to, from) {
                const toDepth = to.path.split('/').length;
                const fromDepth = from.path.split('/').length;

                if (toDepth === fromDepth || toDepth > 4 || fromDepth > 4) {
                    if (this.$lodash.includes(from.name.split('.'), 'view')) {
                        this.transitionName = 'sidebar-right';
                    } else {
                        this.transitionName = 'sidebar-left';
                    }
                } else {
                    this.transitionName = 'sidebar-opacity';
                }
            }
        },
        methods: {
            handleClose(e) {
                this.$emit('close', e);
            }
        }
    };
</script>
