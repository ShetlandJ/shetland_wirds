<script setup>
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import { useForm, usePage } from "@inertiajs/inertia-vue3";
import { onMounted, onUpdated, computed } from "vue";
import { ref, getCurrentInstance } from "vue";
const isLoggedIn = usePage().props.value.isLoggedIn;

const props = defineProps({
    options: Object,
    word: Object,
    parentComment: [Object, null],
});

const form = useForm({
    text: "",
    comment_id: null,
});

const newComment = () => {
    form.comment_id = props.parentComment ? props.parentComment.id : null;
    form.post(route("word.comments.new", { word: props.word.word }), {
        text: form.text,
        comment_id: form.comment_id,
        onSuccess: () => {
            var element = document.getElementsByClassName("ql-editor");
            element[0].innerHTML = "";
        },
    });
};

const isDarkMode = computed(() =>
    window.matchMedia('(prefers-color-scheme: dark)').matches
);

onMounted(() => {
    const toolbar = document.getElementsByClassName('ql-toolbar ql-snow')[0];
    const editor = document.getElementsByClassName('ql-editor ql-blank')[0];

    if (isDarkMode.value) {
        toolbar.classList.add('bg-gray-400');
        editor.classList.add('bg-gray-400');
    }
})
</script>

<template>
    <div>
        <form @submit.prevent="newComment">
            <QuillEditor
                v-model:content="form.text"
                contentType="html"
                :options="options"
                theme="snow"
            />

            <div class="flex justify-end mt-2">
                <div class="mt-1 mr-2">
                    <small v-if="!isLoggedIn"> You must be logged in to comment. </small>
                </div>

                <button
                    :disabled="!isLoggedIn"
                    class="
                        btn
                        inline-block
                        px-6
                        py-2.5
                        bg-blue-600
                        text-white
                        font-medium
                        text-xs
                        leading-tight
                        uppercase
                        rounded
                        shadow-md
                        hover:bg-blue-700 hover:shadow-lg
                        focus:bg-blue-700
                        focus:shadow-lg
                        focus:outline-none
                        focus:ring-0
                        active:bg-blue-800 active:shadow-lg
                        transition
                        duration-150
                        ease-in-out
                        flex
                        items-center
                        disabled:opacity-50
                    "
                    type="submit"
                >
                    Add comment
                </button>
            </div>
        </form>
    </div>
</template>
