<script setup>
import format from "date-fns/format";

const DATE_FORMAT = "d MMM yy";

defineProps({
    wordQueue: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div class="bg-white p-8 rounded-md w-full">
        <div class="flex items-center justify-between pb-6">
            <div>
                <h2 class="text-gray-600 font-semibold">Upcoming words</h2>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex bg-gray-50 items-center p-2 rounded-md"></div>
                <div class="lg:ml-40 ml-10 space-x-8">
                    <button
                        class="
                            bg-indigo-600
                            px-4
                            py-2
                            rounded-md
                            text-white
                            font-semibold
                            tracking-wide
                            cursor-pointer
                        "
                    >
                        Create
                    </button>
                </div>
            </div>
        </div>
        <div>
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
                                        bg-gray-100
                                        text-left text-xs
                                        font-semibold
                                        text-gray-600
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
                                v-for="(queuedWord, index) in wordQueue"
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
                                    {{ queuedWord.word }}
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
                                    <!-- {{new Date(queuedWord.scheduled_for)}} -->
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
                                    <button class="underline">Change</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>