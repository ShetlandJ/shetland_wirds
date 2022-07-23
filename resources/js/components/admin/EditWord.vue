<script setup>
import { usePage } from "@inertiajs/inertia-vue3";
import { computed, onMounted, ref } from "vue";

const searchString = ref("");
const linkSearchString = ref("");
const wordResultsList = ref([]);
const linkedWordResultsList = ref([]);

const showSuccessMessage = ref(false);
const userId = usePage().props.value.user?.uuid;
const { wordRelationTypes } = usePage().props.value;

const searchWords = (firstSearch = true) => {
    axios
        .get("/api/words", {
            params: {
                search: firstSearch
                    ? searchString.value
                    : linkSearchString.value,
            },
        })
        .then(({ data }) => {
            if (firstSearch) wordResultsList.value = data;
            else linkedWordResultsList.value = data;
        })
        .catch((error) => {
            console.log(error);
        });
};

const word = ref(null);
const wordLinks = ref([]);

const getWord = (uuid) => {
    axios
        .get("/api/word", {
            params: {
                uuid,
            },
        })
        .then(({ data }) => {
            console.log(data);
            word.value = data;
        })
        .then(() => {
            wordLinks.value = word.value.linked_words;
        })
        .catch((error) => {
            console.log(error);
        });
};

const updateWord = () => {
    const payload = {
        id: word.value.id,
        word: word.value.word,
        userId: userId,
        definitions:
            word.value.definitions?.map((definition) => ({
                id: definition.id,
                definition: definition.definition,
            })) || [],
        newDefinitions: newDefinitions.value,
        removedDefinitions: removedDefinitions.value,
        wordLinks: wordLinks.value,
    };

    axios
        .post("/api/word", {
            payload,
        })
        .then(({ data }) => {
            word.value = null;
            searchString.value = "";
            wordResultsList.value = [];
            newDefinitions.value = [];
            removedDefinitions.value = [];
            showSuccessMessage.value = true;
        })
        .catch((error) => {
            console.log(error);
        });
};

const setLinkedWord = (value) => {
    const word = linkedWordResultsList.value.find((word) => word.id === value);

    wordLinks.value.push({
        ...word,
        wordRelationType: wordRelationType.value,
        type: wordRelationTypes.find(
            (type) => type.uuid === wordRelationType.value
        ).title,
    });
    linkSearchString.value = "";
    linkedWordResultsList.value = [];
    const input = document.getElementById("linkedWordSelect");
    input.value = "";
    const typesInput = document.getElementById("wordRelationTypesSelect");
    typesInput.value = "";
};

const wordRelationType = ref("");
const setWordRelationType = (value) => {
    wordRelationType.value = value;
};

onMounted(() => {
    const synonym = wordRelationTypes.find(
        (wordRelationType) => wordRelationType.name === "synonym"
    );
    setWordRelationType(synonym.uuid);
});

// remove link from wordLinks array
const removeLink = (value) => {
    const index = wordLinks.value.findIndex((word) => word.id === value);
    wordLinks.value.splice(index, 1);
};

const newDefinitions = ref([]);

const addNewDefinition = () => {
    newDefinitions.value.push({
        definition: "",
        id: newDefinitions.value.length + 1,
    });
};

const removedDefinitions = ref([]);

const removeExistingDefinition = (value) => {
    removedDefinitions.value.push(value);
};

const removeNewDefinition = (value) => {
    const index = newDefinitions.value.findIndex(
        (definition) => definition.id === value
    );
    newDefinitions.value.splice(index, 1);
};

const definitionsWithoutRemovedOnes = computed(() => {
    return word.value.definitions.filter(
        (definition) => !removedDefinitions.value.includes(definition.id)
    );
})
</script>

<template>
    <Container>
        <Alert
            class="mb-4"
            v-if="showSuccessMessage"
            variant="success"
            message="Word successfully updated"
        />

        <p class="mb-2">
            1. Search for the word you want to edit.
        </p>

        <div class="flex mb-4">
            <input type="search" v-model="searchString" />
            <ActionButton @click="searchWords(true)">Search</ActionButton>
        </div>

        <div
            :key="wordResultsList.length"
            v-if="wordResultsList.length > 0"
            class="mb-4"
        >
            <p class="mb-2">2. Select a word from the list</p>

            <div class="flex">
                <select
                    class="
                        w-1/2
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
                v-for="(definition, index) in definitionsWithoutRemovedOnes"
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
                <div class="flex">
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
                    <button
                        class="p-4 text-red-700 items-center"
                        @click="removeExistingDefinition(definition.id)"
                    >
                        X
                    </button>
                </div>
            </div>

            <div v-if="newDefinitions">
                <div
                    class="form-group mb-6"
                    v-for="(definition, index) in newDefinitions"
                    :key="definition.id"
                >
                    <label
                        :for="`newDefinitionInput-${index}`"
                        class="
                            form-label
                            inline-block
                            mb-2
                            dark:text-white
                            text-gray-700
                        "
                    >
                        New definition {{ index + 1 }}:
                    </label>
                    <div class="flex">
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
                            :id="`newDefinitionInput-${index}`"
                        />
                        <button
                            class="p-4 text-red-700 items-center"
                            @click="removeNewDefinition(definition.id)"
                        >
                            X
                        </button>
                    </div>
                </div>
            </div>

            <button class="underline mb-4" @click="addNewDefinition">
                + Add a new definition
            </button>

            <div class="mb-4">
                <p class="mb-2">Find and add any linked words</p>

                <div class="flex mb-4">
                    <input type="search" v-model="linkSearchString" />
                    <ActionButton @click="searchWords(false)"
                        >Search</ActionButton
                    >
                </div>

                <div class="flex" v-if="linkedWordResultsList.length > 0">
                    <select
                        id="linkedWordSelect"
                        class="
                            w-1/2
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
                        @input="(event) => setLinkedWord(event.target.value)"
                    >
                        <option disabled selected :value="null">
                            Select new word
                        </option>

                        <option
                            v-for="(word, index) in linkedWordResultsList"
                            :key="index"
                            class="h1"
                            :value="word.id"
                        >
                            {{ word.word }}
                        </option>
                    </select>
                    <select
                        id="wordRelationTypesSelect"
                        v-if="wordRelationTypes.length > 0"
                        class="
                            ml-4
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
                        @input="(event) => setRelationType(event.target.value)"
                    >
                        <option
                            v-for="(type, index) in wordRelationTypes"
                            :key="index"
                            class="h1"
                            :value="type.uuid"
                        >
                            {{ type.title }}
                        </option>
                    </select>
                </div>

                <div v-if="wordLinks.length" class="flex items-center mt-4">
                    <span class="mr-2">Links:</span>
                    <div
                        v-for="link in wordLinks"
                        :key="link"
                        class="
                            flex
                            justify-center
                            items-center
                            m-1
                            font-medium
                            py-1
                            px-2
                            bg-white
                            rounded-full
                            text-white
                            bg-green-500
                        "
                    >
                        <div
                            class="
                                text-md
                                font-normal
                                leading-none
                                max-w-full
                                flex-initial
                            "
                        >
                            {{ link.word.replace(" (exact match)", "") }}
                            <span class="text-sm text-white ml-1"
                                >({{ link.type }})</span
                            >
                        </div>
                        <div class="flex flex-auto flex-row-reverse">
                            <div>
                                <svg
                                    @click="removeLink(link.id)"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="100%"
                                    height="100%"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="
                                        feather feather-x
                                        cursor-pointer
                                        hover:text-red-400
                                        rounded-full
                                        w-4
                                        h-4
                                        ml-2
                                    "
                                >
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else-if="word && !wordLinks.length">
                    There are no linked words for {{ word.word }}
                </div>
            </div>

            <ActionButton @click="updateWord" message="Update" />
        </div>
    </Container>
</template>