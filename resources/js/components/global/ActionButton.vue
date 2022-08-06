<script setup>
defineProps({
    message: {
        type: String,
        default: null,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    type: {
        type: String,
        default: null
    },
});
</script>

<template>
    <button
        :disabled="disabled"
        :type="type"
        class="
            btn
            inline-block
            px-6
            py-2.5
            bg-blue-600
            text-white
            font-medium
            text-xs
            leading-tight
            uppercase
            rounded
            shadow-md
            hover:bg-blue-700 hover:shadow-lg
            focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0
            active:bg-blue-800 active:shadow-lg
            transition
            duration-150
            ease-in-out
            flex
            items-center
            disabled:opacity-50
        "
    >
        <template v-if="message">{{ message }}</template>
        <slot v-else />
    </button>
</template>