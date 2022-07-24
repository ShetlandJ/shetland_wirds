<script setup>
import { ref } from "vue";
import { Head, useForm, Link } from "@inertiajs/inertia-vue3";
import NavBar from "../components/NavBar.vue";
import HelpUsCard from "../components/HelpUsCard.vue";
import HelpUsForm from "../components/HelpUsForm.vue";
import ReloadIcon from "../components/icons/ReloadIcon.vue";

import { useI18n } from "vue-i18n";
const { t } = useI18n();

defineProps({
    canLogin: {
        type: Boolean,
        default: false,
    },
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
        <Container>
            <div class="md:mx-auto mb-8 max-w-md md:max-w-2xl">
                <p class="flex text-xl justify-center mb-2 dark:text-white">
                    <b> {{ t("nav.about") }} </b>
                </p>
                <p class="text-lg mb-4 dark:text-white">
                    <i18n-t keypath="about.message1" type="span">
                        <template #spaektionary>
                            <b>Spaekationary</b>
                        </template>

                        <template #creator>
                            <a
                                class="underline"
                                href="https://www.twitter.com/JAStewart"
                            >
                                James Stewart
                            </a>
                        </template>

                        <!-- template ihd -->
                        <template #ihd>
                            <a
                                class="underline"
                                href="https://www.iheardee.com"
                            >
                                I Hear Dee </a
                            >.
                        </template>
                    </i18n-t>
                </p>

                <p class="text-lg dark:text-white mb-2">
                    {{ t("about.message2") }}
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
                    <i18n-t keypath="about.message3" type="span">
                        <template #github>
                            <a
                                class="underline"
                                href="https://github.com/ShetlandJ/shetland_wirds"
                                >Github</a
                            >
                        </template>
                    </i18n-t>
                </p>

                <p class="dark:text-white mb-4">
                    <i18n-t keypath="about.message4" type="span">
                        <template #github>
                            <a class="underline" href="/faq"> FAQ </a>
                        </template>
                    </i18n-t>
                </p>
                <p class="dark:text-white mb-4">
                    <i18n-t keypath="about.message5" type="span">
                        <template #github>
                            <a
                                class="underline"
                                href="https://www.iheardee.com"
                            >
                                {{ t("global.here") }}
                            </a>
                        </template>
                    </i18n-t>
                </p>
            </div>
        </Container>
    </div>
</template>