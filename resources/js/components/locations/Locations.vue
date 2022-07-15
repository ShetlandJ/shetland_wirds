<script setup>
import { usePage } from "@inertiajs/inertia-vue3";
import { onMounted, ref } from "vue";
import LocationInput from "./LocationInput.vue";
const { isLoggedIn, userSelectedLocations, locations, word } =
    usePage().props.value;

let showAddForm = ref(true);

const toggleShowAddForm = () => (showAddForm.value = !showAddForm.value);

onMounted(() => {
    if (userSelectedLocations.length > 0) {
        showAddForm.value = false;
    }
});
</script>

<template>
    <div v-if="word">
        <p class="mb-2 dark:text-white" v-if="isLoggedIn">
            Where in Shetland is this word spoken?</p>

        <p class="mb-2 dark:text-white">
            We are gathering data and
            will display a heatmap when we receive enough information to make
            the data useful!
        </p>
        <p class="mt-4" v-if="showAddForm">{{userSelectedLocations.length > 0 ? 'Edit your entries:' : 'Add your own below:'}}</p>

        <div
            v-if="!showAddForm && userSelectedLocations.length > 0"
            class="dark:text-white"
        >
            You have previously selected locations for <b>{{ word.word }}</b
            >. If you would like to amend your selection, please click
            <button class="underline" @click="toggleShowAddForm">here</button>.
        </div>

        <LocationInput
            v-else-if="showAddForm"
            :user-selected-locations="userSelectedLocations"
            :locations="locations"
            :word="word"
        />
    </div>
</template>