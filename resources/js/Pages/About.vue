<script setup>
import { ref } from "vue";
import { Head, useForm, Link } from "@inertiajs/inertia-vue3";
import NavBar from "../components/NavBar.vue";
import HelpUsCard from "../components/HelpUsCard.vue";
import HelpUsForm from "../components/HelpUsForm.vue";
import ReloadIcon from "../components/icons/ReloadIcon.vue";

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

        <div class="md:mx-auto my-8 mx-4 max-w-md md:max-w-2xl">
            <p class="text-lg mb-6 dark:text-white">
                <b>Spaektionary</b> is a collaboration with
                <a class="underline" href="https://www.iheardee.com/"
                    >I Hear Dee</a
                >
                aimed at preserving and recording the usage of Shetland
                language.
            </p>

            <p class="text-lg dark:text-white mb-6">
                We hope to capture recordings of pronounciations, and the
                location where words are most often used.
            </p>

            <p class="text-lg dark:text-white mb-6">
                We also aim to capture Shetland words not necessarily recorded
                within 'official' publications, such as John G. Graham's
                <i>Shetland Dictionary</i>. To sign up
            </p>

            <p class="text-lg dark:text-white">
                <b>Spaektionary</b> was created by Shetlander
                <a class="underline" href="https://twitter.com/JAStewart"
                    >James Stewart</a
                >. The code behind it all is open source and available on
                <a
                    class="underline"
                    href="https://github.com/ShetlandJ/shetland_wirds"
                    >Github</a
                >.
            </p>
        </div>
    </div>
</template>