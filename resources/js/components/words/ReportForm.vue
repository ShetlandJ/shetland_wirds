<script setup>
import { useForm } from "@inertiajs/inertia-vue3";

import { useI18n } from "vue-i18n";
const { t } = useI18n();

const props = defineProps({
    word: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["success"]);

const reportForm = useForm({
    reason_type: null,
    report_reason: null,
});

const reasonList = [
    {
        text: t("word.reporting.inappropriateWord"),
        value: "inappropriate_word_language",
    },
    {
        text: t("word.reporting.inappropriateDefinition"),
        value: "inappropriate_definition_language",
    },
    {
        text: t("word.reporting.inappropriateExampleSentence"),
        value: "inappropriate_example_sentence_language",
    },
    {
        text: t("word.reporting.inappropriateComments"),
        value: "inappropriate_comment",
    },
    {
        text: t("word.reporting.inappropriateRecordings"),
        value: "inappropriate_recording",
    },
    { text: t("word.reporting.other"), value: "other" },
];

const reportWord = () => {
    reportForm.wordId = props.word.id;
    reportForm.post(route("word.report", { word: props.word.slug }), {
        onFinish: () => {
            reportForm.reset();
            emit("success");
        },
    });
};
</script>

<template>
    <form @submit.prevent="reportWord">
        <p class="dark:text-white">{{ t("word.reporting.issueNature") }}</p>
        <div>
            <select
                v-model="reportForm.reason_type"
                class="
                    w-full
                    px-3
                    py-1.5
                    font-normal
                    text-gray-700
                    bg-white bg-clip-padding
                    border border-solid border-gray-300
                    rounded
                    transition
                    ease-in-out
                    m-0
                    focus:text-gray-700
                    focus:bg-white
                    focus:border-blue-600
                    focus:outline-none
                "
            >
                <option disabled selected :value="null">
                    {{ t("global.select") }}
                </option>
                <option
                    v-for="(reason, index) in reasonList"
                    :key="index"
                    class="h1"
                    :value="reason.value"
                >
                    {{ reason.text }}
                </option>
            </select>
        </div>

        <div class="mt-3" v-if="reportForm.reason_type === 'other'">
            <label
                for="reportForm-reportReason"
                class="form-label inline-block mb-2 mt-4 text-gray-700"
            >
                <i18n-t keypath="word.reporting.whatIsTheIssue">
                    <template #word>
                        {{ word.word }}?
                    </template>
                </i18n-t>
            </label>
            <textarea
                v-model="reportForm.report_reason"
                class="
                    form-control
                    block
                    w-full
                    px-3
                    py-1.5
                    text-base
                    font-normal
                    text-gray-700
                    bg-white bg-clip-padding
                    border border-solid border-gray-300
                    rounded
                    transition
                    ease-in-out
                    m-0
                    focus:text-gray-700
                    focus:bg-white
                    focus:border-blue-600
                    focus:outline-none
                "
                id="reportForm"
                rows="3"
                :placeholder="t('word.reporting.yourMessage')"
            />
        </div>

        <ActionButton
            v-if="reportForm.reason_type"
            @click="reportWord"
            type="submit"
            class="mt-4"
        >
            {{t('word.report')}}
        </ActionButton>
    </form>
</template>