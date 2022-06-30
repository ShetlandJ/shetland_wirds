<script setup>
import WordResult from "../components/WordResult.vue";
import NavBar from "../components/NavBar.vue";
import { Head, usePage } from "@inertiajs/inertia-vue3";

let urlPrev = usePage().props.value.urlPrev;

const props = defineProps({
    word: Object,
    canLogin: Boolean,
    canRegister: Boolean,
    isLoggedIn: Boolean,
    locations: Array,
    userSelectedLocations: Array,
    locationData: Array
});
</script>

<template>
    <Head :title="word.word" />

    <div
        class="
            items-top
            justify-center
            min-h-screen
            bg-gray-100
            dark:bg-gray-900
            sm:items-center sm:pt-0
        "
    >
        <NavBar
            :can-login="canLogin"
            :can-register="canRegister"
            :is-logged-in="isLoggedIn"
            @set-search="searchString = $event"
            @suggest-word="toggleSuggestWordForm(true)"
        />

        <WordResult
            :is-logged-in="isLoggedIn"
            :word="word"
            full-view
            :user-selected-locations="userSelectedLocations"
        />
    </div>
</template>