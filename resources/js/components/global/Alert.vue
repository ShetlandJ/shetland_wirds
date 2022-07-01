<script setup>
defineProps({
    message: {
        type: String,
        required: true,
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
        role="alert"
    >
        <p>
            {{ message }}
        </p>
    </div>
</template>