<script setup>
import { usePage } from "@inertiajs/inertia-vue3";
import { formatDateTime } from "../../utils/formatters";
import { ref } from "vue";
import ChildComment from "./ChildComment.vue";
import CommentInput from "../comments/CommentInput.vue";
const Filter = require("bad-words");
const swearFilter = new Filter();

const isLoggedIn = usePage().props.value.isLoggedIn;
const userId = usePage().props.value.user?.uuid;

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
                dark:text-gray-200
            "
        >
            <strong>{{ comment.author }}</strong>
            <span class="text-xs text-gray-400 ml-2">
                {{ formatDateTime(comment.created_at) }}
            </span>
            <p class="text-md dark:text-white mt-2">
                <SanitisedHtml
                    :html-string="swearFilter.clean(comment.message)"
                />
            </p>
            <div class="mt-4 flex justify-between items-center">
                <div
                    v-if="
                        comment.child_comments && comment.child_comments.length
                    "
                    class="
                        text-sm text-gray-500
                        font-semibold
                        hover:underline
                        cursor-pointer
                        dark:text-gray-400
                    "
                    @click="showChildReplies = !showChildReplies"
                >
                    {{
                        comment.child_comments
                            ? comment.child_comments.length
                            : 0
                    }}
                    <span>
                        {{
                            comment.child_comments.length === 1
                                ? "reply"
                                : "replies"
                        }}
                    </span>
                </div>

                <div v-else-if="isLoggedIn">
                    <p
                        @click="showChildReplies = !showChildReplies"
                        class="
                            text-sm text-gray-500
                            font-semibold
                            hover:underline
                            cursor-pointer
                            dark:text-gray-300
                        "
                    >
                        reply
                    </p>
                </div>

                <p
                    v-if="comment.author_uuid === userId"
                    class="
                        text-xs text-gray-500
                        font-semibold
                        hover:underline
                        cursor-pointer
                        dark:text-gray-400
                    "
                >
                    Delete
                </p>
            </div>

            <div v-if="showChildReplies" class="space-y-4">
                <h4
                    v-if="
                        comment.child_comments && comment.child_comments.length
                    "
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

                <ChildComment
                    v-for="childComment in comment.child_comments"
                    :child-comment="childComment"
                    :key="childComment.id"
                />

                <CommentInput
                    v-if="isLoggedIn"
                    :class="{
                        'mt-4': comment.child_comments.length === 0,
                    }"
                    :word="word"
                    :options="commentOptions"
                    :parent-comment="comment"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
<style > .ql-snow {
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