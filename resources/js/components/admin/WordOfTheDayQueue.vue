<script setup>
import { reactive, ref } from "@vue/runtime-core";
import format from "date-fns/format";
import UpdateWordOfTheDayForm from "./UpdateWordOfTheDayForm.vue";
import CreateNewQueuedWord from "./CreateNewQueuedWord.vue";
import { Link } from "@inertiajs/inertia-vue3";

const DATE_FORMAT = "d MMM yy";

defineProps({
    wordQueue: {
        type: Array,
        default: () => [],
    },
});

const changeWordFormShown = ref(false);
const wordOfTheDayToEdit = ref(null);
const showChangeWordForm = (word) => {
    changeWordFormShown.value = true;
    wordOfTheDayToEdit.value = word;
};

const showCreateNewWordForm = ref(false);
</script>

<template>
    <div class="bg-white p-8 rounded-md w-full">
        <div class="flex items-center justify-between pb-6">
            <div>
                <h2 class="text-gray-600 font-semibold">Upcoming words</h2>
            </div>
            <div class="flex items-center justify-between">
                <div class="lg:ml-40 ml-10 space-x-8">
                    <ActionButton @click="showCreateNewWordForm = true">
                        Create
                    </ActionButton>
                </div>
            </div>
        </div>
        <div>
            <CreateNewQueuedWord
                v-if="showCreateNewWordForm"
                :word-queue="wordQueue"
            />
            <UpdateWordOfTheDayForm
                v-if="changeWordFormShown"
                :word-of-the-day-to-edit="wordOfTheDayToEdit"
            />

            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                <div
                    class="
                        inline-block
                        min-w-full
                        shadow
                        rounded-lg
                        overflow-hidden
                    "
                >
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th
                                    v-for="word in [
                                        'Word',
                                        'Scheduled For',
                                        'User',
                                        '',
                                    ]"
                                    :key="word"
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
                                    {{ word }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="queuedWord in wordQueue"
                                :key="queuedWord.id"
                            >
                                <td
                                    class="
                                        px-5
                                        py-5
                                        border-b border-gray-200
                                        bg-white
                                        text-sm
                                    "
                                >
                                    <Link
                                        :href="
                                            route('word.comments', {
                                                slug: queuedWord.slug,
                                            })
                                        "
                                        class="
                                            text-gray-700
                                            dark:text-white
                                            underline
                                        "
                                    >
                                        {{ queuedWord.word }}
                                    </Link>
                                </td>
                                <td
                                    class="
                                        px-5
                                        py-5
                                        border-b border-gray-200
                                        bg-white
                                        text-sm
                                    "
                                >
                                    <span v-if="queuedWord.scheduled_for">{{
                                        format(
                                            new Date(queuedWord.scheduled_for),
                                            DATE_FORMAT
                                        )
                                    }}</span>
                                </td>
                                <td
                                    class="
                                        px-5
                                        py-5
                                        border-b border-gray-200
                                        bg-white
                                        text-sm
                                    "
                                >
                                    {{ queuedWord.creator }}
                                </td>
                                <td
                                    class="
                                        px-5
                                        py-5
                                        border-b border-gray-200
                                        bg-white
                                        text-sm
                                    "
                                >
                                    <button
                                        class="underline"
                                        @click="showChangeWordForm(queuedWord)"
                                    >
                                        Change
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>