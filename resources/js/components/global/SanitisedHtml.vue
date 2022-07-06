<script setup>
import { computed } from 'vue';
import { sanitiseString } from '../../utils/formatters';

const props = defineProps({
    htmlString: String,
});

const removeBrTag = (str) => str.replace(/<br>/g, "");

const cleanedString = computed(() => {
    if (!props.htmlString) return "";

    let str = props.htmlString;

    str = removeBrTag(str);

    return sanitiseString(str);
});
</script>

<template>
    <span v-html="cleanedString" />
</template>