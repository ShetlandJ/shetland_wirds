<script setup>
import { useForm, usePage } from "@inertiajs/inertia-vue3";
import Selectable from "../components/Selectable.vue";
const emit = defineEmits(["hide-form"]);
const isLoggedIn = usePage().props.value.isLoggedIn;

defineProps({
    word: Object,
});

const form = useForm({
    newWord: "",
    translation: "",
    example_sentence: "",
    word_type: null,
});

const newDefinitionForm = useForm({
    wordId: "",
    definition: "",
    example_sentence: "",
    word_type: "",
});

const submitDefinition = (wordId) => {
    newDefinitionForm.wordId = wordId;
    newDefinitionForm.post(route("help-us-new"), {
        onFinish: () => {
            newDefinitionForm.reset();
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
        },
    });
};

const defaultWordTypes = [
    {
        name: "Verb",
        value: "verb",
    },
    {
        name: "Noun",
        value: "noun",
    },
    {
        name: "Adjective",
        value: "adjectiev",
    },
    {
        name: "Adverb",
        value: "adverb",
    },
    {
        name: "Preposition",
        value: "preposition",
    },
    {
        name: "Conjunction",
        value: "conjunction",
    },
    {
        name: "Interjection",
        value: "interjection",
    },
    {
        name: "Pronoun",
        value: "pronoun",
    },
    {
        name: "Numeral",
        value: "numeral",
    },
    {
        name: "Article",
        value: "article",
    },
    {
        name: "Auxiliary",
        value: "auxiliary",
    },
    {
        name: "Determiner",
        value: "determiner",
    },
    {
        name: "Idiom",
        value: "idiom",
    },
    {
        name: "Proper noun",
        value: "proper_noun",
    },
];
</script>

<template>
    <div
        class="
            block
            p-6
            my-8
            md:max-w-2xl
            rounded-lg
            shadow-lg
            bg-white
            max-w-sm
            dark:bg-gray-700
        "
        style="width: 50%"
    >
        <form @submit.prevent="submitDefinition(word.id)">
            <h1 class="text-2xl mb-2 dark:text-white">{{word.word}}</h1>

            <div class="form-group mb-6">
                <label
                    for="wordInput"
                    class="form-label inline-block mb-2 text-gray-700 dark:text-white"
                >
                    Word/phrase definition
                    <span class="text-danger">(required)</span>
                </label>
                <input
                    v-model="form.definition"
                    type="text"
                    class="
                        form-control
                        block
                        w-full
                        px-3
                        py-1.5
                        text-base
                        font-normal
                        text-gray-700
                        bg-white bg-clip-padding
                        border border-solid border-gray-300
                        rounded
                        transition
                        ease-in-out
                        m-0
                        focus:text-gray-700
                        focus:bg-white
                        focus:border-blue-600
                        focus:outline-none
                    "
                    id="wordInput"
                    aria-describedby="wordHelp"
                />
                <small id="wordHelp" class="block mt-1 text-xs text-gray-600 dark:text-white">
                    Enter the definition for {{ word.word }}
                </small>
            </div>
            <div class="form-group mb-6">
                <label
                    for="translationInput"
                    class="form-label inline-block mb-2 text-gray-700 dark:text-white"
                >
                    Example sentence
                </label>
                <input
                    v-model="form.example_sentence"
                    type="text"
                    class="
                        form-control
                        block
                        w-full
                        px-3
                        py-1.5
                        text-base
                        font-normal
                        text-gray-700
                        bg-white bg-clip-padding
                        border border-solid border-gray-300
                        rounded
                        transition
                        ease-in-out
                        m-0
                        focus:text-gray-700
                        focus:bg-white
                        focus:border-blue-600
                        focus:outline-none
                    "
                    id="translationInput"
                    placeholder="Shetland language example sentence"
                />
            </div>

            <div class="form-group form-check mb-6">
                <p class="dark:text-white">What type of word is it?</p>
                <div>
                    <select
                        v-model="form.word_type"
                        class="
                            w-full
                            px-3
                            py-1.5
                            font-normal
                            text-gray-700
                            bg-white bg-clip-padding
                            border border-solid border-gray-300
                            rounded
                            transition
                            ease-in-out
                            m-0
                            focus:text-gray-700
                            focus:bg-white
                            focus:border-blue-600
                            focus:outline-none
                        "
                    >
                    <option disabled selected :value="null">
                        Select word type (optional)

                    </option>
                        <option
                            v-for="(wordType, index) in defaultWordTypes"
                            :key="index"
                            class="h1"
                            :value="wordType.value"
                        >
                            {{ wordType.name }}
                        </option>
                    </select>
                </div>

            </div>
            <div class="flex justify-between">
            <button
                :disabled="!form.definition"
                type="submit"
                class="
                    px-6
                    py-2.5
                    bg-blue-600
                    text-white
                    font-medium
                    text-xs
                    leading-tight
                    uppercase
                    rounded
                    shadow-md
                    hover:bg-blue-700 hover:shadow-lg
                    focus:bg-blue-700
                    focus:shadow-lg
                    focus:outline-none
                    focus:ring-0
                    active:bg-blue-800 active:shadow-lg
                    transition
                    duration-150
                    ease-in-out
                    disabled:opacity-50
                "
            >
                Submit
            </button>

            <button
                v-if="isLoggedIn"
                class="
                    mt-2
                    px-4
                    py-2
                    bg-red-500
                    hover:bg-red-600
                    text-white text-sm
                    font-medium
                    rounded-md
                "
                @click="ignoreWord(selectedWord.id)"
            >
                Ignore
            </button>
            </div>
        </form>
    </div>
</template>

<style scoped>
.text-danger {
    color: #f44336;
}
</style>