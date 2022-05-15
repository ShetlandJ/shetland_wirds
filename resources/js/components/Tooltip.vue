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

<style>
.popper {
    background-color: #cff3ff !important;
    padding: 6px !important;
    border-radius: 4px !important;
}
</style>