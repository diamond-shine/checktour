<template>
    <el-dropdown
        class="el-dropdown--tag"
        trigger="click"
        @command="onChange"
    >
        <el-tag
            :size="size"
            :class="colorClass"
        >{{ label }}
        </el-tag>
        <el-dropdown-menu slot="dropdown">
            <el-dropdown-item
                v-for="item in options"
                :key="item.value"
                :command="item.value"
            >{{ item[labelField] }}</el-dropdown-item>
        </el-dropdown-menu>
    </el-dropdown>
</template>

<script>
    import _ from 'lodash';

    export default {
        props: {
            defaultIndex: {
                type: Number,
                default: 0
            },
            options: {
                type: Array,
                required: true
            },
            size: {
                type: String,
                default: 'small'
            },
            placeholder: {
                type: String,
                default: 'Оберіть значення'
            },
            labelField: {
                type: String,
                default: 'label'
            }
        },
        data: () => ({
            label: '',
            color: ''
        }),
        computed: {
            colorClass() {
                if (this.color) {
                    return `el-tag--${this.color}`;
                }
            }
        },
        mounted() {
            this.setActive(this.$props.defaultIndex);
        },
        methods: {
            onChange: function (value) {
                const index = _.findIndex(this.$props.options, ['value', value]);
                this.setActive(index);
            },
            setActive(index) {
                this.$data.label = this.$props.options[index][this.$props.labelField];
                this.$data.color = this.$props.options[index].status;
            }
        }
    };
</script>
