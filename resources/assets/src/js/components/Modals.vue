<template>
    <div>
        <v-confirm
            :data="confirm"
            @close="closeConfirm"
        />
    </div>
</template>

<script>
    import { mapState, mapActions } from 'vuex';

    export default {
        computed: {
            ...mapState('system', [ 'confirm' ])
        },
        methods: {
            ...mapActions('system', {
                closeConfirm(dispatch) {
                    return dispatch('confirm', null);
                }
            })
        }
    };
</script>
