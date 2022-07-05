<script setup>
import { ref } from "vue";
import format from "date-fns/format";
const DATE_FORMAT = "d MMM yy";
import { Inertia } from "@inertiajs/inertia";

const props = defineProps({
    wordOfTheDayToEdit: {
        type: Object,
        default: null,
    },
});

const wordResultsList = ref([]);
const searchString = ref("");
const replacementWordId = ref(null);

const searchWords = () => {
    axios
        .get("/api/wotd", {
            params: {
                search: searchString.value,
            },
        })
        .then(({ data }) => {
            wordResultsList.value = data;
        })
        .catch((error) => {
            console.log(error);
        });
};

const replaceWord = () => {
    axios
        .post("/api/wotd/replace", {
            id: props.wordOfTheDayToEdit.id,
            replacement_word_id: replacementWordId.value,
        })
        .then(() => {
            Inertia.visit("wotd", {
                only: ["wordQueue"],
            });
        })
        .catch((error) => {
            console.log(error);
        });
};
</script>

<template>
    <div>
        <p class="mb-2">
            Update the Word Of The Day <b>{{ wordOfTheDayToEdit.word }}</b> for
            <b
                >{{
                    format(
                        new Date(wordOfTheDayToEdit.scheduled_for),
                        DATE_FORMAT
                    )
                }}
            </b>
        </p>

        <p class="mb-2">
            1. First search for the word you want to replace
            <b>{{ wordOfTheDayToEdit.word }}</b> with. Note that if your
            preferred word has been used in the 90 days, it will not return in
            these results
        </p>

        <div class="flex mb-4">
            <input type="search" v-model="searchString" />
            <ActionButton @click="searchWords">Search</ActionButton>
        </div>

        <div
            :key="wordResultsList.length"
            v-if="wordResultsList.length > 0"
            class="mb-4"
        >
            <p class="mb-2">
                2. Select the replacement word from the below list
            </p>

            <div class="flex">
                <select
                    v-model="replacementWordId"
                    class="
                        w-1/4
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
                        Select replacement word
                    </option>

                    <option
                        v-for="(word, index) in wordResultsList"
                        :key="index"
                        class="h1"
                        :value="word.id"
                    >
                        {{ word.word }}
                    </option>
                </select>
            </div>
        </div>

        <div v-if="replacementWordId">
            <div>
                <p class="mb-2">3. Click to save the replacement</p>

                <ActionButton @click="replaceWord">Replace</ActionButton>
            </div>
        </div>
    </div>
</template>