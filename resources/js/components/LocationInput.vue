<script setup>
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import { useForm, usePage } from "@inertiajs/inertia-vue3";
import { onMounted, onBeforeMount, onUpdated, computed } from "vue";
import { ref, getCurrentInstance, reactive } from "vue";
const isLoggedIn = usePage().props.value.isLoggedIn;

const props = defineProps({
    locations: Object,
    word: Object,
    userSelectedLocations: Array
});

const form = useForm({
    wordId: props.word.id,
    locations: computed(() => props.userSelectedLocations),
});

const createLocationLink = () => {
    form.post(route("word.locations.new", { word: props.word.word }), {
        wordId: form.wordId,
        locations: form.locations,
        onSuccess: () => {
            form.reset();
        },
    });
};

const onLocationChecked = (value) => {
    if (form.locations.includes(value)) {
        form.locations = form.locations.filter((location) => location !== value);
    } else {
        form.locations.push(value);
    }
}

onUpdated(() => {
    console.log("UPDATE");
})
</script>

<template>
    <div>
        <form @submit.prevent="createLocationLink">
            <div class="justify-end mt-2">
                <div
                    v-if="!isLoggedIn"
                    class="
                        text-center
                        px-4
                        py-3
                        leading-normal
                        text-blue-700
                        bg-blue-100
                        rounded-lg
                    "
                    role="alert"
                >
                    <p>You must be logged in to add locations.</p>
                </div>

                <template v-else>
                    <div>
                        <div v-for="location in locations" :key="location.id + JSON.stringify(userSelectedLocations)">
                            <label class="inline-flex items-center">
                                <input
                                    :checked="form.locations.includes(location.id)"
                                    :value="location.id"
                                    type="checkbox"
                                    class="w-6 h-6 rounded"
                                    @input="event => onLocationChecked(event.target.value)"
                                />
                                <span class="ml-2">
                                    {{ location.name }}
                                </span>
                            </label>
                        </div>
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
                        Submit
                    </button>
                </template>
            </div>
        </form>
    </div>
</template>
