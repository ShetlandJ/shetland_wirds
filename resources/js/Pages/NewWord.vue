<script setup>
import WordResult from "../components/WordResult.vue";
import NavBar from "../components/NavBar.vue";
import { Head, usePage } from "@inertiajs/inertia-vue3";
import NewWordForm from "../components/NewWordForm.vue";

let urlPrev = usePage().props.value.urlPrev;

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

        <NewWordForm
            v-if="!success"
        />

        <Alert
            v-else
            message="Thanks! We'll review your submission over the next few days."
        />
    </div>
</template>