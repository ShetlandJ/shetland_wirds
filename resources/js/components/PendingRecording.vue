<script setup>
import { formatDate } from "../utils/formatters";
import { ref } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
const isLoggedIn = ref(() => usePage().props.value.isLoggedIn);

defineProps({
    word: Object,
    recording: Object,
    index: Number,
});

const deleteForm = useForm({
    recordingUuid: null,
});

const deleteRecording = (recordingUuid) => {
    deleteForm.recordingUuid = recordingUuid;
    deleteForm.delete(route("recording.delete", recordingUuid), {
        recordingUuid,
        onFinish: () => {
            deleteForm.reset();
        },
    });
};

const approveForm = useForm({
    recordingUuid: null,
});

const approveRecording = (recordingUuid) => {
    approveForm.recordingUuid = recordingUuid;
    approveForm.post(route("recording.approve", recordingUuid), {
        recordingUuid,
        onFinish: () => {
            approveForm.reset();
        },
    });
};
</script>

<template>
    <Container>
        <div class="flex justify-between">
            <div class="basis-1/4">
                <p><b>Speaker</b>: {{ recording.speaker_name }}</p>
                <p><b>Word</b>: {{ recording.word.word }}</p>
                <p>
                    <b>Date</b>:
                    {{ formatDate(new Date(recording.created_at)) }}
                </p>
            </div>

            <audio controls>
                <source :src="recording.url" type="audio/mpeg" />
            </audio>
        </div>

        <div class="mt-4">
            <button
                @click="approveRecording(recording.id)"
                class="
                    px-4
                    py-2
                    bg-green-500
                    hover:bg-green-600
                    text-white text-sm
                    font-medium
                    rounded-md
                    mr-2
                "
            >
                Approve
            </button>
            <button
                @click="deleteRecording(recording.id)"
                class="
                    px-4
                    py-2
                    bg-yellow-500
                    hover:bg-yellow-600
                    text-white text-sm
                    font-medium
                    rounded-md
                "
            >
                Reject
            </button>
        </div>
    </Container>
</template>