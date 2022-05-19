<script setup>
import { formatDateTime } from "../utils/formatters";
import { ref } from "vue";

const showChildReplies = ref(false);

defineProps({
    comment: Object,
});
</script>

<template>
    <div class="flex mb-4">
        <div class="flex-shrink-0 mr-3">
            <div
                style="background: #2663eb"
                class="
                    rounded-full
                    flex
                    items-center
                    justify-center
                    mt-2
                    w-8
                    h-8
                    sm:w-10
                    bg-blue-500
                    sm:h-10
                    text-white text-xl
                "
            >
                {{comment.author_initials}}
            </div>
        </div>
        <div
            class="
                flex-1
                border
                rounded-lg
                px-4
                py-2
                sm:px-6 sm:py-4
                leading-relaxed
            "
        >
            <strong>{{ comment.author }}</strong>
            <span class="text-xs text-gray-400 ml-2">
                {{ formatDateTime(comment.created_at) }}
            </span>
            <p class="text-sm">
                {{ comment.message }}
            </p>
            <div class="mt-4 flex items-center">
                <div class="flex -space-x-2 mr-2">
                    <img
                        class="rounded-full w-6 h-6 border border-white"
                        src="https://images.unsplash.com/photo-1554151228-14d9def656e4?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=100&h=100&q=80"
                        alt=""
                    />
                </div>
                <div
                    class="
                        text-sm text-gray-500
                        font-semibold
                        hover:underline
                        cursor-pointer
                    "
                    @click="showChildReplies = !showChildReplies"
                >
                    {{
                        comment.child_comments
                            ? comment.child_comments.length
                            : 0
                    }}
                    {{ comment.child_comments ? "reply" : "replies" }}
                </div>
            </div>

            <div v-if="showChildReplies" class="space-y-4">
                <div
                    v-for="childComment in comment.child_comments"
                    :key="childComment.id"
                >
                    <h4
                        class="
                            my-5
                            uppercase
                            tracking-wide
                            text-gray-400
                            font-bold
                            text-xs
                        "
                    >
                        Replies
                    </h4>

                    <div class="flex">
                        <div class="flex-shrink-0 mr-3">
                            <div
                                style="background: #2663eb"
                                class="
                                    rounded-full
                                    flex
                                    items-center
                                    justify-center
                                    mt-2
                                    w-8
                                    h-8
                                    sm:w-10
                                    bg-blue-500
                                    sm:h-10
                                    text-white text-xl
                                "
                            >
                            {{childComment.author_initials}}
                            </div>
                        </div>
                        <div
                            class="
                                flex-1
                                bg-gray-100
                                rounded-lg
                                px-4
                                py-2
                                sm:px-6 sm:py-4
                                leading-relaxed
                            "
                        >
                            <strong>{{ childComment.author }}</strong>
                            <span class="text-xs text-gray-400 ml-2">
                                {{ formatDateTime(childComment.created_at) }}
                            </span>
                            <p class="text-sm">
                                {{ childComment.message }}
                            </p>
                        </div>
                    </div>
                </div>
                <!-- <div class="flex">
                        <div class="flex-shrink-0 mr-3">
                            <img
                                class="mt-3 rounded-full w-6 h-6 sm:w-8 sm:h-8"
                                src="https://images.unsplash.com/photo-1604426633861-11b2faead63c?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=200&h=200&q=80"
                                alt=""
                            />
                        </div>
                        <div
                            class="
                                flex-1
                                bg-gray-100
                                rounded-lg
                                px-4
                                py-2
                                sm:px-6 sm:py-4
                                leading-relaxed
                            "
                        >
                            <strong>Sarah</strong>
                            <span class="text-xs text-gray-400">3:34 PM</span>
                            <p class="text-xs sm:text-sm">
                                Lorem ipsum dolor sit amet, consetetur
                                sadipscing elitr, sed diam nonumy eirmod tempor
                                invidunt ut labore et dolore magna aliquyam
                                erat, sed diam voluptua.
                            </p>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>