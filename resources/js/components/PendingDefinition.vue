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

const deleteDefinition = (recordingUuid) => {
    deleteForm.recordingUuid = recordingUuid;
    deleteForm.delete(route("definition.delete", recordingUuid), {
        recordingUuid,
        onFinish: () => {
            deleteForm.reset();
        },
    });
};

const approveForm = useForm({
    recordingUuid: null,
});

const approveDefinition = (recordingUuid) => {
    approveForm.recordingUuid = recordingUuid;
    approveForm.post(route("definition.approve", recordingUuid), {
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
                @click="approveDefinition(definition.id)"
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
                @click="deleteDefinition(definition.id)"
                class="
                    px-4
                    py-2
                    bg-red-500
                    hover:bg-red-600
                    text-white text-sm
                    font-medium
                    rounded-md
                "
            >
                Delete
            </button>
        </div>
    </Container>
</template>