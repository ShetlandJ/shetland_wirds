<script setup>
import { useForm } from "@inertiajs/inertia-vue3";
import { computed, ref } from "vue";
import { formatDate } from "../../utils/formatters";

const props = defineProps({
    words: {
        type: Array,
        default: () => [],
    },
    hideControls: {
        type: Boolean,
        default: false,
    },
});

const fields = computed(() => {
    let fieldList = [
        "Word",
        "Definition",
        "Example sentence",
        "Type",
        "Creator",
        "Date",
    ];

    if (props.hideControls) return fieldList;

    return [...fieldList, ""];
});

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
let showApproveForm = ref(false);

const rejectForm = useForm({
    wordUuid: null,
    rejectReason: null,
});

const peerForm = useForm({
    wordUuid: null,
    reason: null,
    approved: false,
});

const rejectWord = (wordId) => {
    rejectForm.wordUuid = wordId;
    rejectForm.post(route("peer.reject"), {
        wordToApprove: rejectForm.wordToApprove,
        rejectReason: rejectForm.rejectReason,
        onFinish: () => {
            showRejectForm.value = false;
            form.reset("rejectForm");
        },
    });
};

const peerVote = (wordId, approved) => {
    peerForm.wordUuid = wordId;
    peerForm.approved = approved;
    peerForm.post(route("word.peer.review"), {
        wordUuid: peerForm.wordUuid,
        reason: peerForm.reason,
        approved: peerForm.approved,
        onFinish: () => {
            showRejectForm.value = false;
            showApproveForm.value = false;
            form.reset("peerForm");
        },
    });
};

const toggleRejectWord = (wordId) => {
    showApproveForm.value = false;
    if (wordId === rejectForm.wordUuid) {
        showRejectForm.value = !showRejectForm.value;
    } else {
        showRejectForm.value = true;
        rejectForm.wordUuid = wordId;
    }
};

const toggleApproveWord = (wordId) => {
    showRejectForm.value = false;
    if (wordId === peerForm.wordUuid) {
        showApproveForm.value = !showApproveForm.value;
    } else {
        showApproveForm.value = true;
        peerForm.wordUuid = wordId;
    }
};

const showApproveFormForWord = computed(() => {
    return (wordId) => {
        return showApproveForm.value && wordId === peerForm.wordUuid;
    };
});

const showRejectFormForWord = computed(() => {
    return (wordId) => {
        return showRejectForm.value && wordId === peerForm.wordUuid;
    };
});

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
                    <tr v-for="pendingWord in words" :key="pendingWord.id">
                        <td :class="cellClass">
                            <div>
                                {{ pendingWord.word }}
                            </div>
                            <template v-if="pendingWord.votes">
                                <div class="text-green-600">
                                    {{ pendingWord.votes.approved }} for
                                </div>
                                <div class="text-red-600">
                                    {{ pendingWord.votes.rejected }} against
                                </div>
                            </template>
                        </td>
                        <td :class="cellClass">
                            <span v-if="!pendingWord.definitions.length"
                                >-</span
                            >
                            <span v-else>{{
                                pendingWord.definitions[0].definition
                            }}</span>
                        </td>
                        <td :class="cellClass">
                            <span v-if="!pendingWord.definitions.length"
                                >-</span
                            >
                            <span v-else>{{
                                pendingWord.definitions[0].example_sentence
                            }}</span>
                        </td>

                        <td :class="cellClass">
                            <span v-if="!pendingWord.definitions.length"
                                >-</span
                            >
                            <span v-else>{{
                                pendingWord.definitions[0].readable_type
                            }}</span>
                        </td>
                        <td :class="cellClass">
                            {{ pendingWord.creator_name }}
                            <span v-if="pendingWord.creator_word_count > 0">
                                ({{ pendingWord.creator_word_count }} word{{
                                    pendingWord.creator_word_count > 1
                                        ? "s"
                                        : ""
                                }})
                            </span>
                            <span v-else>(first words)</span>
                        </td>

                        <td :class="cellClass">
                            {{ formatDate(new Date(pendingWord.created_at)) }}
                        </td>
                        <td v-if="!hideControls" :class="cellClass">
                            <div>
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
                                        @click="
                                            toggleApproveWord(pendingWord.id)
                                        "
                                    >
                                        {{
                                            showApproveForm
                                                ? "Cancel"
                                                : "Vote to approve"
                                        }}
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
                                        @click="
                                            toggleRejectWord(pendingWord.id)
                                        "
                                    >
                                        {{
                                            showRejectForm
                                                ? "Cancel"
                                                : "Vote to reject"
                                        }}
                                    </button>
                                </div>
                            </div>

                            <div v-if="showApproveFormForWord(pendingWord.id)">
                                <label
                                    for="exampleFormControlTextarea1"
                                    class="
                                        form-label
                                        inline-block
                                        mb-2
                                        mt-4
                                        text-gray-700
                                    "
                                    >Optionally provide an approval
                                    reason</label
                                >
                                <textarea
                                    v-model="peerForm.reason"
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
                                            mr-2
                                            bg-green-800
                                            hover:bg-green-700
                                            text-white text-sm
                                            font-medium
                                            rounded-md
                                        "
                                        @click="approveWord(pendingWord.id, true)"
                                    >
                                        Add
                                    </button>

                                    <button
                                        class="
                                            mt-2
                                            px-4
                                            py-2
                                            bg-green-500
                                            hover:bg-green-600
                                            text-white text-sm
                                            font-medium
                                            rounded-md
                                        "
                                        @click="peerVote(pendingWord.id, true)"
                                    >
                                        Vote to approve
                                    </button>
                                </div>
                            </div>
                            <div v-if="showRejectFormForWord(pendingWord.id)">
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
                                    v-model="peerForm.reason"
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
                                            mr-2
                                            bg-red-800
                                            hover:bg-red-700
                                            text-white text-sm
                                            font-medium
                                            rounded-md
                                        "
                                        @click="rejectWord(pendingWord.id, true)"
                                    >
                                        Reject
                                    </button>

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
                                        @click="peerVote(pendingWord.id, false)"
                                    >
                                        Vote to reject
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