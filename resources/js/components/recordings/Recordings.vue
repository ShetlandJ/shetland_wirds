<script setup>
import { usePage } from "@inertiajs/inertia-vue3";
import Recording from './Recording.vue';
import RecordingInput from './RecordingInput.vue';

const {
    word,
    isLoggedIn
} = usePage().props.value;
</script>

<template>
    <div v-if="word">
        <div
            v-if="!word.recordings.length"
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
                There are no recordings available for this word yet, be the
                first to add one!
            </p>
        </div>

        <Recording
            v-for="(recording, index) in word.recordings"
            :recording="recording"
            :key="recording.id"
            :index="index"
        />

        <h4
            class="my-5 uppercase tracking-wide text-gray-400 font-bold text-xs"
        >
            Add a recording of your own
        </h4>
        <template v-if="isLoggedIn">
            <p class="mb-2">
                To add your own recording, just press the Record button to
                start, and the same button again to stop. You'll be able to
                listen back to your recording before submitting.
            </p>
            <p class="mb-2">
                Your recording will need to be approved before it will be
                visible on the site.
            </p>

            <RecordingInput :key="word.recordings.length" />
        </template>

        <div
            v-else
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
            <p>Sign up to add a recording!</p>
        </div>
    </div>
</template>

<style lang="scss" scoped>
</style>