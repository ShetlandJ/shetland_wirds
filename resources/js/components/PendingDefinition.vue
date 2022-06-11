<script setup>
import { formatDate } from "../utils/formatters";
import { ref } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
const isLoggedIn = ref(() => usePage().props.value.isLoggedIn);

defineProps({
    definition: Object,
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
    <div
        className="bg-white shadow-lg rounded-lg p-4 mx-4 md:mx-auto my-8 max-w-md md:max-w-2xl"
    >
        <div class="flex justify-between">
            <div>
                <p><b>User</b>: {{ definition.user }}</p>
                <p><b>Definition</b>: {{ definition.definition }}</p>
                <p><b>Example sentence</b>: {{ definition.example_sentence }}</p>
                <p><b>Word</b>: {{ definition.word }}</p>
                <p>
                    <b>Date</b>:
                    {{ formatDate(new Date(definition.created_at)) }}
                </p>
            </div>
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
    </div>
</template>