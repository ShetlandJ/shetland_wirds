<script setup>
import { useForm } from "@inertiajs/inertia-vue3";
import Selectable from "../components/Selectable.vue";
const emit = defineEmits(['hide-form'])

const form = useForm({
    newWord: "",
    translation: "",
    example_sentence: "",
    word_type: null,
});

const createWord = () => {
    form.post(route("word.new"), {
        onFinish: () => {
            form.reset();
            emit('hide-form');
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
        <form @submit.prevent="createWord">
            <div class="form-group mb-6">
                <label
                    for="wordInput"
                    class="form-label inline-block mb-2 text-gray-700"
                    >Suggested word</label
                >
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
                <small id="wordHelp" class="block mt-1 text-xs text-gray-600">
                    Enter the new word you wish to add to the dictionary
                </small>
            </div>
            <div class="form-group mb-6">
                <label
                    for="translationInput"
                    class="form-label inline-block mb-2 text-gray-700"
                >
                    What does it mean in English?
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
                    class="form-label inline-block mb-2 text-gray-700"
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
                <p>What type of word is it?</p>
                <div
                    class="flex flex-wrap"
                >
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
                <!-- <label
                    class="form-check-label inline-block text-gray-800"
                    for="exampleCheck1"
                    >Check me out</label
                > -->
            </div>
            <button
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
                "
            >
                Submit
            </button>
        </form>
    </div>
</template>