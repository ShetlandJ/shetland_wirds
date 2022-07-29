<script setup>
import { useForm } from "@inertiajs/inertia-vue3";
import { computed, ref } from "vue";
import { Link } from "@inertiajs/inertia-vue3";

defineProps({
    mostViewed: {
        type: Array,
        default: () => [],
    },
});

const fields = ["Word", "Count"];

const cellClass = "px-1 py-1 border-b border-gray-200 bg-white text-md";
</script>

<template>
    <div class="mx-4 mx-8 px-4 px-8 py-4 overflow-x-auto">
        <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            v-for="field in fields"
                            :key="field"
                            class="
                                px-5
                                py-3
                                border-b-2 border-gray-200
                                bg-blue-200
                                text-left text-xs
                                font-semibold
                                text-gray-800
                                uppercase
                                tracking-wider
                            "
                        >
                            {{ field }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="word in mostViewed" :key="word.id">
                        <td :class="cellClass">
                            <Link
                                :href="
                                    route('word.comments', {
                                        slug: word.slug,
                                    })
                                "
                                class="text-gray-700 dark:text-white underline"
                            >
                                {{ word.word }}
                            </Link>
                        </td>
                        <td :class="cellClass">
                            {{ word.count }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style>
td {
    vertical-align: top;
    text-align: left;
}
</style>