<script setup>
import { useForm } from "@inertiajs/inertia-vue3";
import Selectable from "../components/Selectable.vue";
const emit = defineEmits(["hide-form", "success"]);

const form = useForm({
    newWord: "",
    translation: "",
    example_sentence: "",
    word_type: null,
    confirm: false,
});

const createWord = () => {
    if (!form.confirm) {
        return false;
    }

    form.post(route("create"), {
        onFinish: () => {
            form.reset();
            emit("hide-form");
            emit("success");
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
            md:mx-auto
            my-8
            max-w-lg
            md:max-w-2xl
            rounded-lg
            shadow-lg
            bg-white
            max-w-sm
            dark:bg-gray-700
        "
    >
        <form @submit.prevent="createWord">
            <div class="form-group mb-6">
                <label
                    for="wordInput"
                    class="form-label inline-block mb-2 dark:text-white text-gray-700"
                >
                    Suggested word <span class="text-danger">(required)</span>
                </label>
                <input
                    v-model="form.newWord"
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
                <small id="wordHelp" class="block mt-1 text-xs dark:text-white text-gray-600">
                    Enter the new word you wish to add to the dictionary
                </small>
            </div>
            <div class="form-group mb-6">
                <label
                    for="translationInput"
                    class="form-label inline-block mb-2 dark:text-white text-gray-700"
                >
                    What does it mean in English?
                    <span class="text-danger">(required)</span>
                </label>
                <input
                    v-model="form.translation"
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
                    placeholder="English language translation"
                />
            </div>
            <div class="form-group mb-6">
                <label
                    for="exampleSentenceInput"
                    class="form-label inline-block mb-2 dark:text-white text-gray-700"
                >
                    Can you provide an example Shetland language sentence?
                    (Optional)
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
                    id="exampleSentenceInput"
                    placeholder="e.g. 'He's a lok bigger as his faider.'"
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
            <!-- Checkbox inside div -->
            <div class="form-group form-check mb-6 flex">
                <input
                    type="checkbox"
                    class="form-check-input mt-2"
                    id="exampleCheck1"
                    v-model="form.confirm"
                />
                <label class="ml-2 form-check-label dark:text-white" for="exampleCheck1">
                    I confirm that I am happy for Spaektionary and I Hear Dee to use this word in this online
                    dictionary, as well as in other media in the future. <span class="text-danger">(required)</span>
                </label>
            </div>
            <button
                :disabled="!form.newWord || !form.translation || !form.confirm"
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
        </form>
    </div>
</template>

<style scoped>
.text-danger {
    color: #f44336;
}

select {
  -moz-appearance: none;
  -webkit-appearance: none;
  appearance: none;
  background-image: none;
}
</style>