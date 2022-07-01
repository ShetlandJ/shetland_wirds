<script setup>
defineProps({
    message: {
        type: String,
        required: true,
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
            variant === 'info' ? 'bg-blue-100 text-blue-700' : '',
            variant === 'success' ? 'bg-green-100 text-green-700' : '',
        ]"
        role="alert"
    >
        <p>
            {{ message }}
        </p>
    </div>
</template>