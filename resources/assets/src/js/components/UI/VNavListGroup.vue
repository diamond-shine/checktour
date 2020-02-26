<template>
    <div>
        <v-divider v-if="$slots.default" />

        <slot />
    </div>
</template>
