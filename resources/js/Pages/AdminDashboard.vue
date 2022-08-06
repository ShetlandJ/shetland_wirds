<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Jetstream/Welcome.vue";
import StatCard from "../components/StatCard.vue";
import AdminMetrics from "../components/admin/AdminsMetrics.vue";
import PendingDefinition from "../components/PendingDefinition.vue";
import WordOfTheDayQueue from "../components/admin/WordOfTheDayQueue.vue";
import EditWord from "../components/admin/EditWord.vue";
import RevisionsList from "../components/admin/RevisionsList.vue";
import PendingWordsTable from '../components/admin/PendingWordsTable.vue';
import PendingRecordingsTable from '../components/admin/PendingRecordingsTable.vue';
import UsersTable from '../components/admin/UsersTable.vue';
import ReportedWordsTable from '../components/admin/ReportedWordsTable.vue';
import LatestCommentsTable from '../components/admin/LatestCommentsTable.vue';

import { computed } from "vue";

defineProps({
    pendingWords: {
        type: Array,
        default: () => [],
    },
    pendingRecordings: {
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
    },
    wordRelationTypes: {
        type: Array,
        default: () => [],
    },
    users: {
        type: Array,
        default: () => [],
    },
    roles: {
        type: Array,
        default: () => [],
    },
    reports: {
        type: Array,
        default: () => [],
    },
    latestWords: {
        type: Array,
        default: () => [],
    },
    latestRecordings: {
        type: Array,
        default: () => [],
    },
    latestComments: {
        type: Array,
        default: () => [],
    }
});

const heading = computed(() => {
    if (route().current("dashboard")) return "Admin dashboard";
    if (route().current("approval")) return "Words requiring approval";
    if (route().current("word-admin")) return "Word admin";
    if (route().current("rejected")) return "Rejected words";
    if (route().current("recordings")) return "Pending recordings";
    if (route().current("users")) return "User management";
    if (route().current("reports")) return "Reported words";
    if (route().current("latest.words")) return "Latest words";
    if (route().current("latest.recordings")) return "Latest recordings";
    if (route().current("latest.comments")) return "Latest comments";
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

        <div v-if="route().current('approval')">
            <PendingWordsTable :words="pendingWords" />
        </div>

        <div v-if="route().current('word-admin')">
            <EditWord />
        </div>

        <div v-if="route().current('latest.words')">
            <PendingWordsTable :words="latestWords" hide-controls />
        </div>

        <div v-if="route().current('latest.comments')">
            <LatestCommentsTable :comments="latestComments" />
        </div>

        <div v-if="route().current('latest.recordings')">
            <PendingRecordingsTable :recordings="latestRecordings" hide-controls />
        </div>

        <div v-if="revisions">
            <RevisionsList :revisions="revisions" />
        </div>

        <div v-if="wordQueue.length > 0">
            <WordOfTheDayQueue :word-queue="wordQueue" />
        </div>

        <div v-if="route().current('recordings')">
            <PendingRecordingsTable :pending-recordings="pendingRecordings" />
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

        <div v-if="route().current('users')">
            <UsersTable :users="users" :roles="roles" />
        </div>

        <ReportedWordsTable v-if="route().current('reports')" :reports="reports" />
    </AppLayout>
</template>
