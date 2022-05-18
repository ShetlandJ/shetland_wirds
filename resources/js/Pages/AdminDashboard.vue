<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Jetstream/Welcome.vue";
import WordResult from "../components/WordResult.vue";
import StatCard from "../components/StatCard.vue";
import { computed } from "vue";

defineProps({
    words: {
        type: Array,
        default: () => [],
    },
});

const heading = computed(() => {
    if (route().current("dashboard")) return "Admin dashboard";
    if (route().current("approval")) return "Words requiring approval";
    return "Rejected words";
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
            <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
                <div class="p-4 flex items-center">
                    <StatCard
                        class="m-4"
                        v-for="stat in [1, 2, 3]"
                        :key="stat"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
