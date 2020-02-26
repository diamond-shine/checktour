export default {
    data: () => ({
        labelKey: 'label',
        filterQuery: '',
        sorting: false,
        items: []
    }),
    computed: {
        allIds() {
            const findId = (item, state = []) => {
                state.push(item.id);

                if (item.children.length) {
                    state = state.concat(
                        this.$lodash.reduce(
                            item.children,
                            (state, item) => {
                                return state.concat(
                                    findId(item)
                                );
                            },
                            []
                        )
                    );
                }

                return state;
            };

            return this.$lodash.reduce(
                this.processedItems,
                (state, item) => {
                    return state.concat(
                        findId(item)
                    );
                },
                []
            );
        },

        expanded() {
            if (this.$data.sorting) {
                return this.allIds;
            }

            return this.filteredItemIds.expanded;
        },
        term() {
            return this.$data.filterQuery.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
        },
        termRegExp() {
            if (!this.term) {
                return null;
            }

            return new RegExp(`(${this.term})`, 'gi');
        },
        processedItems() {
            const highlight = item => {
                if (this.filteredItemIds.hide.indexOf(item.id) !== -1) {
                    return null;
                }

                let label = item[this.$data.labelKey];

                if (item[this.$data.labelKey]
                    && this.termRegExp !== null
                    && this.termRegExp.test(item[this.$data.labelKey])
                ) {
                    label = item[this.$data.labelKey]?.replace(
                        this.termRegExp,
                        '<span style="background-color: yellow;">$1</span>'
                    );
                }

                let data = {
                    ...item,
                    label,
                    children: item
                        .children
                        .map(
                            child => highlight(child)
                        )
                        .filter(value => value)
                };

                data[this.$data.labelKey] = label;

                return data;
            };

            return this
                .$data
                .items
                .map(
                    item => highlight(item)
                )
                .filter(value => value);
        },
        filteredItemIds() {
            const expanded = [];
            const hide = [];
            const matched = [];

            if (!this.term) {
                return {
                    expanded,
                    hide
                };
            }

            const expand = item => {
                const match = this.termRegExp !== null ?
                    this.termRegExp.test(item[this.$data.labelKey]) :
                    false;

                if (match) {
                    matched.push(item.id);
                }

                let hasExpandedChildren = false;

                if (item.children.length) {
                    hasExpandedChildren = item
                        .children
                        .map(expand)
                        .some(value => value);
                }

                if (match && hasExpandedChildren) {
                    expanded.push(item.id);
                } else if (match
                    && item.parent_id
                    && !hasExpandedChildren
                ) {
                    expanded.push(item.parent_id);
                }

                if (!match
                    && !hasExpandedChildren
                    && matched.indexOf(item.parent_id) === -1
                ) {
                    hide.push(item.id);
                }

                return match || hasExpandedChildren;
            };

            this.$data.items.forEach(expand);

            return {
                expanded,
                hide: this.$lodash.uniq(hide)
            };
        }
    }
};
