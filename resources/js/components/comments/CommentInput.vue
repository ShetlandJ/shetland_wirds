<script setup>
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import { useForm, usePage } from "@inertiajs/inertia-vue3";
import { onMounted, onUpdated, computed } from "vue";
import { ref, getCurrentInstance } from "vue";
const isLoggedIn = usePage().props.value.isLoggedIn;

const emit = defineEmits(["editing-complete"]);

const props = defineProps({
    options: Object,
    word: Object,
    parentComment: [Object, null],
    childComment: {
        type: [Object, null],
        default: null,
    },
    commentId: {
        type: [String, null],
        default: null,
    },
    value: {
        type: String,
        default: "",
    },
    actionMessage: {
        type: String,
        default: "Add comment",
    },
    editMode: {
        type: Boolean,
        default: false,
    },
});

const form = useForm({
    text: "",
    comment_id: null,
});

const newComment = () => {
    form.comment_id = props.parentComment ? props.parentComment.id : null;
    form.post(route("word.comments.new", { slug: props.word.slug }), {
        text: form.text,
        comment_id: form.comment_id,
        onSuccess: () => {
            var element = document.getElementsByClassName("ql-editor");
            element[0].innerHTML = "";
        },
    });
};

const updateForm = useForm({
    text: "",
    childCommentId: null,
});

const updateComment = () => {
    updateForm.childCommentId = props.commentId;
    updateForm.patch(
        route("word.comments.update", {
            word: props.word.word,
        }),
        {
            text: updateForm.text,
            childCommentId: props.commentId,
            onSuccess: () => {
                var element = document.getElementsByClassName("ql-editor");
                element[0].innerHTML = "";
                emit("editing-complete");
            },
        }
    );
};

const isDarkMode = computed(
    () => window.matchMedia("(prefers-color-scheme: dark)").matches
);

const updateKey = ref("");

onMounted(() => {
    const toolbar = document.getElementsByClassName("ql-toolbar ql-snow")[0];
    const editor = document.getElementsByClassName("ql-editor ql-blank")[0];

    if (isDarkMode.value) {
        toolbar.classList.add("bg-gray-400");
        editor.classList.add("bg-gray-400");
    }

    updateForm.text = props.value;
    updateKey.value = new Date();
});
</script>

<template>
    <div>
        <form @submit.prevent="newComment" v-if="!editMode">
            <QuillEditor
                v-model:content="form.text"
                contentType="html"
                :options="options"
                theme="snow"
            />

            <div class="flex justify-end mt-2">
                <div class="mt-1 mr-2">
                    <small v-if="!isLoggedIn">
                        You must be logged in to comment.
                    </small>
                </div>

                <ActionButton
                    :message="actionMessage"
                    :disabled="!isLoggedIn"
                />
            </div>
        </form>

        <form @submit.prevent="updateComment" v-else>
            <QuillEditor
                :key="updateKey"
                v-model:content="updateForm.text"
                contentType="html"
                :options="options"
                theme="snow"
            />

            <div class="flex justify-end mt-2">
                <div class="mt-1 mr-2">
                    <small v-if="!isLoggedIn">
                        You must be logged in to comment.
                    </small>
                </div>

                <ActionButton
                    :message="actionMessage"
                    :disabled="!isLoggedIn"
                />
            </div>
        </form>
    </div>
</template>
