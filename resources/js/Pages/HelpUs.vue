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
});

const showThanksMessage = ref(false);

const form = useForm({});

const refreshWords = () => {
    form.get(route("help-us"));
};

const showDefinitionForm = ref(false);
const selectedWord = ref(false);

const toggleDefinitionForm = (word) => {
    selectedWord.value = word;
    if (!showDefinitionForm.value) {
        showDefinitionForm.value = !showDefinitionForm.value;
    }
    showThanksMessage.value = false;
};

const newDefinitionForm = useForm({
    wordId: "",
    definition: "",
    example_sentence: "",
});

const submitDefinition = (wordId) => {
    newDefinitionForm.wordId = wordId;
    newDefinitionForm.post(route("help-us-new"), {
        onFinish: () => {
            newDefinitionForm.reset();
            showDefinitionForm.value = false;
            showThanksMessage.value = true;
        },
    });
};

const ignoreWordForm = useForm({
    wordId: "",
});

const ignoreWord = (wordId) => {
    ignoreWordForm.wordId = wordId;
    ignoreWordForm.post(route("help-us-ignore"), {
        onFinish: () => {
            ignoreWordForm.reset();
            selectedWord.value = null;
            showDefinitionForm = false;
        },
    });
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

        <div class="md:mx-auto my-8 mx-4 max-w-md md:max-w-2xl dark:text-white">
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
                dark:text-white
            "
        >
            <b>Words missing definitions</b>
        </p>
        <div class="flex justify-center">
            <div class="mx-4 dark:text-white">
                Click on any word that you know the definition of to help us
                plug the gaps!
            </div>
        </div>
        <div class="flex justify-center">
            <span
                @click="refreshWords"
                class="text-sm text-blue-700 cursor-pointer dark:text-blue-400"
            >
                (Refresh words)
            </span>
        </div>

        <div class="mb-2 flex justify-center">
            <div class="mb-2">
                <div class="p-4 flex flex-wrap justify-center">
                    <HelpUsCard
                        @click="toggleDefinitionForm(word)"
                        class="m-4 hover:shadow-lg cursor-pointer"
                        v-for="word in missingDefinitions"
                        :key="word.id"
                        :word="word"
                    />
                </div>
            </div>
        </div>
        <div
            v-if="showDefinitionForm && !showThanksMessage"
            class="
                flex
                justify-center
                min-w-0
                rounded-lg
                shadow-xs
                overflow-hidden
                bg-white
                dark:bg-gray-800
            "
        >
            <HelpUsForm :word="selectedWord" />
        </div>

            <div
                v-else-if="showThanksMessage"
                class="
                    text-center
                    px-4
                    py-3
                    leading-normal
                    text-blue-700
                    bg-blue-100
                    rounded-lg
                "
                role="alert"
            >
                <p>
                    Thanks! Click on another word to submit another.
                </p>
            </div>
    </div>
</template>