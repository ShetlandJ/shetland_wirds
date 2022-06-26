<script setup>
import { Inertia } from "@inertiajs/inertia";
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
const randomWordSlug = usePage().props.value.randomWord;

const logout = async () => {
    Inertia.post(route("logout"));
    window.location.reload();
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
    "random"
];
</script>

<template>
    <div
        :class="{
            block: showingNavigationDropdown,
            hidden: !showingNavigationDropdown,
        }"
        class=""
    >
        <div class="pt-2 pb-3 space-y-1">
            <button
                @click="showAlphabetBrowser = !showAlphabetBrowser"
                class="dark:text-white"
            >
                {{ showAlphabetBrowser ? "Close" : "Browse" }}
            </button>

            <div
                v-if="showAlphabetBrowser"
                class="flex flex-wrap justify-center"
            >
                <div
                    v-for="letter in alphabetArray"
                    :key="letter"
                    class="flex justify-center my-2 px-1"
                >
                    <Link
                        v-if="letter !== 'random'"
                        class="uppercase"
                        :href="route('letter', letter)"
                    >
                        <div
                            style="width: 100px"
                            class="
                                bg-gray-300
                                flex
                                justify-center
                                rounded-md
                                text-xl
                            "
                        >
                            {{ letter }}
                        </div>
                    </Link>
                    <Link
                        v-else
                        class="uppercase"
                        :href="route('word', { word: randomWordSlug })"
                    >
                        <div
                            style="width: 100px"
                            class="
                                bg-gray-300
                                flex
                                justify-center
                                rounded-md
                                text-xl
                            "
                        >
                            {{ letter }}
                        </div>
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
                    dark:text-white
                "
                :href="route('create')"
                :class="{ 'font-bold': $page.url === '/create' }"
            >
                + Add
            </Link>

            <Link
                v-if="isLoggedIn"
                :href="route('dashboard')"
                :active="route().current('dashboard')"
                class="dark:text-white"
            >
                Dashboard
            </Link>

            <Link
                class="
                    block
                    no-underline
                    hover:underline
                    mt-0
                    text-grey-darkest
                    hover:text-black
                    md:border-none md:p-0
                    dark:text-white
                "
                :href="route('about')"
                :class="{ 'font-bold': $page.url === '/about' }"
            >
                About
            </Link>
        </div>

        <div class="pt-2 pb-1 border-t border-gray-200">
            <div class="mb-2">
                <Link
                    v-if="!isLoggedIn"
                    class="dark:text-white"
                    :href="route('login')"
                >
                    Login
                </Link>
            </div>
            <div class="mb-2">
                <Link
                    v-if="!isLoggedIn"
                    class="dark:text-white"
                    :href="route('register')"
                >
                    Register
                </Link>
            </div>
            <form v-if="isLoggedIn" @submit.prevent="logout">
                <button type="submit" class="dark:text-white">Log Out</button>
            </form>
        </div>
    </div>
</template>