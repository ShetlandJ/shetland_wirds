<script setup>
import WordResult from "../components/WordResult.vue";
import NavBar from "../components/NavBar.vue";

const props = defineProps({
    word: Object,
    canLogin: Boolean,
    canRegister: Boolean,
    isLoggedIn: Boolean,
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

        <WordResult :is-logged-in="isLoggedIn" :word="word" full-view />
    </div>
</template>