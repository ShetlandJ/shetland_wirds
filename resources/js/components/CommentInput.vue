<script setup>
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import { useForm } from "@inertiajs/inertia-vue3";
import { onMounted, onUpdated } from "vue";
import { ref, getCurrentInstance } from 'vue';

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
    form.comment_id = props.parentComment ? props.parentComment.id : null
    form.post(route("newComment", { word: props.word.word }), {
        text: form.text,
        comment_id: form.comment_id,
        onSuccess: () => {
            // message.value = '';
            var element = document.getElementsByClassName("ql-editor");
            element[0].innerHTML = "";
        }
    });
};

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
                <button
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
                    Post
                </button>
            </div>
        </form>
    </div>
</template>
