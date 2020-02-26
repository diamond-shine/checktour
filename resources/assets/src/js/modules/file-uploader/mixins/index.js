export const preload = {
    data: () => ({
        imageIsLoaded: false,
        imageHasError: false
    }),
    computed: {
        imageErrorIcon() {
            return this.$helpers.icon('image-error');
        }
    },
    methods: {
        onMarkImageAsLoaded() {
            this.$data.imageIsLoaded = true;
        },

        onMarkImageAsError() {
            this.$data.imageHasError = true;
        }
    }
};
