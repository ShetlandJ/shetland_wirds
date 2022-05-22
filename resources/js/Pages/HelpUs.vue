<script setup>
import { ref } from "vue";
import { Head, useForm, Link } from "@inertiajs/inertia-vue3";
import NavBar from "../components/NavBar.vue";
import HelpUsCard from "../components/HelpUsCard.vue";
import ReloadIcon from "../components/icons/ReloadIcon.vue";

defineProps({
    canRegister: Boolean,
    isLoggedIn: Boolean,
    missingDefinitions: Array,
    missingExampleSentences: Array,
});

const form = useForm({});

const refreshWords = () => {
    form.get(route("help-us"));
};
</script>

<template>
    <Head title="Welcome" />

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

        <!-- <div class=""> -->
        <div class="md:mx-auto my-8 max-w-md md:max-w-2xl">
            We need contributers to help plug gaps in our dictionary of Shetland
            words. You can help us by adding any definitions or examples of
            usage from the below options.
        </div>

        <p
            class="
                text-xl
                md:mx-auto
                mt-8
                max-w-md
                md:max-w-2xl
                flex
                justify-center
            "
        >
            <b>Words missing definitions</b>
        </p>
        <div class="flex justify-center">
            <div>
                Click on any word that you know the definition of to help us
                plug the gaps!
            </div>
        </div>
        <div class="flex justify-center">
            <span
                @click="refreshWords"
                class="text-sm text-blue-700 cursor-pointer"
            >
                (Refresh words)
            </span>
        </div>

        <div class="grid gap-6 mb-2 flex items-center">
            <div class="grid gap-6 mb-2">
                <div class="p-4 flex justify-center">
                    <HelpUsCard
                        class="m-4 hover:shadow-lg cursor-pointer"
                        v-for="word in missingDefinitions"
                        :key="word.id"
                        :word="word"
                    />
                </div>
            </div>
        </div>
    </div>
</template>