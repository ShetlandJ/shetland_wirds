<script setup>
import { defineComponent } from "vue";
import Popper from "vue3-popper";

defineProps({
    content: String,
})
</script>

<template>
    <Popper placement="top" hover :content="content">
        <slot />
    </Popper>
</template>