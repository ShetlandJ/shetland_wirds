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
})
</script>

<template>
    <div v-if="word">
        <p class="mb-2 dark:text-white" v-if="isLoggedIn">
            Where in Shetland is this word spoken? <span v-if="showAddForm">Add your own below or&nbsp;</span>
            <button v-if="showAddForm" @click="toggleShowAddForm" class="underline">
                see chart
            </button>
            <button v-else-if="!showAddForm && userSelectedLocations.length === 0" @click="toggleShowAddForm" class="underline">
                Add your own
            </button>
        </p>

        <div v-if="!showAddForm && userSelectedLocations.length > 0" class="dark:text-white">
            You have previously selected locations for <b>{{ word.word }}</b>. If you
            would like to amend your selection, please click
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