<script setup>
import { ref, computed } from "vue";
import { Head, useForm, Link } from "@inertiajs/inertia-vue3";
import SearchBar from "../components/SearchBar.vue";
import WordResult from "../components/WordResult.vue";
import NavBar from "../components/NavBar.vue";
import Pagination from "../components/Pagination.vue";
import MobileMenu from "../components/MobileMenu.vue";

import { useI18n } from "vue-i18n";
const { t } = useI18n();

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
    exactMatch: Object,
    pagination: Object,
    searchString: String,
    letter: String,
    randomWord: String,
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

        <div class="mb-8">
            <div
                class="flex justify-center mt-4 dark:text-white"
                v-if="pagination && pagination.pages > 1"
            >
                <span v-if="pagination.pages > 1">
                    <i18n-t keypath="nav.showingPagination" type="span">
                        <template #from>
                            <b>{{ currentPageStartsAt }}</b>
                        </template>
                        <template #to>
                            <b>{{ currentPageEndsAt }}</b>
                        </template>
                        <template #total>
                            <b>{{ pagination.total }}</b>
                        </template>
                    </i18n-t>
                </span>
            </div>

            <template v-if="(words && words.length) || exactMatch">
                <template v-if="exactMatch">
                    <WordResult
                        :is-logged-in="isLoggedIn"
                        :search-string="searchString"
                        v-for="word in [exactMatch]"
                        :key="word.uuid"
                        :word="word"
                        exact-match
                    />
                </template>

                <WordResult
                    :is-logged-in="isLoggedIn"
                    :search-string="searchString"
                    v-for="word in words"
                    :key="word.uuid"
                    :word="word"
                />
            </template>

            <Alert
                v-else-if="searchString && !words.length"
                message="Sorry, we couldn't find any words that match your search
                    term"
            />

            <div class="flex justify-center">
                <Pagination
                    v-if="pagination && pagination.pages > 1"
                    :pagination="pagination"
                    @page-change="handlePageChange"
                />
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
