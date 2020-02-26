<script>
    import Vue from 'vue';
    import Popper from 'popper.js';

    export default {
        props: {
            visible: {
                required: true,
                type: Boolean
            },
            placement: {
                type: String,
                default: 'bottom'
            },
            width: {
                type: Number,
                default: 160
            }
        },
        data: () => ({
            status: false,
            popperContent: null,
            popperInstance: null
        }),
        watch: {
            visible: {
                handler(value) {
                    if (value) {
                        this.show();
                    } else {
                        this.hide();
                    }
                },
                immediate: true
            }
        },
        beforeDestroy() {
            this.hide();
        },
        methods: {
            show() {
                if (this.$data.popperInstance) {
                    return;
                }

                const element = document.createElement('div');

                element.setAttribute('id', `vue-popper-${this._uid}`);

                document.body.appendChild(element);

                this.$data.popperContent = new Vue({
                    render: h => h('el-popover', {
                        props: {
                            value: this.$props.visible,
                            width: this.$props.width
                        }
                    }, [
                        this.$slots.content
                    ])
                }).$mount(element);

                const arrow = document.createElement('div');
                arrow.setAttribute('x-arrow', '');
                arrow.className = 'popper__arrow';

                this.$data.popperContent.$el.firstElementChild.appendChild(arrow);

                this.$data.popperInstance = new Popper(this.$el, this.$data.popperContent.$el.firstElementChild, {
                    placement: this.$props.placement
                });
            },

            hide() {
                if (this.$data.popperContent) {
                    this.$data.popperContent.$destroy();
                    this.$data.popperContent.$el.parentNode.removeChild(
                        this.$data.popperContent.$el
                    );
                    this.$data.popperContent = null;
                }

                if (this.$data.popperInstance) {
                    this.$data.popperInstance.destroy();

                    this.$data.popperInstance = null;
                }
            }
        },
        render() {
            if (!this.$lodash.has(this.$slots.default, '0')) {
                return;
            }

            return this.$slots.default[ 0 ];
        }
    };
</script>
