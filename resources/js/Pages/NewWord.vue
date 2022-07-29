<script setup>
import WordResult from "../components/WordResult.vue";
import NavBar from "../components/NavBar.vue";
import { Head, usePage } from "@inertiajs/inertia-vue3";
import NewWordForm from "../components/NewWordForm.vue";
import { computed, ref } from "@vue/runtime-core";

let urlPrev = usePage().props.value.urlPrev;
const errorBags = computed(() => {
    return usePage().props.value?.errorBags?.default
});

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    isLoggedIn: Boolean,
    success: Boolean
});

const goBack = () => window.history.back();
</script>

<template>
    <Head title="Suggest a new word" />

    <div
        class="
            items-top
            justify-center
            min-h-screen
            bg-gray-100
            dark:bg-gray-900
            sm:items-center sm:pt-0
        "
    >
        <NavBar
            :can-login="canLogin"
            :can-register="canRegister"
            :is-logged-in="isLoggedIn"
            @set-search="searchString = $event"
        />

        <Alert
            v-if="errorBags && errorBags.newWord"
            variant="warning"
        >
            <SanitisedHtml :html-string="errorBags.newWord[0]" />
        </Alert>

        <NewWordForm
            v-if="!success"
        />

        <Alert
            v-else
            :message="t('addWord.thanks')"
        />
    </div>
</template>