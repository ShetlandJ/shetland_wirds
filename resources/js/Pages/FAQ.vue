<script setup>
import { ref } from "vue";
import { Head, useForm, Link } from "@inertiajs/inertia-vue3";
import NavBar from "../components/NavBar.vue";
import HelpUsCard from "../components/HelpUsCard.vue";
import HelpUsForm from "../components/HelpUsForm.vue";
import ReloadIcon from "../components/icons/ReloadIcon.vue";
import FaqList from '../components/FaqList.vue';

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
        <div
            className="bg-white dark:bg-gray-700 shadow-lg rounded-lg mx-4 p-6 md:mx-auto my-8 max-w-lg md:max-w-2xl"
        >
            <p class="flex text-xl justify-center mb-2 dark:text-white">
                <b> {{t('faq.heading')}} </b>
            </p>

            <p class="text-lg dark:text-white mb-6">
                <i18n-t keypath="faq.message1" type="span">
                <template #creator>
                    <a class="underline" href="https://www.twitter.com/JAStewart">
                        James Stewart
                    </a>
                </template>
                <template #ihd>
                    <a class="underline" href="https://www.iheardee.com"
                        >I Hear Dee</a
                    >
                </template>
                </i18n-t>
            </p>

            <p class="text-lg dark:text-white mb-6">
                {{t('faq.message2')}}
            </p>

            <p class="text-lg dark:text-white mb-6">
                <i18n-t keypath="faq.message3" type="span">
                    <template #here>
                                       <a
                    class="underline"
                    href="https://www.iheardee.com/English/history-of-Shaetlan"
                    >here</a
                >
                    </template>
                </i18n-t>
            </p>

            <p class="text-lg dark:text-white mb-6">
                {{t('faq.message4')}}
            </p>

            <FaqList />
        </div>
    </div>
</template>