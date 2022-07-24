<script setup>
import { formatDate } from "../../utils/formatters";
import { ref } from "vue";
const isLoggedIn = ref(() => usePage().props.value.isLoggedIn);

const emit = defineEmits(['remove']);

const props = defineProps({
    word: Object,
    recording: Object,
    index: Number,
    adminView: Boolean,
    canRemove: {
        type: Boolean,
        default: false,
    }
});

const remove = () => {
    emit('remove', props.recording.id);
}
</script>

<template>
    <div
        class="
            bg-indigo-100
            p-4
            rounded-md
            flex
            justify-between
            items-center
            mb-4
        "
    >
        <div class="basis-1/2 hidden md:block">
            <p><b>Speaker</b>: {{ recording.speaker_name }}</p>
            <p><b>Type</b>: {{ recording.type }}</p> <span v-if="adminView">
                Word: {{recording.word.word}}
            </span>
            <p><b>Date</b>: {{ formatDate(new Date(recording.created_at)) }}</p>
        </div>

        <audio controls class="w-full">
            <source :src="recording.url" type="audio/mpeg" />
        </audio>

        <div v-if="canRemove">
            <button
                class="
                    bg-red-500
                    text-white
                    text-sm
                    py-2
                    ml-2
                    px-4
                    rounded-md
                    hover:bg-red-600
                    hover:text-white
                    focus:outline-none
                    focus:shadow-outline
                    focus:border-red-600
                    focus:shadow-outline-red-600
                    transition-colors duration-200
                "
                @click="remove"
            >
                Remove
            </button>
        </div>

        <div v-if="adminView">
            <button
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