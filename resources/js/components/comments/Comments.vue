<script setup>
import { usePage } from "@inertiajs/inertia-vue3";
import Comment from "./Comment.vue";
import CommentInput from "./CommentInput.vue";
import { ref, reactive } from "vue";

import { useI18n } from "vue-i18n";
const { t } = useI18n();

const { isLoggedIn } = usePage().props.value;
const word = reactive(usePage().props.value.word);

const props = defineProps({
    comments: {
        type: Array,
        default: [],
    },
});

const getLocale = () => {
    return localStorage.getItem("spaekationary-locale") || "shet";
};

const commentOptions = ref({
    placeholder: getLocale() === 'shet'
    ? `Whit's yir tochts aboot ${word.word}?`
    : `Any thoughts on ${word.word}?`,
});
</script>

<template>
    <div v-if="word">
        <Alert
            class="mb-4"
            v-if="!comments.length"
            :message="
                isLoggedIn
                    ? t('word.comments.noComments')
                    : t('word.comments.login')
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