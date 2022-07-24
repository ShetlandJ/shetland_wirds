<script setup>
import { useForm } from "@inertiajs/inertia-vue3";
import Selectable from "../components/Selectable.vue";
const emit = defineEmits(["hide-form", "success"]);

import { useI18n } from "vue-i18n";
const { t } = useI18n();

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
                    class="
                        form-label
                        inline-block
                        mb-2
                        dark:text-white
                        text-gray-700
                    "
                >
                    {{ t("addWord.suggestedLabel") }}
                    <span class="text-danger"
                        >({{ t("global.required") }})</span
                    >
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
                <small
                    id="wordHelp"
                    class="block mt-1 text-xs dark:text-white text-gray-600"
                >
                    {{ t("addWord.suggestedWordDesc") }}
                </small>
            </div>
            <div class="form-group mb-6">
                <label
                    for="translationInput"
                    class="
                        form-label
                        inline-block
                        mb-2
                        dark:text-white
                        text-gray-700
                    "
                >
                    {{ t("addWord.englishLabel") }}
                    <span class="text-danger"
                        >({{ t("global.required") }})</span
                    >
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
                    :placeholder="t('addWord.englishPlaceholder')"
                />
            </div>
            <div class="form-group mb-6">
                <label
                    for="exampleSentenceInput"
                    class="
                        form-label
                        inline-block
                        mb-2
                        dark:text-white
                        text-gray-700
                    "
                >
                    {{ t("addWord.exampleSentenceLabel") }}
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
                <p class="dark:text-white">{{ t("addWord.wordType") }}</p>
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
                            {{ t("addWord.wordTypePlaceholder") }}
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

            <div class="form-group form-check mb-6 flex">
                <input
                    type="checkbox"
                    class="form-check-input mt-2"
                    id="exampleCheck1"
                    v-model="form.confirm"
                />
                <label
                    class="ml-2 form-check-label dark:text-white"
                    for="exampleCheck1"
                >
                    {{ t("addWord.confirm") }}
                    <span class="text-danger"
                        >({{ t("global.required") }})</span
                    >
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
                {{t('global.submit')}}
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