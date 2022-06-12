<script setup>
import { useForm, usePage, Link } from "@inertiajs/inertia-vue3";
import JetApplicationMark from "@/Jetstream/ApplicationMark.vue";
import JetBanner from "@/Jetstream/Banner.vue";
import JetDropdown from "@/Jetstream/Dropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink.vue";
import JetNavLink from "@/Jetstream/NavLink.vue";
import JetResponsiveNavLink from "@/Jetstream/ResponsiveNavLink.vue";
import { ref } from "vue";

defineProps({
    showingNavigationDropdown: Boolean,
});

const isLoggedIn = usePage().props.value.isLoggedIn;

const emit = defineEmits(["set-search"]);

const form = useForm({
    searchString: "",
});

const search = () => {
    emit("set-search", form.searchString);
    form.transform((data) => ({
        ...data,
    })).post(route("search", { searchTerm: form.searchString }), {
        searchString: form.searchString,
    });
};

const logout = () => {
    Inertia.post(route("logout"));
};

const showAlphabetBrowser = ref(false);

const alphabetArray = [
    "a",
    "b",
    "c",
    "d",
    "e",
    "f",
    "g",
    "h",
    "i",
    "j",
    "k",
    "l",
    "m",
    "n",
    "o",
    "p",
    "q",
    "r",
    "s",
    "t",
    "u",
    "v",
    "w",
    "x",
    "y",
    "z",
];
</script>

<template>
    <div
        :class="{
            block: showingNavigationDropdown,
            hidden: !showingNavigationDropdown,
        }"
        class="sm:hidden"
    >
        <div class="pt-2 pb-3 space-y-1">
            <div class="input-group relative flex items-stretch w-full mb-2">
                <form class="flex" @submit.prevent="search">
                    <input
                        v-model="form.searchString"
                        size="150"
                        type="search"
                        class="
                            form-control
                            relative
                            flex-auto
                            min-w-0
                            block
                            w-full
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
                        "
                        placeholder="Search words"
                        aria-label="Search"
                        aria-describedby="button-addon2"
                    />
                    <button
                        :disabled="form.searchString.length === 0"
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
                        id="button-addon2"
                    >
                        <svg
                            aria-hidden="true"
                            focusable="false"
                            data-prefix="fas"
                            data-icon="search"
                            class="w-4"
                            role="img"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 512 512"
                        >
                            <path
                                fill="currentColor"
                                d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"
                            ></path>
                        </svg>
                    </button>
                </form>
            </div>

            <button @click="showAlphabetBrowser = !showAlphabetBrowser">
                {{ showAlphabetBrowser ? "Close" : "Browse" }}
            </button>

            <div v-if="showAlphabetBrowser">
                <div
                    v-for="letter in alphabetArray"
                    :key="letter"
                    class="flex justify-center my-3"
                >
                    <Link class="uppercase" :href="route('letter', letter)">
                        {{ letter }}
                    </Link>
                </div>
            </div>

            <Link
                class="
                    block
                    no-underline
                    hover:underline
                    py-2
                    text-grey-darkest
                    hover:text-black
                    md:border-none md:p-0
                "
                :href="route('create')"
            >
                + Add
            </Link>

            <Link
                v-if="isLoggedIn"
                :href="route('dashboard')"
                :active="route().current('dashboard')"
            >
                Dashboard
            </Link>
        </div>

        <div class="pt-2 pb-1 border-t border-gray-200">
            <div class="mb-2">
                <Link v-if="!isLoggedIn" :href="route('login')"> Login </Link>
            </div>
            <div class="mb-2">
                <Link v-if="!isLoggedIn" :href="route('register')">
                    Register
                </Link>
            </div>
            <form method="POST" v-if="isLoggedIn" @submit.prevent="logout">
                <Link as="button"> Log Out </Link>
            </form>
        </div>
    </div>
</template>