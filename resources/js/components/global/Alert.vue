<script setup>
defineProps({
    message: {
        type: String,
        default: null,
    },
    variant: {
        type: String,
        default: 'info',
    },
});
</script>

<template>
    <div
        class="
            text-center
            px-4
            py-3
            leading-normal
            text-blue-700
            bg-blue-100
            rounded-lg
        "
        :class="[
            variant === 'info' ? 'bg-blue-100 text-blue-700 dark:bg-blue-800 dark:text-blue-200' : '',
            variant === 'success' ? 'bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-200' : '',
        ]"
        role="alert"
    >
        <p v-if="message">
            {{ message }}
        </p>
        <slot v-else />
    </div>
</template>