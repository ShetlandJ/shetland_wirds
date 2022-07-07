<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Jetstream/Welcome.vue";
import WordResult from "../components/WordResu\lt.vue";
import StatCard from "../components/StatCard.vue";
import AdminMetrics from "../components/admin/AdminsMetrics.vue";
import PendingRecording from "../components/PendingRecording.vue";
import PendingDefinition from "../components/PendingDefinition.vue";
import WordOfTheDayQueue from '../components/admin/WordOfTheDayQueue.vue';
import EditWord from '../components/admin/EditWord.vue';
import RevisionsList from '../components/admin/RevisionsList.vue';
import { computed } from "vue";

defineProps({
    words: {
        type: Array,
        default: () => [],
    },
    recordings: {
        type: Array,
        default: () => [],
    },
    definitions: {
        type: Array,
        default: () => [],
    },
    metrics: {
        type: Object,
        default: () => ({}),
    },
    wordQueue: {
        type: Array,
        default: () => [],
    },
    revisions: {
        type: Object,
        default: () => null,
    }
});

const heading = computed(() => {
    if (route().current("dashboard")) return "Admin dashboard";
    if (route().current("approval")) return "Words requiring approval";
    if (route().current("word-admin")) return "Word admin";
    if (route().current("rejected")) return "Rejected words";
    if (route().current("recordings")) return "Pending recordings";
    return "Admin dashboard";
});
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ heading }}
            </h2>
        </template>

        <div v-if="words">
            <WordResult
                is-logged-in
                v-for="word in words"
                :key="word.uuid"
                :word="word"
                admin-view
            />
        </div>

        <div v-if="route().current('word-admin')">
            <EditWord />
        </div>

        <div v-if="revisions">
            <RevisionsList :revisions="revisions" />
        </div>

        <div v-if="wordQueue.length > 0">
            <WordOfTheDayQueue :word-queue="wordQueue" />
        </div>

        <div v-if="recordings">
            <PendingRecording
                v-for="(recording, index) in recordings"
                :key="recording.uuid"
                :index="index"
                :recording="recording"
                admin-view
            />
        </div>

        <div v-if="definitions">
            <PendingDefinition
                v-for="(definition, index) in definitions"
                :key="definition.id"
                :index="index"
                :definition="definition"
            />
        </div>

        <div v-if="route().current('dashboard')">
            <AdminMetrics :metrics="metrics" />
        </div>
    </AppLayout>
</template>
