<script setup>
import { useForm, usePage } from "@inertiajs/inertia-vue3";
import {
    onMounted,
    onBeforeMount,
    onUpdated,
    computed,
    ref,
    getCurrentInstance,
    reactive,
} from "vue";
const isLoggedIn = usePage().props.value.isLoggedIn;

const props = defineProps({
    locations: Object,
    word: Object,
    userSelectedLocations: Array,
});

const form = useForm({
    wordId: props.word.id,
    locations: props.userSelectedLocations,
});

onMounted(() => {
    form.locations = props.userSelectedLocations;
});

const locationKey = reactive(JSON.stringify(props.userSelectedLocations));

const createLocationLink = () => {
    form.post(route("word.locations.new", { word: props.word.word }), {
        wordId: form.wordId,
        locations: form.locations,
        onSuccess: ({ props }) => {
            form.locations = props.userSelectedLocations;
        },
    });
};

const onLocationChecked = (value) => {
    if (form.locations.includes(value)) {
        form.locations = form.locations.filter(
            (location) => location !== value
        );
    } else {
        form.locations.push(value);
    }
};

const searchString = ref("");

const filteredLocations = computed(() => {
    if (searchString.value === "") {
        return props.locations;
    }
    return props.locations.filter((location) => {
        return location.name
            .toLowerCase()
            .includes(searchString.value.toLowerCase());
    });
});
</script>

<template>
    <div :key="JSON.stringify(props.userSelectedLocations)">
        <form @submit.prevent="createLocationLink">
            <div class="justify-end mt-2">
                <Alert
                    v-if="!isLoggedIn"
                    message="You must be logged in to add locations."
                />

                <template v-else>
                    <input
                        class="
                            form-control
                            relative
                            flex-auto
                            min-w-0
                            block
                            w-1/2
                            px-3
                            py-1.5
                            text-base
                            font-normal
                            text-gray-700
                            bg-white bg-clip-padding
                            border border-solid border-gray-300
                            rounded
                            transition
                            ease-in-out
                            m-0
                            focus:text-gray-700
                            focus:bg-white
                            focus:border-blue-600
                            focus:outline-none
                            mb-4
                        "
                        type="text"
                        placeholder="Search locations"
                        v-model="searchString"
                    />

                    <div>
                        <div
                            v-for="location in filteredLocations"
                            :key="location.id"
                        >
                            <label class="inline-flex items-center">
                                <input
                                    :checked="
                                        form.locations.includes(location.id)
                                    "
                                    :value="location.id"
                                    type="checkbox"
                                    class="w-6 h-6 rounded"
                                    @input="
                                        (event) =>
                                            onLocationChecked(
                                                event.target.value
                                            )
                                    "
                                />
                                <span class="ml-2 dark:text-white">
                                    {{ location.name }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <ActionButton
                        type="submit"
                        :disabled="!isLoggedIn"
                        message="Submit"
                    />
                </template>
            </div>
        </form>
    </div>
</template>
