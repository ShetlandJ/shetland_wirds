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
        <div
            className="bg-white shadow-lg rounded-lg mx-4 p-6 md:mx-auto my-8 max-w-lg md:max-w-2xl"
        >
            <div class="md:mx-auto mt-2 mb-8 mx-4 max-w-md md:max-w-2xl">
                <p class="flex text-xl justify-center mb-2 dark:text-white">
                    <b> About </b>
                </p>
                <p class="text-lg mb-4 dark:text-white">
                    The Spaektionary was created by
                    <a
                        class="underline"
                        href="https://www.twitter.com/JAStewart"
                        >James Stewart</a
                    >
                    and consists of the existing lexical database in the
                    Shaetlan Project by
                    <a class="underline" href="https://www.iheardee.com"
                        >I Hear Dee</a
                    >. The aim of this dictionary is to encourage any and all
                    speakers of Shaetlan to participate in enhancing the
                    dictionary with both sound recordings and example sentences.
                    We are also hoping for users to add words and expressions
                    that are not already in the dictionary, so as to make it
                    more complete. We aim to map submissions so that we can
                    create a visual image of where in Shetland which words and
                    pronunciations are most commonly found. This will allow for
                    a more nuanced picture of the rich and colourful Shaetlan
                    language than has been available before.
                </p>

                <p class="text-lg dark:text-white mb-2">
                    The database that the Spaektionary is built on consists of a
                    corpus of the speech by 20 oral history interviews by the
                    Shetland Museum and Archives done in the 1980s and 1990s,
                    released for research by kind permission from Dr. Brian
                    Smith. This oral history corpus has a total of 37,5 hours of
                    speech (ca 316,000 words). The speakers are from different
                    areas of Shetland, there are 10 male and 10 female speakers
                    and they are all 60 years or above at the time of recording.
                    All interviews were transcribed by Angus Johnson, who kindly
                    released his transcriptions for the use in this project. The
                    database also includes the collected entries found in the
                    following publications:
                </p>

                <p class="dark:text-white mb-2 ml-4">
                    Christie-Johnston, Alistair & Adaline Christie-Johnston.
                    2014.
                    <i>Shetland words. A dictionary of the Shetland dialect</i>
                    Lerwick: The Shetland Times.
                </p>

                <p class="dark:text-white mb-2 ml-4">
                    Graham, John J. 2009. <i>The Shetland dictionary</i>.
                    Lerwick: Shetland Times.
                </p>

                <p class="dark:text-white mb-2 ml-4">
                    Shetland ForWirds. 2015.
                    <i>Mirds o Wirds. A Shetland dialect word book</i> Lerwick:
                    Shetland Times.
                </p>

                <p class="dark:text-white mb-2 ml-4">
                    Shetland Guild of Spinners, Knitters, Weavers and Dyers.
                    2015. <i>A legacy of Shetland lace</i>
                    Lerwick: Shetland Times.
                </p>

                <p class="dark:text-white mb-4">
                   The code behind it all is open source and available on
                    <a
                        class="underline"
                        href="https://github.com/ShetlandJ/shetland_wirds"
                        >Github</a
                    >.
                </p>

                <p class="dark:text-white mb-4">
                   See our <a class="underline" href="/faq">Frequently Asked Questions</a> page for more information
                    about the project.
                </p>
                <p class="dark:text-white mb-4">
                   For more about I Hear Dee and who we are, see <a class="underline" href="https://www.iheardee.com">here</a>.
                </p>
            </div>
        </div>
    </div>
</template>