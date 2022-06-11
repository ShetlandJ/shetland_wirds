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
        name: "I'm not sure!",
        value: null,
    },
    {
        name: "Verb",
        value: "v",
    },
    {
        name: "Noun",
        value: "n",
    },
    {
        name: "Adjective",
        value: "adj",
    },
    {
        name: "Adverb",
        value: "adv",
    },
    {
        name: "Preposition",
        value: "prep",
    },
    {
        name: "Conjunction",
        value: "conj",
    },
];
</script>

<template>
    <div
        class="
            block
            p-6
            md:mx-auto
            my-8
            max-w-md
            md:max-w-2xl
            rounded-lg
            shadow-lg
            bg-white
            max-w-sm
        "
    >
        <form @submit.prevent="submitDefinition(word.id)">
            <h1 class="text-2xl mb-2">{{word.word}}</h1>

            <div class="form-group mb-6">
                <label
                    for="wordInput"
                    class="form-label inline-block mb-2 text-gray-700"
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
                <small id="wordHelp" class="block mt-1 text-xs text-gray-600">
                    Enter the definition for {{ word.word }}
                </small>
            </div>
            <div class="form-group mb-6">
                <label
                    for="translationInput"
                    class="form-label inline-block mb-2 text-gray-700"
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
                <p>What type of word is it?</p>
                <div class="flex flex-wrap">
                    <selectable
                        class="mr-2"
                        v-for="(wordType, index) in defaultWordTypes"
                        :key="index"
                        :input-name="wordType.name"
                        :input-value="wordType.value"
                        :selected="form.word_type === wordType.value"
                        v-model="form.word_type"
                        @select="form.word_type = $event"
                    />
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