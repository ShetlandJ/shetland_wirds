<script setup>
const props = defineProps({
    word: Object,
});
</script>

<template>
    <div
        class="
            min-w-0
            rounded-lg
            shadow-xs
            overflow-hidden
            bg-white
            dark:bg-gray-800
        "
    >
        <div class="p-4 flex items-center">
            <div>
                <p
                    class="
                        mb-2
                        text-md
                        font-medium
                        text-gray-600
                        dark:text-gray-400
                    "
                >
                    {{word.word}}
                </p>
                <!-- <h4
                    class="
                        text-3xl
                        font-semibold
                        text-gray-700
                        dark:text-gray-200
                    "
                >
                    {{stat}}
                </h4> -->
            </div>
        </div>
    </div>
</template>