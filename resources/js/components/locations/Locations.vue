<script setup>
import { usePage } from "@inertiajs/inertia-vue3";
import { onMounted, ref } from "vue";
import LocationInput from "./LocationInput.vue";
const { isLoggedIn, userSelectedLocations, locations, word } =
    usePage().props.value;

import { useI18n } from "vue-i18n";
const { t } = useI18n();

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
            {{t('word.locations.whereInShetland')}}
        </p>

        <p class="mb-2 dark:text-white">
            {{t('word.locations.dataGathering')}}
        </p>

        <p class="mt-4 dark:text-white" v-if="showAddForm && isLoggedIn">
            {{
                userSelectedLocations.length > 0
                    ? `${t('word.locations.editEntries')}:`
                    : `${t('word.locations.addEntries')}:`
            }}
        </p>

        <div
            v-if="!showAddForm && userSelectedLocations.length > 0"
            class="dark:text-white"
        >
            <i18n-t keypath="word.locations.previous" type="span">
                <template #word>
                   <b>{{word.word}}</b>
                </template>

                <template #here>
                    <button class="underline" @click="toggleShowAddForm">{{t('global.here')}}</button>.
                </template>
            </i18n-t>
        </div>

        <LocationInput
            v-else-if="showAddForm"
            :user-selected-locations="userSelectedLocations"
            :locations="locations"
            :word="word"
        />
    </div>
</template>