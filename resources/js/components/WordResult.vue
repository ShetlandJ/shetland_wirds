<script setup>
import { Link, useForm } from "@inertiajs/inertia-vue3";
import BookIcon from "./icons/BookIcon.vue";
import Tooltip from "./Tooltip.vue";
import ShetlandFlag from "./icons/ShetlandFlag.vue";

defineProps({
    word: Object,
    searchString: String,
    isLoggedIn: Boolean,
});

const form = useForm({
    wordToLike: null,
    searchString: "",
});

const likeWord = (word, searchString) => {
    if (!this.isLoggedIn) {
        return false;
    }
    form.wordToLike = word;
    form.searchString = searchString;

    form.post(route("search", { searchTerm: searchString }), {
        searchString: searchString,
        wordToLike: form.wordToLike,
    });
};
</script>

<template>
    <div
        className="bg-white shadow-lg rounded-lg mx-4 md:mx-auto my-8 max-w-md md:max-w-2xl"
    >
        <div className="flex items-start px-4 py-6">
            <div>
                <div className="flex items-center justify-between mb-2">
                    <Link
                        :href="route('word', { word: word.word })"
                        class="text-sm text-gray-700 underline"
                    >
                        <h2
                            className="text-lg font-semibold text-gray-900 -mt-1"
                        >
                            {{ word.word }}
                        </h2>
                    </Link>

                    <Tooltip
                        v-if="word.external_id"
                        content="Official definition from John Graham's Shetland Dictionary"
                    >
                        <div style="height: 30px; width: 30px">
                            <BookIcon />
                        </div>
                    </Tooltip>
                    <Tooltip content="This word was created by a user" v-else>
                        <div style="height: 30px; width: 15px">
                            <ShetlandFlag />
                        </div>
                    </Tooltip>
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
                    v-if="word.see_also.length > 0"
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