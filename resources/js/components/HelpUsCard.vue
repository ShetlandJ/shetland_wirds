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
            bg-gray-800
        "
    >
        <div class="p-4 flex items-center">
            <div>
                <h4
                    class="
                        text-2xl
                        font-semibold
                        text-gray-700
                        text-gray-200
                    "
                >
                    {{word.word}}
                </h4>
            </div>
        </div>
    </div>
</template>