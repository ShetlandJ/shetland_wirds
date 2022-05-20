<script setup>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import BookIcon from "./icons/BookIcon.vue";
import Tooltip from "./Tooltip.vue";
import ShetlandFlag from "./icons/ShetlandFlag.vue";
import Comment from "./Comment.vue";
import CommentInput from "./CommentInput.vue";
import Recording from "./Recording.vue";
import { ref } from "vue";

const props = defineProps({
    word: Object,
    searchString: String,
    isLoggedIn: Boolean,
    adminView: Boolean,
    fullView: Boolean,
});

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

    form.post(route("search", { searchTerm: searchString }), {
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
        onFinish: () => form.reset("rejectForm"),
    });
};

const commentOptions = ref({
    placeholder: `Any thoughts on ${props.word.word}?`,
});

const activeTab = ref("comments");
</script>

<template>
    <div
        className="bg-white shadow-lg rounded-lg mx-4 md:mx-auto my-8 max-w-md md:max-w-2xl"
    >
        <div className="items-start px-4 py-6">
            <div>
                <div className="flex items-center justify-between mb-2">
                    <div class="flex">
                        <Link
                            :href="route('word', { word: word.slug })"
                            class="text-sm text-gray-700 underline"
                        >
                            <h2
                                className="text-lg font-semibold text-gray-900 -mt-1"
                            >
                                {{ word.word }}
                            </h2>
                        </Link>
                        <span v-if="word.rejected" class="text-red-500 ml-3">
                            (Rejected)
                        </span>
                    </div>

                    <Tooltip
                        v-if="word.external_id"
                        content="Official definition from John Graham's Shetland Dictionary"
                    >
                        <div style="height: 30px; width: 30px">
                            <BookIcon />
                        </div>
                    </Tooltip>
                    <div class="mr-4" v-else>
                        <Tooltip content="This word was created by a user">
                            <div style="height: 30px; width: 15px">
                                <ShetlandFlag />
                            </div>
                        </Tooltip>
                    </div>
                </div>

                <p className="text-gray-700 mb-2">{{ word.translation }}</p>
                <div v-if="word.example_sentence">
                    <p className="mt-3 text-gray-700 mb-2">
                        {{
                            word.example_sentence
                                ? `Usage: ${word.example_sentence}`
                                : "No example sentence exists"
                        }}
                    </p>
                </div>
                <div
                    className="flex align-items-end mb-1"
                    v-if="word.see_also && word.see_also.length > 0"
                >
                    <span className="text-gray-900 mr-2"> See also: </span>
                    <span
                        v-for="seeAlso in word.see_also"
                        className="cursor-pointer underline hover:text-blue-800 visited:text-purple-600 mr-2"
                        :key="seeAlso"
                    >
                        {{ seeAlso.word }}
                    </span>
                </div>

                <div className="flex mr-2 text-gray-700 text-sm mr-3">
                    <svg
                        fill="none"
                        viewBox="0 0 24 24"
                        class="w-4 h-4 mr-1 heart cursor-pointer"
                        stroke="red"
                        @click="likeWord(word.word, searchString)"
                    >
                        <template v-if="isLoggedIn">
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
                        </template>
                        <template v-else>
                            <path
                                fill="red"
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                            />
                        </template>
                    </svg>

                    <span v-if="word.likes">{{ word.likes }}</span>

                    <span class="mx-2">&#8226;</span>

                    <Link
                        v-if="!fullView"
                        :href="route('word', { word: word.slug })"
                        class="text-sm text-gray-700 underline"
                    >
                        {{ word.comments.length }} comment{{
                            word.comments.length === 1 ? "" : "s"
                        }}
                    </Link>

                    <button
                        @click="activeTab = 'comments'"
                        v-else
                        class="text-sm text-gray-700 hover:underline"
                    >
                        {{ word.comments.length }} comment{{
                            word.comments.length === 1 ? "" : "s"
                        }}
                    </button>

                    <span class="mx-2">&#8226;</span>

                    <button
                        @click="activeTab = 'recordings'"
                        class="text-sm text-gray-700 hover:underline"
                    >
                        {{ word.recordings.length }} recording{{
                            word.recordings.length === 1 ? "" : "s"
                        }}
                    </button>
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
                        {{
                            activeTab === "comments" ? "Comments" : "Recordings"
                        }}
                    </h4>
                    <div v-if="activeTab === 'comments'">
                        <Comment
                            v-for="comment in word.comments"
                            :key="comment.id"
                            :comment="comment"
                            :word="word"
                        />
                        <div class="mb-4">
                            <CommentInput
                                v-if="isLoggedIn"
                                :word="word"
                                :options="commentOptions"
                            />
                        </div>
                    </div>
                    <div v-else-if="activeTab === 'recordings'">
                        <Recording
                            v-for="(recording, index) in word.recordings"
                            :recording="recording"
                            :key="recording.id"
                            :index="index"
                        />
                    </div>
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