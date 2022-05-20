<script setup>
import { formatDate } from "../utils/formatters";
import { ref } from "vue";
const isLoggedIn = ref(() => usePage().props.value.isLoggedIn);

defineProps({
    word: Object,
    recording: Object,
    index: Number,
});
</script>

<template>
    <div class="bg-indigo-100 p-4 rounded-md flex justify-between items-center mb-4">
        <div class="basis-1/4">
            <p><b>Speaker</b>: {{ recording.speaker_name }}</p>
            <p><b>Type</b>: {{ recording.type }}</p>
            <p><b>Date</b>: {{ formatDate(new Date(recording.created_at)) }}</p>
        </div>

        <audio controls>
            <source :src="recording.url" type="audio/mpeg" />
        </audio>
    </div>
</template>