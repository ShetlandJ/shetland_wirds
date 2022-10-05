<script setup>
import { formatDate } from "../../utils/formatters";
import { ref } from "vue";
const isLoggedIn = ref(() => usePage().props.value.isLoggedIn);

import { useI18n } from "vue-i18n";
import { usePage } from "@inertiajs/inertia-vue3";
const { t } = useI18n();

const { user } = usePage().props.value;

const emit = defineEmits(["remove"]);

const props = defineProps({
    word: Object,
    recording: Object,
    index: Number,
    adminView: Boolean,
    canRemove: {
        type: Boolean,
        default: false,
    },
});

const remove = () => {
    emit("remove", props.recording.id);
};
</script>

<template>
    <div
        class="
            bg-indigo-100
            p-4
            rounded-md
            mb-4
        "
    >
        <div class="flex justify-between items-center">
            <div class="basis-1/2 hidden md:block">
                <p>
                    <b>{{ t("word.recordings.speaker") }}</b
                    >: {{ recording.speaker_name }}
                </p>
                <p>
                    <b>{{ t("word.recordings.type") }}</b
                    >: {{ recording.type }}
                </p>
                <span v-if="adminView">
                    {{ t("word.recordings.word") }}: {{ recording.word.word }}
                </span>
                <p>
                    <b>{{ t("word.recordings.date") }}</b
                    >: {{ formatDate(new Date(recording.created_at)) }}
                </p>
            </div>

            <audio controls class="w-full">
                <source :src="recording.url" type="audio/mpeg" />
            </audio>

            <div v-if="canRemove">
                <button
                    class="
                        bg-red-500
                        text-white text-sm
                        py-2
                        ml-2
                        px-4
                        rounded-md
                        hover:bg-red-600 hover:text-white
                        focus:outline-none
                        focus:shadow-outline
                        focus:border-red-600
                        focus:shadow-outline-red-600
                        transition-colors
                        duration-200
                    "
                    @click="remove"
                >
                    {{ t("global.remove") }}
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
                    {{ t("global.approve") }}
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
                    {{ t("global.reject") }}
                </button>
            </div>
        </div>
        <p class="mt-2" v-if="user.location">
            Speaker has marked their accent as being from <b>{{user.location.name}}</b>
        </p>
    </div>
</template>