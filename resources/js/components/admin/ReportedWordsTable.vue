<script setup>
import { Inertia } from "@inertiajs/inertia";
import { useForm } from "@inertiajs/inertia-vue3";
import { formatDate } from "../../utils/formatters";

defineProps({
    reports: {
        type: Array,
        default: () => [],
    },
});

const fields = ["Word", "Reason", "Comment", "Date", ""];

const cellClass = "px-5 py-5 border-b border-gray-200 bg-white text-sm";

const reasonList = [
    { text: "Inappropriate word", value: "inappropriate_word_language" },
    {
        text: "Inappropriate language in the definition",
        value: "inappropriate_definition_language",
    },
    {
        text: "Inappropriate language in the example sentence",
        value: "inappropriate_example_sentence_language",
    },
    { text: "Inappropriate comment(s)", value: "inappropriate_comment" },
    { text: "Inappropriate recording(s)", value: "inappropriate_recording" },
    { text: "Other", value: "other" },
];

const findReason = (reason) => {
    const found = reasonList.find((item) => item.value === reason);
    return found ? found.text : "";
};

const form = useForm({
    reportId: null,
})

const resolve = (report) => {
    form.reportId = report.id;
    form.post(
        route("reports.resolve", {
            reportId: report.id,
        }),
    )};
    // form.post(route("reports.resolve", {reportId: form.reportId}), {
    //     onFinish: () => {
    //         form.reset();
    //     },
    // });

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
                    <tr
                        v-for="report in reports"
                        :key="report.id"
                    >
                        <td :class="cellClass">
                            {{ report.word }}
                        </td>

                        <td :class="cellClass">
                            {{ findReason(report.reason_type) }}
                        </td>

                        <td :class="cellClass">
                            {{ report.reason ? report.reason : 'N/A' }}
                        </td>

                        <td :class="cellClass">
                            {{ formatDate(new Date(report.created_at)) }}
                        </td>

                        <td :class="cellClass">
                            <div>
                                <button
                                    @click="resolve(report)"
                                    class="
                                        px-4
                                        py-2
                                        bg-green-500
                                        hover:bg-green-600
                                        text-white text-sm
                                        font-medium
                                        rounded-md
                                        mr-2
                                    "
                                >
                                    Mark as resolved
                                </button>
                            </div>
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