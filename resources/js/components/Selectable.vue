<script setup>
const emit = defineEmits(['select'])

defineProps({
    inputName: String,
    inputValue: String,
    selected: Boolean,
});

const updateActivePlan = (value) => {
    emit('select', value);
};
</script>

<template>
    <div
        class="
            w-48
            bg-white
            rounded-lg
            shadow-md
            p-2
            hover:cursor-pointer
            mb-8
            hover:bg-green-lightest
            focus:outline-none focus:shadow-outline-green
        "
        :class="[this.selected ? 'bg-green-lightest shadow-outline-green' : '']"
        tabindex="0"
        @click="updateActivePlan(this.inputValue)"
        @keydown.space="updateActivePlan(this.inputValue)"
    >
        <div class="flex justify-between items-center">
            <h1 class="uppercase text-base tracking-wide text-blue-darker my-2">
                {{ this.inputName }}
            </h1>
            <div v-if="this.selected">
                <svg
                    class="w-6 h-6"
                    width="200px"
                    height="200px"
                    viewBox="0 0 200 200"
                    version="1.1"
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink"
                >
                    <g
                        id="Page-1"
                        stroke="none"
                        stroke-width="1"
                        fill="none"
                        fill-rule="evenodd"
                    >
                        <g id="checkmark-outline" fill-rule="nonzero">
                            <path
                                d="M31.1442786,171.840796 C5.2779518,146.858262 -5.09578082,109.862896 4.01023318,75.0738981 C13.1162472,40.2848999 40.2848999,13.1162472 75.0738981,4.01023318 C109.862896,-5.09578082 146.858262,5.2779518 171.840796,31.1442786 C209.549474,70.1869539 209.010186,132.247241 170.628714,170.628714 C132.247241,209.010186 70.1869539,209.549474 31.1442786,171.840796 Z"
                                id="Shape"
                                fill="#97EBDC"
                            ></path>
                            <polygon
                                id="Path"
                                fill="#00836D"
                                points="66.6666667 89.4527363 89.5522388 112.437811 132.338308 69.6517413 146.268657 83.7810945 89.5522388 140.298507 52.7363184 103.482587 66.6666667 89.3532338"
                            ></polygon>
                        </g>
                    </g>
                </svg>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-green {
	background-color: #38c172
}

.bg-green-light {
	background-color: #51d88a
}

.bg-green-lighter {
	background-color: #a2f5bf
}

.bg-green-lightest {
	background-color: #e3fcec
}

.hover\:bg-green-darkest:hover {
	background-color: #0f2f21
}

.hover\:bg-green-darker:hover {
	background-color: #1a4731
}

.hover\:bg-green-dark:hover {
	background-color: #1f9d55
}

.hover\:bg-green:hover {
	background-color: #38c172
}

.hover\:bg-green-light:hover {
	background-color: #51d88a
}

.hover\:bg-green-lighter:hover {
	background-color: #a2f5bf
}

.hover\:bg-green-lightest:hover {
	background-color: #e3fcec
}

.shadow-outline-green {
	-webkit-box-shadow: 0 0 0 3px rgba(5, 202, 160, .8);
	box-shadow: 0 0 0 3px rgba(5, 202, 160, .8)
}
</style>