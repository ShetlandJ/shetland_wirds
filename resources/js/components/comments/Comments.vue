<script setup>
import { usePage } from "@inertiajs/inertia-vue3";
import Comment from "./Comment.vue";
import CommentInput from "./CommentInput.vue";
import { ref, reactive } from "vue";

const { isLoggedIn } = usePage().props.value;
const word = reactive(usePage().props.value.word);

const props = defineProps({
    comments: {
        type: Array,
        default: [],
    },
});

const commentOptions = ref({
    placeholder: `Any thoughts on ${word.word}?`,
});
</script>

<template>
    <div v-if="word">
        <Alert
            class="mb-4"
            v-if="!comments.length"
            :message="
                isLoggedIn
                    ? 'There are no comments available for this word yet, be the first to add one!'
                    : 'Log in to add a comment'
            "
        />

        <Comment
            v-for="comment in comments"
            :key="comment.id"
            :comment="comment"
            :word="word"
        />
        <div class="mb-4">
            <CommentInput
                v-if="isLoggedIn"
                :word="word"
                :options="commentOptions"
            />
        </div>
    </div>
</template>


<style lang="scss" scoped>
</style>