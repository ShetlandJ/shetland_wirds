<script setup>
import { usePage } from "@inertiajs/inertia-vue3";
import { ref } from "vue";
import LocationInput from "./LocationInput.vue";
const { isLoggedIn, userSelectedLocations, locations, word } =
    usePage().props.value;

let showAddForm = ref(false);

const toggleShowAddForm = () => (showAddForm.value = !showAddForm.value);
</script>

<template>
    <div v-if="word">
        <p class="mb-2" v-if="isLoggedIn">
            Where in Shetland is this word spoken? <span v-if="showAddForm">Add your own below or&nbsp;</span>
            <button v-if="showAddForm" @click="toggleShowAddForm" class="underline">
                see chart
            </button>
        </p>

        <div v-if="!showAddForm">
            You have previously selected locations for <b>{{ word.word }}</b>. If you
            would like to amend your selection, please click
            <button class="underline" @click="toggleShowAddForm">here</button>.
        </div>

        <LocationInput
            v-else
            :user-selected-locations="userSelectedLocations"
            :locations="locations"
            :word="word"
        />
    </div>
</template>