<script setup>
import { ref } from "vue";
import { Head, useForm, Link } from "@inertiajs/inertia-vue3";
import NavBar from "../components/NavBar.vue";
import HelpUsCard from "../components/HelpUsCard.vue";
import HelpUsForm from "../components/HelpUsForm.vue";
import ReloadIcon from "../components/icons/ReloadIcon.vue";
import FaqList from '../components/FaqList.vue';

defineProps({
    canRegister: Boolean,
    isLoggedIn: Boolean,
    missingDefinitions: Array,
    missingExampleSentences: Array,
    randomWord: String,
});
</script>

<template>
    <Head title="About" />

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
        <div
            className="bg-white dark:bg-gray-700 shadow-lg rounded-lg mx-4 p-2 md:mx-auto my-8 max-w-lg md:max-w-2xl"
        >
            <p class="flex text-xl justify-center mb-2 dark:text-white">
                <b> Frequently Asked Questions </b>
            </p>

            <p class="text-lg dark:text-white mb-6">
                The Spaektionary was created by James Stewart and consists of
                the existing lexical database in the Shaetlan Project by
                <a class="underline" href="https://www.iheardee.com"
                    >I Hear Dee</a
                >.
            </p>

            <p class="text-lg dark:text-white mb-6">
                It should be noted that this is not a dictionary of words that
                are exclusive and unique to shaetlan. Very few words are. As is
                normal for any language, shaetlan shares many words and
                expressions with other languages, especially those that are
                closely related to shaetlan, or those that shaetlan has been in
                close contact with. In fact, there is no known spoken language
                in the world that does not share vocabulary with some other
                language. This does not in any way make any given language less
                valuable. It also does not mean that these words are less
                relevant for the given languages.
            </p>

            <p class="text-lg dark:text-white mb-6">
                The immediate ancestors of shaetlan are scots and norn. Norn is
                extinct, but descended from western old norse. Scots is a macro
                language with a number of highly diverse varieties. They descend
                from northumbrian old english. Standard english, on the other
                hand, descends from mercian old english. So scots is not and has
                never been a dialect of english any more than english is or has
                been a dialect of scots. Shaetlan has also been in intense
                contact with the low country germanic languages (low german and
                dutch), and, for the last few centuries, with standard english.
                All of these languages are closely related and have many
                similarities among them. But they are not identical. And they
                have all affected the outcome of shaetlan. For more about the
                history of shaetlan, see
                <a
                    class="underline"
                    href="https://www.iheardee.com/english/history-of-shaetlan"
                    >here</a
                >.
            </p>

            <p class="text-lg dark:text-white mb-6">
                We regularly get questions of these kinds:
            </p>

            <FaqList />
        </div>
    </div>
</template>