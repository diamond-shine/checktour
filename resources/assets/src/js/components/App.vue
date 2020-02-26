<template>
    <router-view />
</template>

<script>
    import store from '@store';

    export default {
        beforeRouteEnter(to, from, next) {
            const request = store.dispatch('system/state');

            request.then(
                () => next()
            );
        }
    };
</script>
