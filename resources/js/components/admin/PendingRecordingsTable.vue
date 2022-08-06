<script setup>
import { useForm } from "@inertiajs/inertia-vue3";
import { formatDate } from "../../utils/formatters";
import { computed } from "vue";
import { Link } from "@inertiajs/inertia-vue3";

const props = defineProps({
    recordings: {
        type: Array,
        default: () => [],
    },
    hideControls: {
        type: Boolean,
        default: false,
    },
});

const fields = computed(() => {
    if (props.hideControls) {
        return ["Word", "Text", "User", "Date"];
    }

    return ["Word", "Text", "User", "Date", ""];
})

const cellClass = "px-5 py-5 border-b border-gray-200 bg-white text-sm";

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
    <div class="mx-4 mx-8 px-4 px-8 py-4 overflow-x-auto">
        <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            v-for="field in fields"
                            :key="field"
                            class="
                                px-5
                                py-3
                                border-b-2 border-gray-200
                                bg-blue-200
                                text-left text-xs
                                font-semibold
                                text-gray-800
                                uppercase
                                tracking-wider
                            "
                        >
                            {{ field }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="recording in recordings"
                        :key="recording.id"
                    >
                        <td :class="cellClass">
                                                        <Link
                                :href="
                                    route('word.comments', {
                                        slug: recording.word.slug,
                                    })
                                "
                                class="text-gray-700 underline"
                            >
                                {{ recording.word.word }}
                            </Link>
                        </td>

                        <td :class="cellClass">
                            {{ recording.speaker_name }}
                        </td>

                        <td :class="cellClass">
                            <audio controls>
                                <source :src="recording.url" type="audio/mpeg" />
                            </audio>
                        </td>

                        <td :class="cellClass">
                            {{ formatDate(new Date(recording.created_at)) }}
                        </td>

                        <td :class="cellClass" v-if="!hideControls">
                            <div>
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
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style>
td {
    vertical-align: top;
    text-align: left;
}
</style>