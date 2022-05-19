<script setup>
import "@vueup/vue-quill/dist/vue-quill.snow.css";

defineProps({
    options: Object,
});
</script>

<template>
    <div>
        <QuillEditor :options="options" theme="snow" />
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
            type="button"
        >
            Post
        </button>
        </div>
    </div>
</template>
