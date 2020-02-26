<template>
    <div :class="getGroupClass">
        <label
            v-if="text"
            :for="id"
            class="form-label"
        >{{ text }}
            <span
                v-if="required"
                class="text-danger">*</span>
        </label>

        <masked-input
            v-if="hasMask"
            ref="maskedInput"
            :id="id"
            :value="value"
            :name="name"
            :type="type"
            :placeholder="placeholder"
            :tabindex="tabIndex"
            :class="{'is-invalid': !!errors}"
            :guide="false"
            :mask="mask"
            class="form-control"
            @input="handleInput"
            @focus="toggleFocus(true)"
            @blur="toggleFocus(false)"
            @mouseover="toggleHover(true)"
            @mouseleave="toggleHover(false)"
        />

        <input
            v-else
            ref="input"
            :id="id"
            :value="value"
            :name="name"
            :type="type"
            :placeholder="placeholder"
            :tabindex="tabIndex"
            :class="{'is-invalid': !!errors}"
            class="form-control"
            @input="handleInput"
            @focus="toggleFocus(true)"
            @blur="toggleFocus(false)"
            @mouseover="toggleHover(true)"
            @mouseleave="toggleHover(false)"
        >

        <div
            v-if="errors"
            class="invalid-feedback"
        >{{ errorsText }}
        </div>
    </div>
</template>

<script>
    import MaskedInput from 'vue-text-mask';

    export default {
        components: {
            MaskedInput
        },
        props: {
            id: {
                type: String,
                required: true
            },
            required: {
                type: Boolean,
                default: false
            },
            type: {
                type: String,
                default: 'text'
            },
            text: {
                type: String,
                default: null
            },
            placeholder: {
                type: String,
                default: null
            },
            name: {
                type: String,
                default: null
            },
            value: {
                type: [ String, Number ],
                default: null
            },
            errors: {
                type: Array,
                default: null
            },
            maskType: {
                type: String,
                default: null
            },
            groupClass: {
                type: [ Object, String ],
                default: null
            },
            tabIndex: {
                type: Number,
                default: 1
            }
        },
        data() {
            return {
                hover: false
            };
        },
        computed: {
            getGroupClass() {
                const { groupClass } = this.$props;

                const classObj = {
                    'form-group': true
                };

                if (this.$lodash.isString(groupClass)) {
                    return this.$lodash.assign({}, classObj, { [ groupClass ]: true });
                } else if (this.$lodash.isObject(groupClass)) {
                    return this.$lodash.assign({}, classObj, groupClass);
                }

                return classObj;
            },
            hasMask() {
                return this.$props.maskType
                    && this.$props.maskType === 'telephone';
            },
            mask() {
                switch (this.$props.maskType) {
                    case 'telephone':
                        return [ '+', '3', '8', ' ', '(', /[0-9]/, /\d/, /\d/, ')', ' ', /\d/, /\d/, /\d/, '-', /\d/, /\d/, '-', /\d/, /\d/, /\d?/ ];

                    default:
                        return [];
                }
            }
        }
    };
</script>
