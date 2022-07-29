<script setup>
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";
import { ref } from "vue";
import { formatDateTime } from "../../utils/formatters";
import CommentInput from "../comments/CommentInput.vue";

import { useStore } from "../../store/commentStore";
const commentStore = useStore();

import { useI18n } from "vue-i18n";
const { t } = useI18n();

const isLoggedIn = usePage().props.value.isLoggedIn;
const userId = usePage().props.value.user?.uuid;

const props = defineProps({
    childComment: Object,
    word: Object,
});

const commentOptions = ref({
    placeholder: t('word.comments.continue'),
});

const editMode = ref(false);

const deleteComment = (commentId) => {
    Inertia.delete(
        route("word.comments.delete", { slug: props.word.slug, commentId }),
        {
            commentId,
        }
    );
};

const toggleEdit = () => {
    commentStore.setChildEditing(!editMode.value);
    editMode.value = !editMode.value;
};
</script>

<template>
    <div class="flex" v-if="childComment && childComment.id">
        <div
            class="
                flex-1
                bg-gray-100
                dark:bg-gray-500
                rounded-lg
                px-4
                py-2
                sm:px-6 sm:py-4
                leading-relaxed
                dark:text-gray-500
                dark:text-white
            "
        >
            <strong class="dark:text-white">{{ childComment.author }}</strong>
            <span class="text-xs text-gray-400 ml-2 dark:text-white">
                {{ formatDateTime(childComment.created_at) }}
            </span>
            <p class="text-sm mt-2 dark:text-white" v-if="!editMode">
                <SanitisedHtml :html-string="childComment.message" />
            </p>
            <CommentInput
                v-else
                :value="childComment.message"
                :child-comment="childComment"
                class="mt-4"
                :comment-id="childComment.id"
                :word="word"
                :action-message="t('word.comment.update)"
                :edit-mode="editMode"
                @editing-complete="editMode = false"
            />

            <div class="flex justify-end">
                <template v-if="!editMode">
                    <p
                        v-if="childComment.author_id === userId && isLoggedIn"
                        class="
                            text-xs text-gray-500
                            font-semibold
                            hover:underline
                            cursor-pointer
                            dark:text-gray-300
                            mr-2
                        "
                        @click="toggleEdit"
                    >
                        {{t('global.edit')}}
                    </p>

                    <p
                        v-if="childComment.author_id === userId && isLoggedIn"
                        class="
                            text-xs text-gray-500
                            font-semibold
                            hover:underline
                            cursor-pointer
                            dark:text-gray-300
                        "
                        @click="deleteComment(childComment.id)"
                    >
                        {{t('global.delete')}}
                    </p>
                </template>
                <template v-else>
                    <p
                        v-if="childComment.author_id === userId && isLoggedIn"
                        class="
                            text-xs text-red-500
                            font-semibold
                            hover:underline
                            cursor-pointer
                            dark:text-gray-400
                            mt-3
                        "
                        @click="toggleEdit"
                    >
                        {{t('global.cancel')}}
                    </p>
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>
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