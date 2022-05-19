<script setup>
import { quillEditor, Quill } from "vue3-quill";

import { reactive } from "vue";

const state = reactive({
    content: "<p>2333</p>",
    _content: "",
    editorOption: {
        placeholder: "core",
        modules: {
            // toolbars: [
            // custom toolbars options
            // will override the default configuration
            // ],
            // other moudle options here
            // otherMoudle: {}
        },
        // more options
    },
    disabled: false,
});

const onEditorBlur = (quill) => {
    console.log("editor blur!", quill);
};
const onEditorFocus = (quill) => {
    console.log("editor focus!", quill);
};
const onEditorReady = (quill) => {
    console.log("editor ready!", quill);
};
const onEditorChange = ({ quill, html, text }) => {
    console.log("editor change!", quill, html, text);
    state._content = html;
};

setTimeout(() => {
    state.disabled = true;
}, 2000);
</script>

<template>
    <quill-editor
        v-model:value="state.content"
        :options="state.editorOption"
        :disabled="state.disabled"
        @blur="onEditorBlur($event)"
        @focus="onEditorFocus($event)"
        @ready="onEditorReady($event)"
        @change="onEditorChange($event)"
    />
</template>
