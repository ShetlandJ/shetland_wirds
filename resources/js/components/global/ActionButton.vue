<script setup>
import { computed } from "@vue/runtime-core";

const props = defineProps({
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
        default: null,
    },
    colour: {
        type: String,
        default: "blue",
    },
});

const colourClasses = computed(() => {
    return `
        bg-${props.colour}-600
        hover:bg-${props.colour}-700 hover:shadow-lg
        focus:bg-${props.colour}-700 focus:shadow-lg focus:outline-none focus:ring-0
        active:bg-${props.colour}-800 active:shadow-lg
    `;
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
            text-white
            font-medium
            text-xs
            leading-tight
            uppercase
            rounded
            shadow-md
            transition
            duration-150
            ease-in-out
            flex
            items-center
            disabled:opacity-50
        "
        :class="colourClasses"
    >
        <template v-if="message">{{ message }}</template>
        <slot v-else />
    </button>
</template>