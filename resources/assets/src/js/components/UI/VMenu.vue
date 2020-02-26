<template>
    <ul
        role="menubar"
        class="c-nav__menu el-menu"
    >
        <li
            v-for="level1 in sidebar.children"
            :key="level1.key"
            class="el-menu-item-group"
        >
            <div
                v-if="level1.name !== 'top'"
                class="el-menu-item-group__title"
            >{{ level1.name }}</div>

            <ul>
                <template v-for="level2 in level1.children">
                    <li
                        v-if="!level2.children.length"
                        :key="level2.key"
                        role="menuitem"
                        tabindex="-1"
                        class="el-menu-item"
                    >
                        <router-link
                            v-if="level2.props.route.name != 'disabled'"
                            :to="level2.props.route || {}"
                            class="el-menu-item-link"
                            @click.native="toggleSidebarMobile(false)"
                        >
                            <i
                                v-if="level2.props.icon"
                                :class="level2.props.icon"
                                class="icon"
                            />
                            <span class="title">{{ level2.name }}</span>
                        </router-link>
                        <span
                            v-else

                            class="el-menu-item-link"
                        >
                            <i
                                v-if="level2.props.icon"
                                :class="level2.props.icon"
                                class="icon"
                            />
                            <span class="title">{{ level2.name }}</span>
                        </span>
                    </li>

                    <li
                        v-else
                        :key="level2.key"
                        :ref="level2.key"
                        role="menuitem"
                        aria-haspopup="true"
                        tabindex="-1"
                        class="el-submenu"
                    >
                        <div
                            class="el-submenu__title"
                            @click="toggle(level2.key)"
                        >
                            <span class="title">{{ level2.name }}</span>
                            <i class="el-submenu__icon-arrow el-icon-arrow-down"/>
                        </div>

                        <ul
                            role="menu"
                            class="el-menu"
                        >
                            <li
                                v-for="level3 in level2.children"
                                :key="level3.key"
                                role="menuitem"
                                tabindex="-1"
                                class="el-menu-item"
                            >
                                <router-link
                                    :to="level3.props.route || {}"
                                    class="el-menu-item-link"
                                >
                                    <i
                                        v-if="level3.props.icon"
                                        :class="level3.props.icon"
                                        class="icon"
                                    />
                                    <span class="title">{{ level3.name }}</span>
                                </router-link>
                            </li>
                        </ul>
                    </li>
                </template>
            </ul>
        </li>
    </ul>
</template>

<script>
    import { mapState, mapActions } from 'vuex';

    export default {
        computed: {
            ...mapState('system', [
                'sidebar'
            ])
        },
        methods: {
            ...mapActions('system', ['toggleSidebarMobile']),
            toggle(el) {
                const parent = this.$refs[el][0];

                if (parent.classList.contains('is-opened')) {
                    parent.classList.remove('is-opened');
                    parent.removeAttribute('aria-expanded');

                } else {
                    parent.classList.add('is-opened');
                    parent.setAttribute('aria-expanded', true);
                }
            }
        }
    };
</script>
