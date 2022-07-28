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
import CiteSheet from "./words/CiteSheet.vue";
import ReportForm from "./words/ReportForm.vue";

import { computed, onMounted, ref } from "vue";

import { useI18n } from "vue-i18n";
const { t } = useI18n();

import format from "date-fns/format";
const APA_DATE_FORMAT = "yyyy MMMM d";
const CHICAGO_DATE_FORMAT = "MMMM d, yyyy";
const BIBTEX_YEAR = "yyyy";
const BIBTEX_DATE = "d-MMMM-yyyy";
const HOUR_MINUTE_FORMAT = "h:mm a";

const props = defineProps({
    word: Object,
    searchString: String,
    isLoggedIn: Boolean,
    adminView: Boolean,
    fullView: Boolean,
    userSelectedLocations: Array,
    exactMatch: Boolean,
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

    form.post(route("word.like", { slug: props.word.slug }), {
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

const url = Inertia.page.url;
const isComments = url.includes("comments");
const isRecordings = url.includes("recordings");
const isLocations = url.includes("locations");

let showDescriptor = ref(false);

const toggleDescriptor = (definition) => {
    if (definition.type_descriptor) {
        showDescriptor.value = !showDescriptor.value;
    }
};

const showCite = ref(false);

const URL = window.location.href;

const citeURL = computed(
    () => `${window.location.origin}/word/id/${props.word.id}`
);

const showReportForm = ref(false);

const toggleShowCite = () => {
    showCite.value = !showCite.value;
    showReportForm.value = false;
};

const toggleShowReportForm = () => {
    showReportForm.value = !showReportForm.value;
    showCite.value = false;
};

const closeReportForm = () => {
    showReportForm.value = false;
    showReportAlert.value = true;
};

const showReportAlert = ref(false);
</script>

<template>
    <Container>
        <div className="items-start dark:bg-gray-700">
            <div>
                <Alert
                    class="mb-4"
                    v-if="showReportAlert"
                    variant="success"
                    :message="t('word.report')"
                />
                <div className="flex items-center justify-between mb-2">
                    <div
                        class="flex"
                        :class="[
                            word.definitions && word.definitions.length > 0
                                ? 'mb-1'
                                : 'mb-3',
                        ]"
                    >
                        <Link
                            :href="route('word.comments', { word: word.slug })"
                            class="text-sm text-gray-700 flex"
                        >
                            <h2
                                className="
                                text-lg
                                font-semibold
                                underline
                                text-gray-900
                                -mt-1
                                dark:text-white

                            "
                            >
                                {{ word.word }}
                            </h2>
                            <span
                                v-if="exactMatch"
                                class="font-sm ml-2 text-green-500"
                            >
                                🎯 {{t('word.exactMatch')}}</span
                            >
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
                            class="w-4 h-4 heart"
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
                    class="mb-3"
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
                                cursor-pointer
                            "
                        >
                            <div @click="toggleDescriptor(definition)">
                                <span class="italic underline">{{
                                    definition.readable_type
                                }}</span>
                            </div>
                        </div>
                    </div>
                    <div
                        v-if="showDescriptor"
                        class="
                            ml-auto
                            text-right text-sm
                            dark:text-white
                            text-gray-500
                            cursor-pointer
                        "
                    >
                        {{ definition.type_descriptor }}
                    </div>
                    <div v-if="definition.example_sentence">
                        <p className="mt-1 text-gray-700 mb-3 dark:text-white">
                            {{t('word.usage')}}: {{definition.example_sentence}}
                        </p>
                    </div>
                </div>

                <div v-if="word.linked_words.length" class="my-3">
                    <div class="flex text-sm italic">
                        <div class="mr-2 text-gray-700 dark:text-white">
                            Synonyms:
                        </div>
                        <div class="flex">
                            <div
                                v-for="(linkedWord, index) in word.linked_words"
                                :key="linkedWord.id"
                                class="mr-1"
                            >
                                <Link
                                    :href="
                                        route('word.comments', {
                                            word: linkedWord.slug,
                                        })
                                    "
                                    class="
                                        text-gray-700
                                        dark:text-white
                                        underline
                                    "
                                >
                                    {{ linkedWord.word }}
                                </Link>
                                <span
                                    v-if="index < word.linked_words.length - 1"
                                    class="text-gray-500"
                                    >,</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    className="flex items-center mr-2 text-gray-700 text-sm mr-3 mt-2"
                >
                    <div>
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
                            {{ word.comments.length }}
                            {{t('word.comment', { count: word.comments.length })}}
                        </Link>

                        <Link
                            :href="route('word.comments', { word: word.slug })"
                            v-else
                            class="
                                inline-block
                                text-blue-600
                                rounded-t-lg
                                rounded-b-lg
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
                            {{ word.comments.length }}
                            {{t('word.comment', { count: word.comments.length })}}
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
                                    slug: word.slug,
                                })
                            "
                        >
                            {{ word.recordings.length }}
                            {{t('word.recording', { count: word.recordings.length })}}
                        </Link>

                        <Link
                            :href="
                                route('word.recordings', {
                                    slug: word.slug,
                                })
                            "
                            v-else
                            class="
                                inline-block
                                text-blue-600
                                rounded-t-lg
                                rounded-b-lg
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
                            {{ word.recordings.length }}
                            {{t('word.recording', { count: word.recordings.length })}}
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
                                    slug: word.slug,
                                })
                            "
                            >
                                {{t('word.location', 2)}}
                            </Link
                        >

                        <Link
                            :href="
                                route('word.locations', {
                                    slug: word.slug,
                                })
                            "
                            v-else
                            class="
                                inline-block
                                text-blue-600
                                rounded-t-lg
                                rounded-b-lg
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
                            {{t('word.location', 2)}}
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
                        <span v-if="isComments">{{t('word.comment', 2)}}</span>
                        <span v-else-if="isRecordings">{{t('word.recording', 2)}}</span>
                        <span v-else-if="isLocations">{{t('word.location', 2)}}</span>
                    </h4>

                    <Comments v-if="isComments" :comments="word.comments" />
                    <Recordings v-else-if="isRecordings" />
                    <Locations v-else-if="isLocations" />
                </div>
            </div>

            <div v-if="fullView">
                <div class="text-sm text-gray-600 flex mt-2">
                    <span
                        class="cursor-pointer dark:text-white underline"
                        @click="toggleShowCite"
                        >{{t('word.cite')}}</span
                    >
                    <span class="mx-2">&#8226;</span>
                    <span
                        @click="toggleShowReportForm"
                        class="cursor-pointer dark:text-white underline"
                    >
                        {{t('word.report')}}
                    </span>
                </div>

                <CiteSheet class="mt-3" v-if="showCite" :word="word" />

                <ReportForm
                    class="mt-3"
                    v-if="showReportForm"
                    :word="word"
                    @success="closeReportForm"
                />
            </div>
        </div>
    </Container>
</template>

<style scoped>
.heart {
    height: 20px;
    width: 20px;
}
</style>