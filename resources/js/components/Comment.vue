<script setup>
import { formatDateTime } from "../utils/formatters";
import { ref } from "vue";
import SanitisedHtml from "./SanitisedHtml.vue";
import CommentInput from "./CommentInput.vue";

const showChildReplies = ref(false);

defineProps({
    word: Object,
    comment: Object,
});

const commentOptions = ref({
    placeholder: `Continue the conversation...`,
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
                {{ comment.author_initials }}
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
                <SanitisedHtml :html-string="comment.message" />
            </p>
            <div class="mt-4 flex items-center">
                <!-- <div class="flex -space-x-2 mr-2">
                    <img
                        class="rounded-full w-6 h-6 border border-white"
                        src="https://images.unsplash.com/photo-1554151228-14d9def656e4?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=100&h=100&q=80"
                        alt=""
                    />
                </div> -->
                <div
                    v-if="
                        comment.child_comments && comment.child_comments.length
                    "
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
                    <span v-if="comment.child_comments">
                        {{
                            comment.child_comments.length === 1
                                ? "reply"
                                : "replies"
                        }}
                    </span>
                </div>

                <div v-else>
                    <p
                        @click="showChildReplies = !showChildReplies"
                        class="
                            text-sm text-gray-500
                            font-semibold
                            hover:underline
                            cursor-pointer
                        "
                    >
                        reply
                    </p>
                </div>
            </div>

            <div v-if="showChildReplies" class="space-y-4">
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
                <div
                    v-for="childComment in comment.child_comments"
                    :key="childComment.id"
                >
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
                                {{ childComment.author_initials }}
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
                                <SanitisedHtml
                                    :html-string="childComment.message"
                                />
                            </p>
                        </div>
                    </div>
                </div>
                <CommentInput
                    :word="word"
                    :options="commentOptions"
                    :parent-comment="comment"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
<style>
.ql-snow {
    border-spacing: 0;
}
.ql-toolbar,
.ql-container {
    border: 1px solid #dcdcdc;
    border-radius: 0.5rem;
    transition: all 0.1s ease-in-out;
}
.ql-toolbar {
    border-bottom: 0px !important;
    background: #f5f5f5;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}
.ql-container {
    border-top: 0px !important;
    border-top-left-radius: 0;
    border-top-right-radius: 0;
}
.ql-snow .ql-picker-label::before,
.ql-snow .ql-stroke,
.ql-snow .ql-stroke:hover,
.ql-snow .ql-fill {
    stroke: #dcdcdc;
    color: #dcdcdc;
    transition: all 0.1s ease-in-out;
}
.has-focus .ql-toolbar {
    border: 1px solid #686868;
    color: #686868;
    stroke: #686868;
}
.has-focus .ql-container {
    border: 1px solid #686868;
}
.has-focus .ql-snow .ql-picker-label::before,
.has-focus .ql-snow .ql-stroke,
.has-focus .ql-snow .ql-stroke:hover {
    color: #686868;
    stroke: #686868;
}
.has-focus .ql-snow .ql-fill {
    fill: black;
}

.ql-editor.ql-blank::before {
    left: 22px !important;
}
.read-only .ql-container {
    border-top: 1px solid #ccc !important;
    border-top-left-radius: 0.5rem !important;
    border-top-right-radius: 0.5rem !important;
}
</style>