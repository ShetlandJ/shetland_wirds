<script setup>
import { formatDateTime } from "../utils/formatters";
import { ref } from "vue";
const isLoggedIn = ref(() => usePage().props.value.isLoggedIn)

defineProps({
    word: Object,
    recording: Object
});


</script>

<template>
    <div class="flex mb-4">
        <audio controls>
            <source :src="recording.url" type="audio/mpeg">
        </audio>
    </div>
</template>