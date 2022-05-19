<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Jetstream/Welcome.vue";
import WordResult from "../components/WordResult.vue";
import StatCard from "../components/StatCard.vue";
import AdminMetrics from "../components/admin/AdminsMetrics.vue";
import { computed } from "vue";

defineProps({
    words: {
        type: Array,
        default: () => [],
    },
    metrics: {
        type: Object,
        default: () => ({}),
    },
});

const heading = computed(() => {
    if (route().current("dashboard")) return "Admin dashboard";
    if (route().current("approval")) return "Words requiring approval";
    if (route().current("my-words")) return "My words";
    if (route().current('rejected')) return "Rejected words";
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

        <div v-if="route().current('dashboard')">
            <AdminMetrics :metrics="metrics" />
        </div>
    </AppLayout>
</template>