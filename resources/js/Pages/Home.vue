<script setup>
import { ref, computed } from "vue";
import { Head, useForm, Link } from "@inertiajs/inertia-vue3";
import SearchBar from "../components/SearchBar.vue";
import WordResult from "../components/WordResult.vue";
import NavBar from "../components/NavBar.vue";

let searchString = "";

const newWord = useForm({
    word: "",
    translation: "",
    example_sentence: "",
});

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    isLoggedIn: Boolean,
    words: Array,
    pagination: Object,
    searchString: String,
    letter: String,
    randomWord: String,
    featuredWord: Object,
});

const form = useForm({
    page: 1,
    searchString: "",
});

const letterForm = useForm({
    page: 1,
    letter: "",
});

const handlePageChange = (pageNumber) => {
    if (props.letter) {
        letterForm.letter = props.letter;
        letterForm.page = pageNumber;
        letterForm.get(route("letter", props.letter), {
            letter: letterForm.letter,
            page: letterForm.page,
        });
    } else {
        form.page = pageNumber;
        form.searchString = props.searchString;
        form.post(route("search", { searchTerm: form.searchString }), {
            searchString: form.searchString,
            page: pageNumber,
        });
    }
};

const currentPageStartsAt = computed(() => {
    return (
        props.pagination.page * props.pagination.perPage -
        props.pagination.perPage +
        1
    );
});

const currentPageEndsAt = computed(() => {
    return Math.min(
        props.pagination.page * props.pagination.perPage,
        props.pagination.total
    );
});

const today = () => {
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date().toLocaleDateString(undefined, options);
};
</script>

<template>
    <Head title="Shetland dictionary" />

    <div
        class="
            items-top
            justify-center
            min-h-screen
            bg-gray-100
            dark:bg-gray-900
            sm:items-center sm:pt-0
        "
        style="padding-bottom: 25px"
    >
        <NavBar
            :can-login="canLogin"
            :can-register="canRegister"
            :is-logged-in="isLoggedIn"
            @set-search="searchString = $event"
            @suggest-word="toggleSuggestWordForm(true)"
        />

        <div
            className="bg-white dark:bg-gray-700 shadow-lg rounded-lg mx-4 p-2 md:mx-auto my-8 max-w-lg md:max-w-2xl"
        >
            <div class="mb-8">
                <p class="flex text-xl justify-center mb-2 dark:text-white">
                    <b>Spaektionary</b>
                </p>
                <p class="dark:text-white mb-4">
                    Welcome to the dictionary by and for Shetlanders!
                </p>

                <p class="dark:text-white mb-4">
                    Help us make this dictionary better by adding your voice to
                    the words and expressions. And help us by adding the words
                    and expressions that are missing!
                </p>

                <p class="dark:text-white mb-4">
                    We will map the words and expressions by location to show a
                    fuller picture of Shaetlan speech.
                </p>

                <div class="dark:text-white mb-4" style="display: block">
                    To get started, you can search for a word or phrase in the
                    search bar above, or click on browse in the menu to browse
                    by letter. If you want to contribute, click
                    <Link
                        class="text-sm text-gray-700 underline"
                        :href="route('register')"
                        style="display: inline-flex !important"
                    >
                        <h2
                            className="
                                font-semibold
                                text-lg
                                text-gray-900
                                -mt-1
                                dark:text-white
                                dark:border-b
                            "
                        >
                            here
                        </h2>
                    </Link>
                    to register!
                </div>

                <div class="dark:text-white mb-2 flex" style="display: block">
                    Our featured entry for {{ today() }} is

                    <Link
                        :href="
                            route('word.comments', { word: featuredWord.slug })
                        "
                        class="text-sm text-gray-700 underline"
                        style="display: inline-flex !important"
                    >
                        <h2
                            className="
                                font-semibold
                                text-lg
                                text-gray-900
                                -mt-1
                                dark:text-white
                                dark:border-b
                            "
                        >
                            {{ featuredWord.word }}
                        </h2>
                    </Link>
                    .
                    <span>Click to find out more!</span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-gray-100 {
    background-color: #f7fafc;
    background-color: rgba(247, 250, 252, var(--tw-bg-opacity));
}

.text-gray-500 {
    color: #a0aec0;
    color: rgba(160, 174, 192, var(--tw-text-opacity));
}

.text-gray-600 {
    color: #718096;
    color: rgba(113, 128, 150, var(--tw-text-opacity));
}

.text-gray-700 {
    color: #4a5568;
    color: rgba(74, 85, 104, var(--tw-text-opacity));
}

.text-gray-900 {
    color: #1a202c;
    color: rgba(26, 32, 44, var(--tw-text-opacity));
}

@media (prefers-color-scheme: dark) {
    .dark\:bg-gray-800 {
        background-color: #2d3748;
        background-color: rgba(45, 55, 72, var(--tw-bg-opacity));
    }

    .dark\:bg-gray-900 {
        background-color: #1a202c;
        background-color: rgba(26, 32, 44, var(--tw-bg-opacity));
    }

    .dark\:border-gray-700 {
        border-color: #4a5568;
        border-color: rgba(74, 85, 104, var(--tw-border-opacity));
    }

    .dark\:text-white {
        color: #fff;
        color: rgba(255, 255, 255, var(--tw-text-opacity));
    }
}
</style>
