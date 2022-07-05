<script setup>
import { ref } from "vue";

const searchString = ref("");
const wordResultsList = ref([]);

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

const wordId = ref(null);
const word = ref(null);

const getWord = (uuid) => {
    axios
        .get("/api/word", {
            params: {
                uuid,
            },
        })
        .then(({ data }) => {
            word.value = data;
        })
        .catch((error) => {
            console.log(error);
        });
};

const updateWord = () => {
    const payload = {
        id: word.value.id,
        word: word.value.word,
        definitions:
            word.value.definitions?.map((definition) => ({
                id: definition.id,
                definition: definition.definition,
            })) || [],
    };

    axios
        .post("/api/word", {
            payload,
        })
        .then(({ data }) => {
            word.value = data;
        })
        .catch((error) => {
            console.log(error);
        });
};
</script>

<template>
    <div
        className="bg-white shadow-lg rounded-lg mx-4 p-2 md:mx-auto my-8 max-w-lg md:max-w-2xl"
    >
        <p class="mb-2">
            1. First search for the word you want to add. Note that if your
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
            <p class="mb-2">2. Select a word from the list</p>

            <div class="flex">
                <select
                    v-model="wordId"
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
                    @input="(event) => getWord(event.target.value)"
                >
                    <option disabled selected :value="null">
                        Select new word
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

        <div v-if="word" class="mt-4">
            <hr />
            <h4 class="text-lg mt-6 mb-2">
                Editing <b>{{ word.word }}</b>
            </h4>

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
                    Word:
                </label>
                <input
                    v-model="word.word"
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
                />
            </div>

            <div
                class="form-group mb-6"
                v-for="(definition, index) in word.definitions"
                :key="definition.id"
            >
                <label
                    :for="`definitionInput-${index}`"
                    class="
                        form-label
                        inline-block
                        mb-2
                        dark:text-white
                        text-gray-700
                    "
                >
                    Definition {{ index + 1 }}:
                </label>
                <input
                    v-model="definition.definition"
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
                    :id="`definitionInput-${index}`"
                />
            </div>

            <ActionButton @click="updateWord" message="Update" />
        </div>
    </div>
</template>