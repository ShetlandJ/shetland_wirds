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

import { useI18n } from "vue-i18n";
const { t } = useI18n();

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
    "random",
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
                        v-else-if="randomWordSlug"
                        class="uppercase"
                        :href="route('word.comments', { word: randomWordSlug })"
                    >
                        <div
                            style="width: 100px; background-color: #ffe368"
                            class="flex justify-center rounded-md text-xl"
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
                :href="route('faq')"
                :class="{ 'font-bold': $page.url === '/faqs' }"
            >
                FAQs
            </Link>

            <Link
                class="
                    block
                    no-underline
                    hover:underline
                    text-grey-darkest
                    hover:text-black
                    md:border-none md:p-0
                    dark:text-white
                "
                :href="route('create')"
                :class="{ 'font-bold': $page.url === '/create' }"
            >
                + {{t('nav.add')}}
            </Link>

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
                :href="route('about')"
                :class="{ 'font-bold': $page.url === '/about' }"
            >
                {{t('nav.about')}}
            </Link>

            <Link
                v-if="isLoggedIn"
                :href="route('dashboard')"
                :active="route().current('dashboard')"
                class="dark:text-white"
            >
                {{t('nav.dashboard')}}
            </Link>

            <form v-if="isLoggedIn" @submit.prevent="logout" class="py-2">
                <button type="submit" class="dark:text-white">{{t('global.logout')}}</button>
            </form>
        </div>

        <div class="pt-2 pb-1 border-t border-gray-200">
            <div class="mb-2">
                <Link
                    v-if="!isLoggedIn"
                    class="dark:text-white"
                    :href="route('login')"
                >
                    {{t('global.login')}}
                </Link>
            </div>
            <div class="mb-2">
                <Link
                    v-if="!isLoggedIn"
                    class="dark:text-white"
                    :href="route('register')"
                >
                    {{t('global.register')}}
                </Link>
            </div>
        </div>
    </div>
</template>