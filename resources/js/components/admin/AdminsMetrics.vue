<script setup>
import StatCard from "../StatCard.vue";
import MostViewed from './MostViewedWords.vue';
import MostSearched from './MostSearched.vue';

defineProps({
    metrics: {
        type: Object,
        default: () => ({}),
    },
});
</script>

<template>
    <div>
        <div>
            <h1 class="px-4 pt-4 m-4 mb-0 text-4xl">Most viewed (last 7 days)</h1>
            <div class="grid gap-6 mb-2">
                <div class="p-4 flex items-center">
                    <MostViewed :most-viewed="metrics.topViewedPages" />
                    <MostSearched :most-searched="metrics.topSearches" />
                </div>
            </div>
        </div>

        <div>
            <h1 class="px-4 pt-4 m-4 mb-0 text-4xl">Highlight metrics</h1>
            <div class="grid gap-6 mb-2">
                <div class="p-4 flex items-center">
                    <StatCard
                        class="m-4"
                        v-for="stat in metrics.headline"
                        :key="stat"
                        :name="stat.name"
                        :stat="stat.value"
                        :type="stat.type"
                    />
                </div>
            </div>
        </div>

        <div class="mb-2">
            <h1 class="px-4 pt-4 m-4 mb-0 text-4xl">Recent</h1>
            <div class="grid gap-6 mb-2">
                <div class="p-4 flex items-center">
                    <StatCard
                        class="m-4"
                        v-for="stat in metrics.recent"
                        :key="stat"
                        :name="stat.name"
                        :stat="stat.value"
                        :type="stat.type"
                    />
                </div>
            </div>
        </div>

        <div class="mb-2">
            <h1 class="px-4 pt-4 m-4 mb-0 text-4xl">All time</h1>
            <div class="grid gap-6 mb-2">
                <div class="p-4 flex items-center">
                    <StatCard
                        class="m-4"
                        v-for="stat in metrics.allTime"
                        :key="stat"
                        :name="stat.name"
                        :stat="stat.value"
                        :type="stat.type"
                    />
                </div>
            </div>
        </div>
    </div>
</template>