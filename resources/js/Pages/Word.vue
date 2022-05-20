<script setup>
import WordResult from "../components/WordResult.vue";
import NavBar from "../components/NavBar.vue";

const props = defineProps({
    word: Object,
    canLogin: Boolean,
    canRegister: Boolean,
    isLoggedIn: Boolean,
    recording: String
});
</script>

<template>
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

        <audio controls>
            <source :src="'http://localhost:8000/storage/booshim/581a8675-3515-41fd-bcc2-59eb4d3a11c9.mp3'" type="audio/mpeg">
        </audio>

        <WordResult :is-logged-in="isLoggedIn" :word="word" full-view />
    </div>
</template>