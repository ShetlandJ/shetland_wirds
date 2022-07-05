<script setup>
import { usePage } from "@inertiajs/inertia-vue3";
import { computed, ref } from "@vue/runtime-core";

const props = defineProps({
    wordQueue: {
        type: Array,
        default: () => [],
    },
});

const disabledDates = computed(() => {
    return props.wordQueue.map((word) => new Date(word.scheduled_for));
});

const searchString = ref("");
const wordResultsList = ref([]);
const newWordId = ref(null);
const scheduleDate = ref(null);

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

    console.log(usePage().props.value.user);
const addWord = () => {
    axios
        .post("/api/wotd/new", {
            word_id: newWordId.value,
            schedule_date: scheduleDate.value,
            creator_id: usePage().props.value.user.id,
    })
        .then(({ data }) => {
            window.location.reload();
        })
        .catch((error) => {
            console.log(error);
        });
};
</script>

<template>
    <div>
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
                    v-model="newWordId"
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

        <template v-if="newWordId">
            <p class="mb-2">3. Select a date to schedule the word for</p>

            <Datepicker
                v-model="scheduleDate"
                class="w-1/4 mb-4"
                :enable-time-picker="false"
                :disabled-dates="disabledDates"
                :min-date="new Date()"
                format="dd/MM/yyyy"
                placeholder="Please select a date"
            />
        </template>

        <div v-if="scheduleDate">
            <p class="mb-2">4. Add it to the queue!</p>

            <ActionButton @click="addWord">Add</ActionButton>
        </div>
    </div>
</template>