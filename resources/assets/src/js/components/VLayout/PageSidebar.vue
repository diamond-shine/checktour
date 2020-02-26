<template>
    <nav class="o-layout__nav c-nav">
        <v-scroll>
            <div class="c-nav__header">
                <div
                    class="c-nav__user"
                    aria-haspopup="list"
                    role="button"
                >
                    <div
                        v-if="user.avatar"
                        class="c-nav__user-avatar"
                    >
                        <img
                            :src="user.avatar"
                            :alt="user.full_name"
                        >
                    </div>

                    <div class="c-nav__user-main">
                        <div class="c-nav__user-name">
                            {{ user.full_name }}
                        </div>

                        <div
                            v-if="user.role"
                            class="c-nav__user-role"
                        >
                            {{ user.role.title }}
                        </div>
                    </div>

                    <el-dropdown
                        trigger="click"
                        placement="bottom"
                        @command="dropdown"
                    >
                        <i class="icon c-icon-arrow" />

                        <el-dropdown-menu
                            slot="dropdown"
                        >
                            <el-dropdown-item command="profile">{{ $t('Profile') }}</el-dropdown-item>
                            <el-dropdown-item command="logout">{{ $t('Logout') }}</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </div>
            </div>

            <div class="c-nav__main">
                <v-menu />
            </div>

            <div class="c-nav__footer">
                <div class="c-nav__footer-item">
                    <button
                        :aria-label="$t('Відображення бокового меню')"
                        type="button"
                        class="c-nav__footer-link"
                        @click="toggle()"
                    >
                        <span
                            v-if="show_sidebar"
                            class="c-icon-minimise"
                            aria-hidden="true"
                        />

                        <span
                            v-else
                            class="c-icon-maximise"
                            aria-hidden="true"
                        />
                    </button>
                </div>

                <div class="c-nav__footer-item c-nav__footer-item--grow">
                    <a
                        :href="site_url"
                        target="_blank"
                        class="c-nav__footer-link"
                    >
                        <span class="u-fz-xs">{{ $t('Go to the bookeo') }}</span>
                    </a>
                </div>

                <div class="c-nav__footer-item">
                    <button
                        :aria-label="$t('Змінити тему')"
                        type="button"
                        class="c-nav__footer-link"
                        @click="switchTheme"
                    >
                        <span
                            class="c-icon-design"
                            aria-hidden="true"
                        />
                    </button>
                </div>
            </div>
        </v-scroll>

        <footer class="o-layout__footer d-lg-none d-xl-none">
            <!-- &copy; IDEIL 2020 -->
        </footer>
    </nav>
</template>

<script>
    import { mapState, mapActions } from 'vuex';
    import http from '@utils/http';

    export default {
        computed: {
            ...mapState('system', [
                'sidebar',
                'site_url',
                'current_site',
                'sites',
                'user',
                'show_sidebar',
                'theme'
            ])
        },
        beforeMount() {
            this.applyTheme();
        },
        methods: {
            dropdown(command) {
                if (command === 'logout') {
                    http.post('logout');
                }

                if (command === 'profile') {
                    this.$router.push({
                        name: 'users.edit',
                        params: {
                            userId: this.user.id
                        }
                    })
                }
            },

            applyTheme() {
                const body = document.querySelector('body');

                if (this.theme === 'dark') {
                    body.classList.remove('t-light');
                    body.classList.add('t-dark');
                } else {
                    body.classList.remove('t-dark');
                    body.classList.add('t-light');
                }
            },

            ...mapActions('system', {
                toggle: 'toggleSidebar',

                async changeSite(dispatch, site) {
                    await dispatch('changeSite', site);

                    this.$cookies.set('site_id', site, 0);

                    window.location.reload();
                },
                async switchTheme(dispatch) {
                    await dispatch(
                        'switchTheme',
                        this.theme === 'dark' ? 'light' : 'dark'
                    );

                    this.applyTheme();
                }
            })
        }
    };
</script>
