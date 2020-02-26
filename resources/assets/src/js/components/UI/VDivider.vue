<template>
    <div class="u-unwrap">
        <hr :class="hrClass">
    </div>
</template>

<script>
    export default {
        props: {
            hrClass: {
                type: String,
                default: ''
            }
        }
    };
</script>
