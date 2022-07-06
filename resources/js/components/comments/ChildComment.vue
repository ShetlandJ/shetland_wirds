<script setup>
import { usePage } from '@inertiajs/inertia-vue3';
import { ref } from "vue";
import { formatDateTime } from "../../utils/formatters";

const isLoggedIn = usePage().props.value.isLoggedIn;

defineProps({
    childComment: Object,
});

const commentOptions = ref({
    placeholder: `Continue the conversation...`,
});
</script>

<template>
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
                dark:bg-gray-500
                rounded-lg
                px-4
                py-2
                sm:px-6 sm:py-4
                leading-relaxed
                dark:text-gray-200
            "
        >
            <strong>{{ childComment.author }}</strong>
            <span class="text-xs text-gray-400 ml-2">
                {{ formatDateTime(childComment.created_at) }}
            </span>
            <p class="text-sm">
                <SanitisedHtml :html-string="childComment.message" />
            </p>
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