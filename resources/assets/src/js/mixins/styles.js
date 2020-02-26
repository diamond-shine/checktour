
import { mapState } from 'vuex';

export default {
    computed: {
        ...mapState('system', [
            'user'
        ]),
        noLabels() {
            return this.isMobile && this.isGuide;
        },
        isMobile() {
            if( navigator.userAgent.match(/Android/i)
                 || navigator.userAgent.match(/webOS/i)
                 || navigator.userAgent.match(/iPhone/i)
                 || navigator.userAgent.match(/iPad/i)
                 || navigator.userAgent.match(/iPod/i)
                 || navigator.userAgent.match(/BlackBerry/i)
                 || navigator.userAgent.match(/Windows Phone/i)) {
                return true;
            }

            return false;

        },

        isGuide() {
            return this.user.is_guide;
        },



        guideMobileFontBase() {

        },
        guideMobileFontLg() {
            return this.isMobile && this.isGuide ? 'guide-mobile-font-lg' : '';
        },
        guideMobileFontMd() {
            return this.isMobile && this.isGuide ? 'guide-mobile-font-md' : '';
        },
        guideMobileFontSm() {
            return this.isMobile && this.isGuide ? 'guide-mobile-font-sm' : '';
        },
        guideMobileFontXs() {
            return this.isMobile && this.isGuide ? 'guide-mobile-font-xs' : '';
        }
    },
    methods: {
        setGuideMobileFontBase() {
            if (this.isMobile && this.isGuide) {
                document.querySelector('body').classList.add("guide-mobile-font-base");
            }
        }
    }
}
