<script setup>
import { useForm } from "@inertiajs/inertia-vue3";
import { formatDate } from "../../utils/formatters";
import { Link } from "@inertiajs/inertia-vue3";

defineProps({
    comments: {
        type: Array,
        default: () => [],
    },
});

const fields = ["Word", "Text", "User", "Date"];

const cellClass = "px-5 py-5 border-b border-gray-200 bg-white text-sm";
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
                    <tr v-for="comment in comments" :key="comment.id">
                        <td :class="cellClass">
                            <Link
                                :href="
                                    route('word.comments', {
                                        slug: comment.word_slug,
                                    })
                                "
                                class="text-gray-700 underline"
                            >
                                {{ comment.word }}
                            </Link>
                        </td>
                        <td :class="cellClass">
                            <SanitisedHtml :html-string="comment.message" />
                        </td>
                        <td :class="cellClass">
                            {{ comment.user }}
                        </td>
                        <td :class="cellClass">
                            {{ formatDate(new Date(comment.created_at)) }}
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