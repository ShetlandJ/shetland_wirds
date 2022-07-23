<script setup>
import { useForm } from "@inertiajs/inertia-vue3";
import { computed, ref } from "vue";

defineProps({
    pendingWords: {
        type: Array,
        default: () => [],
    },
});

const fields = ["Word", "Definition", "Example sentence", "Type", 'Creator', ""];

const approveForm = useForm({
    wordToApprove: null,
});

const approveWord = (wordId) => {
    approveForm.wordToApprove = wordId;
    approveForm.post(route("approve"), {
        wordToApprove: approveForm.wordToApprove,
    });
};

let showRejectForm = ref(false);

const rejectForm = useForm({
    wordToReject: null,
    rejectReason: null,
});

const rejectWord = (wordId) => {
    rejectForm.wordToReject = wordId;
    rejectForm.post(route("reject"), {
        wordToApprove: rejectForm.wordToApprove,
        rejectReason: rejectForm.rejectReason,
        onFinish: () => {
            showRejectForm.value = false;
            form.reset("rejectForm");
        },
    });
};

const toggleRejectWord = (wordId) => {
    if (wordId === rejectForm.wordToReject) {
        showRejectForm.value = !showRejectForm.value;
        rejectForm.wordToReject = null;
    } else {
        showRejectForm.value = true;
        rejectForm.wordToReject = wordId;
    }
};

const showRejectFormForWord = computed(() => {
    return (wordId) => {
        return showRejectForm.value && wordId === rejectForm.wordToReject;
    }
});

const cellClass = 'px-5 py-5 border-b border-gray-200 bg-white text-sm';
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
                                bg-gray-100
                                text-left text-xs
                                font-semibold
                                text-gray-600
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
                        v-for="pendingWord in pendingWords"
                        :key="pendingWord.id"
                    >
                        <td
                            :class="cellClass"
                        >
                            {{ pendingWord.word }}
                        </td>
                        <td
                            :class="cellClass"
                        >
                            <span v-if="!pendingWord.definitions.length"
                                >-</span
                            >
                            <span v-else>{{
                                pendingWord.definitions[0].definition
                            }}</span>
                        </td>
                        <td
                            :class="cellClass"
                        >
                            <span v-if="!pendingWord.definitions.length"
                                >-</span
                            >
                            <span v-else>{{
                                pendingWord.definitions[0].example_sentence
                            }}</span>
                        </td>

                        <td
                            :class="cellClass"
                        >
                            <span v-if="!pendingWord.definitions.length"
                                >-</span
                            >
                            <span v-else>{{
                                pendingWord.definitions[0].readable_type
                            }}</span>
                        </td>
                        <td
                            :class="cellClass"
                        >
                            {{ pendingWord.creator_name }}
                            <span v-if="pendingWord.creator_word_count > 0">
                                ({{ pendingWord.creator_word_count }} words)
                            </span>
                            <span v-else>(first words)</span>
                        </td>
                        <td
                            :class="cellClass"
                        >
                            <div>
                                <button
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
                                    @click="approveWord(pendingWord.id)"
                                >
                                    Approve
                                </button>
                                <button
                                    v-if="!pendingWord.rejected"
                                    class="
                                        px-4
                                        py-2
                                        bg-yellow-500
                                        hover:bg-yellow-600
                                        text-white text-sm
                                        font-medium
                                        rounded-md
                                    "
                                    @click="toggleRejectWord(pendingWord.id)"
                                >
                                    {{ showRejectForm ? "Cancel" : "Reject" }}
                                </button>
                            </div>

                            <div
                                v-if="
                                    showRejectFormForWord(pendingWord.id)
                                "
                            >
                                <label
                                    for="exampleFormControlTextarea1"
                                    class="
                                        form-label
                                        inline-block
                                        mb-2
                                        mt-4
                                        text-gray-700
                                    "
                                    >Please provide a reject reason</label
                                >
                                <textarea
                                    v-model="rejectForm.rejectReason"
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
                                    id="exampleFormControlTextarea1"
                                    rows="3"
                                    placeholder="Your message"
                                />
                                <div class="flex justify-end">
                                    <button
                                        class="
                                            mt-2
                                            px-4
                                            py-2
                                            bg-red-500
                                            hover:bg-red-600
                                            text-white text-sm
                                            font-medium
                                            rounded-md
                                        "
                                        @click="rejectWord(pendingWord.id)"
                                    >
                                        Reject
                                    </button>
                                </div>
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