<template>
    <v-layout>
        <template #content="{ locked }">
            Content placeholder
        </template>
    </v-layout>
</template>

<script>
    import { page } from '@mixins';

    export default {
        mixins: [ page ]
    };
</script>
