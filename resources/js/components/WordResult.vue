<script setup>
import { Inertia } from "@inertiajs/inertia";
import { Link, useForm, usePage } from "@inertiajs/inertia-vue3";
import BookIcon from "./icons/BookIcon.vue";
import Tooltip from "./Tooltip.vue";
import ShetlandFlag from "./icons/ShetlandFlag.vue";
import Comment from "./comments/Comment.vue";
import CommentInput from "./comments/CommentInput.vue";
import Recording from "./recordings/Recording.vue";
import RecordingInput from "./recordings/RecordingInput.vue";
import Comments from "./comments/Comments.vue";
import Recordings from "./recordings/Recordings.vue";
import Locations from "./locations/Locations.vue";

import { onMounted, ref } from "vue";

const props = defineProps({
    word: Object,
    searchString: String,
    isLoggedIn: Boolean,
    adminView: Boolean,
    fullView: Boolean,
    userSelectedLocations: Array,
});

const locations = usePage().props.value.locations;

const form = useForm({
    wordToLike: null,
    searchString: "",
});

const likeWord = (word, searchString) => {
    if (!props.isLoggedIn) {
        return false;
    }
    form.wordToLike = word;
    form.searchString = searchString;

    form.post(route("wordLike", { word: props.word.word }), {
        searchString: searchString,
        wordToLike: form.wordToLike,
    });
};

const approveForm = useForm({
    wordToApprove: null,
});

const approveWord = (wordId) => {
    approveForm.wordToApprove = wordId;
    approveForm.post(route("approve"), {
        wordToApprove: approveForm.wordToApprove,
    });
};

let showRejectForm = ref(false);

const rejectForm = useForm({
    wordToReject: null,
    rejectReason: null,
});

const rejectWord = (wordId) => {
    rejectForm.wordToReject = wordId;
    rejectForm.post(route("reject"), {
        wordToApprove: rejectForm.wordToApprove,
        rejectReason: rejectForm.rejectReason,
        onFinish: () => {
            showRejectForm.value = false;
            form.reset("rejectForm");
        },
    });
};

const commentOptions = ref({
    placeholder: `Any thoughts on ${props.word.word}?`,
});

const url = Inertia.page.url;
const isComments = url.includes("comments");
const isRecordings = url.includes("recordings");
const isLocations = url.includes("locations");
</script>

<template>
    <div
        className="bg-white shadow-lg rounded-lg mx-4 md:mx-auto my-8 max-w-lg md:max-w-2xl"
    >
        <div className="items-start px-4 py-6 dark:bg-gray-700">
            <div>
                <div className="flex items-center justify-between mb-2">
                    <div class="flex">
                        <Link
                            :href="route('word.comments', { word: word.slug })"
                            class="text-sm text-gray-700 underline"
                        >
                            <h2
                                className="
                                text-lg
                                font-semibold
                                text-gray-900
                                -mt-1
                                dark:text-white
                                dark:border-b
                            "
                            >
                                {{ word.word }}
                            </h2>
                        </Link>
                        <span v-if="word.rejected" class="text-red-500 ml-3">
                            (Rejected)
                        </span>
                    </div>
                    <div class="flex">
                        <span class="mr-1 text-sm dark:text-white">{{
                            word.likes
                        }}</span>
                        <svg
                            fill="none"
                            viewBox="0 0 24 24"
                            class="w-4 h-4 mr-3 heart"
                            :class="[isLoggedIn ? 'cursor-pointer' : '']"
                            stroke="red"
                            @click="likeWord(word.word, searchString)"
                        >
                            <path
                                v-if="word.is_liked"
                                fill="red"
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                            />
                            <path
                                v-else
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                            />
                        </svg>
                    </div>
                </div>
                <div
                    v-for="(definition, index) in word.definitions"
                    :key="definition.id"
                >
                    <div class="flex mb-2">
                        <div
                            class="mr-2 dark:text-white"
                            v-if="word.definitions.length > 1"
                        >
                            {{ index + 1 }}.
                        </div>
                        <div className="text-gray-700 dark:text-white">
                            {{ definition.definition.replace(/,/g, ", ") }}
                        </div>
                        <div
                            v-if="definition.readable_type"
                            class="
                                ml-auto
                                text-right text-sm
                                dark:text-white
                                text-gray-500
                                italic
                            "
                        >
                            {{ definition.readable_type }}
                        </div>
                    </div>
                    <div v-if="definition.example_sentence">
                        <p className="mt-1 text-gray-700 mb-3 dark:text-white">
                            {{
                                definition.example_sentence
                                    ? `Usage: ${definition.example_sentence}`
                                    : "No example sentence exists"
                            }}
                        </p>
                    </div>
                </div>

                <div
                    className="flex items-center mr-2 text-gray-700 text-sm mr-3 mt-2"
                >
                    <div :class="[fullView ? 'border-b border-gray-200' : '']">
                        <Link
                            v-if="!fullView"
                            :href="route('word.comments', { word: word.slug })"
                            class="
                                text-sm text-gray-700
                                hover:underline
                                mr-2
                                dark:text-white
                            "
                        >
                            {{ word.comments.length }} comment{{
                                word.comments.length === 1 ? "" : "s"
                            }}
                        </Link>

                        <Link
                            :href="route('word.comments', { word: word.slug })"
                            v-else
                            class="
                                inline-block
                                text-blue-600
                                rounded-t-lg
                                py-4
                                px-4
                                text-sm
                                font-medium
                                text-center
                                dark:bg-gray-800 dark:text-white
                            "
                            :class="[
                                isComments
                                    ? 'bg-gray-100 dark:bg-gray-500 font-bold'
                                    : '',
                            ]"
                        >
                            {{ word.comments.length }} comment{{
                                word.comments.length === 1 ? "" : "s"
                            }}
                        </Link>

                        <Link
                            v-if="!fullView"
                            class="
                                text-sm text-gray-700
                                hover:underline
                                dark:text-white
                            "
                            :href="
                                route('word.recordings', {
                                    word: word.slug,
                                })
                            "
                        >
                            {{ word.recordings.length }} recording{{
                                word.recordings.length === 1 ? "" : "s"
                            }}
                        </Link>

                        <Link
                            :href="
                                route('word.recordings', {
                                    word: word.slug,
                                })
                            "
                            v-else
                            class="
                                inline-block
                                text-blue-600
                                rounded-t-lg
                                py-4
                                px-4
                                text-sm
                                font-medium
                                text-center
                                dark:bg-gray-800 dark:text-white
                            "
                            :class="[
                                isRecordings
                                    ? 'bg-gray-100 dark:bg-gray-500 font-bold'
                                    : '',
                            ]"
                        >
                            {{ word.recordings.length }} recording{{
                                word.recordings.length === 1 ? "" : "s"
                            }}
                        </Link>

                        <Link
                            v-if="!fullView"
                            class="
                                text-sm text-gray-700
                                hover:underline
                                dark:text-white
                                ml-2
                            "
                            :href="
                                route('word.locations', {
                                    word: word.slug,
                                })
                            "
                            >locations</Link
                        >

                        <Link
                            :href="
                                route('word.locations', {
                                    word: word.slug,
                                })
                            "
                            v-else
                            class="
                                inline-block
                                text-blue-600
                                rounded-t-lg
                                py-4
                                px-4
                                text-sm
                                font-medium
                                text-center
                                dark:bg-gray-800 dark:text-white
                            "
                            :class="[
                                isLocations
                                    ? 'bg-gray-100 dark:bg-gray-500 font-bold'
                                    : '',
                            ]"
                        >
                            locations
                        </Link>
                    </div>
                </div>
                <div v-if="fullView" class="mt-4">
                    <h4
                        class="
                            my-5
                            uppercase
                            tracking-wide
                            text-gray-400
                            font-bold
                            text-xs
                        "
                    >
                        <span v-if="isComments">Comments</span>
                        <span v-else-if="isRecordings">Recordings</span>
                        <span v-else-if="isLocations">Locations</span>
                    </h4>

                    <Comments v-if="isComments" :comments="word.comments" />
                    <Recordings v-else-if="isRecordings" />
                    <Locations v-else-if="isLocations" />
                </div>
            </div>

            <div class="mt-4 flex justify-between" v-if="word.pending">
                <div class="mt-2">
                    <span class="font-bold">Creator</span>:
                    {{ word.creator_name }}
                </div>
                <div>
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
                        @click="approveWord(word.id)"
                    >
                        Approve
                    </button>
                    <button
                        v-if="!word.rejected"
                        class="
                            px-4
                            py-2
                            bg-yellow-500
                            hover:bg-yellow-600
                            text-white text-sm
                            font-medium
                            rounded-md
                        "
                        @click="showRejectForm = !showRejectForm"
                    >
                        {{ showRejectForm ? "Cancel" : "Reject" }}
                    </button>
                </div>
            </div>

            <div v-if="showRejectForm">
                <label
                    for="exampleFormControlTextarea1"
                    class="form-label inline-block mb-2 text-gray-700"
                    >Reject reason</label
                >
                <textarea
                    v-model="rejectForm.rejectReason"
                    class="
                        form-control
                        block
                        w-full
                        px-3
                        py-1.5
                        text-base
                        font-normal
                        text-gray-700
                        bg-white bg-clip-padding
                        border border-solid border-gray-300
                        rounded
                        transition
                        ease-in-out
                        m-0
                        focus:text-gray-700
                        focus:bg-white
                        focus:border-blue-600
                        focus:outline-none
                    "
                    id="exampleFormControlTextarea1"
                    rows="3"
                    placeholder="Your message"
                />
                <div class="flex justify-end">
                    <button
                        class="
                            mt-2
                            px-4
                            py-2
                            bg-red-500
                            hover:bg-red-600
                            text-white text-sm
                            font-medium
                            rounded-md
                        "
                        @click="rejectWord(word.id)"
                    >
                        Reject
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.heart {
    height: 20px;
    width: 20px;
}
</style>