<script setup>
import { Inertia } from "@inertiajs/inertia";
import { Link, useForm, usePage, InertiaApp } from "@inertiajs/inertia-vue3";
import JetDropdown from "@/Jetstream/Dropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink.vue";
import MobileMenu from "./MobileMenu.vue";
import Hamburger from "./Hamburger.vue";
import LanguageSelector from './LanguageSelector.vue';
import { ref } from "vue";

import { useI18n } from "vue-i18n";
const { t } = useI18n();

const emit = defineEmits(["setSearch", "suggest-word"]);

const form = useForm({
    searchString: "",
});

const suggestWord = () => {
    emit("suggest-word");
};

const search = () => {
    if (form.searchString.length === 0) {
        return;
    }

    emit("set-search", form.searchString);
    form.transform((data) => ({
        ...data,
    })).post(route("search", { searchTerm: form.searchString }), {
        searchString: form.searchString,
    });
};

const randomWordSlug = usePage().props.value.randomWord;

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    isLoggedIn: Boolean,
});

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

const showingNavigationDropdown = ref(false);

const logout = () => {
    Inertia.post(route("logout"));
};

const userIsTrusted = usePage().props.value.user?.is_trusted;
</script>

<template>
    <header
        class="
            border-b
            lg:flex
            md:items-center md:justify-between
            p-4
            pb-0
            shadow-lg
            md:pb-4
            bg-gray-100
            dark:bg-gray-800
        "
    >
        <div class="flex items-center justify-between mb-4 md:mb-0">
            <div class="flex items-center">
                <h1 class="leading-none text-2xl text-grey-darkest">
                    <Link
                        class="
                            no-underline
                            text-grey-darkest
                            hover:text-black
                            dark:text-white
                        "
                        :href="route('home')"
                    >
                        <span class="dark:text-white">Da Spaektionary</span>
                    </Link>
                </h1>

                <form
                    class="
                        md:grow
                        md:flex
                        mb-4
                        ml-4
                        w-full
                        md:mb-0
                        lg:w-1/4 lg:flex
                        md:hidden
                        hidden
                    "
                    @submit.prevent="search"
                >
                    <div class="flex justify-center">
                        <div
                            class="
                                input-group
                                relative
                                flex
                                items-stretch
                                w-full
                            "
                        >
                            <div class="flex">
                                <input
                                    v-model="form.searchString"
                                    size="300"
                                    type="search"
                                    style="max-width: 300px;"
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
                                    :placeholder="t('nav.searchPlaceholder')"
                                    aria-label="Search"
                                    aria-describedby="button-addon2"
                                />
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
                                    type="submit"
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
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <Hamburger
                class="md:flex lg:hidden"
                :showing-navigation-dropdown="showingNavigationDropdown"
                @toggle="showingNavigationDropdown = !showingNavigationDropdown"
            />
        </div>

        <MobileMenu
            :showing-navigation-dropdown="showingNavigationDropdown"
            class="lg:hidden"
        />

        <form
            class="md:flex mb-4 w-full md:mb-0 lg:w-1/4 lg:hidden"
            @submit.prevent="search"
        >
            <div class="flex justify-center">
                <div class="xl:w-96">
                    <div class="input-group relative flex items-stretch w-full">
                        <div class="flex">
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
                                :placeholder="t('nav.searchPlaceholder')"
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
                                type="submit"
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
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <nav class="hidden lg:flex sm:justify-center">
            <ul
                class="list-reset md:flex md:items-center"
                style="width: max-content"
            >
                <template v-if="!isLoggedIn">
                    <li class="md:ml-4 my-2" v-if="!isLoggedIn">
                        <Link
                            class="
                                block
                                no-underline
                                hover:underline
                                py-2
                                text-grey-darkest
                                hover:text-black
                                md:border-none md:p-0
                                dark:text-white dark:hover:text-white
                            "
                            :href="route('login')"
                        >
                            {{t('global.login')}}
                        </Link>
                    </li>
                    <li class="md:ml-4 my-2" v-if="!isLoggedIn">
                        <Link
                            class="
                                border-t
                                block
                                no-underline
                                hover:underline
                                py-2
                                text-grey-darkest
                                hover:text-black
                                md:border-none md:p-0
                                dark:text-white dark:hover:text-white
                            "
                            :href="route('register')"
                        >
                            {{t('global.register')}}
                        </Link>
                    </li>
                </template>

                <li class="md:ml-4 my-2">
                    <Link
                        class="
                            border-t
                            block
                            no-underline
                            hover:underline
                            py-2
                            text-grey-darkest
                            hover:text-black
                            md:border-none md:p-0
                            dark:text-white dark:hover:text-white
                        "
                        :href="route('faq')"
                        :class="{ 'font-bold': $page.url === '/faq' }"
                    >
                        FAQs
                    </Link>
                </li>
                <li class="md:ml-4 my-2">
                    <Link
                        class="
                            border-t
                            block
                            no-underline
                            hover:underline
                            py-2
                            text-grey-darkest
                            hover:text-black
                            md:border-none md:p-0
                            dark:text-white dark:hover:text-white
                        "
                        :href="route('about')"
                        :class="{ 'font-bold': $page.url === '/about' }"
                    >
                        {{t('nav.about')}}
                    </Link>
                </li>
                <li class="md:ml-4 my-2">
                    <a
                        class="
                            border-t
                            block
                            no-underline
                            hover:underline
                            py-2
                            text-grey-darkest
                            hover:text-black
                            md:border-none md:p-0
                            dark:text-white dark:hover:text-white
                        "
                        :href="route('create')"
                        :class="{
                            'font-bold': $page.url === '/create',
                        }"
                    >
                        + {{t('nav.add')}}
                    </a>
                </li>
                <template v-if="isLoggedIn">
                    <li class="md:ml-4 my-2" v-if="userIsTrusted">
                        <Link
                            class="
                                border-t
                                block
                                no-underline
                                hover:underline
                                py-2
                                text-grey-darkest
                                dark:text-white
                                md:border-none md:p-0
                            "
                            :href="route('dashboard')"
                        >
                            {{t('nav.dashboard')}}
                        </Link>
                    </li>
                    <li class="md:ml-4 my-2">
                        <Link
                            class="
                                border-t
                                block
                                no-underline
                                hover:underline
                                py-2
                                text-grey-darkest
                                dark:text-white
                                md:border-none md:p-0
                            "
                            @click="logout"
                        >
                            {{t('global.logout')}}
                        </Link>
                    </li>
                </template>

                <JetDropdown>
                    <template #trigger>
                        <span class="inline-flex rounded-md mb-2">
                            <button
                                type="button"
                                class="
                                    mt-2
                                    md:ml-4
                                    items-center
                                    inline-flex
                                    border-t
                                    block
                                    no-underline
                                    py-2
                                    text-grey-darkest
                                    hover:text-black
                                    md:border-none md:p-0
                                    dark:text-white dark:hover:text-white
                                    cursor-pointer
                                "
                            >
                                {{t('global.browse')}}

                                <svg
                                    class="ml-2 -mr-0.5 h-4 w-4"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </span>
                    </template>

                    <template #content>
                        <div class="p-2" style="width: max-content">
                            <ul
                                class="
                                    p-0
                                    list-none
                                    m-0
                                    font-bold
                                    text-sm
                                    md:grid md:grid-cols-4 md:gap-1 md:gap-y-2
                                "
                            >
                                <li
                                    v-for="letter in alphabetArray"
                                    :key="letter"
                                >
                                    <a
                                        class="nav-link uppercase"
                                        :href="route('letter', letter)"
                                    >
                                        {{ letter }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <Link
                            v-if="randomWordSlug"
                            class="p-0 flex justify-center m-0 font-bold random"
                            :href="
                                route('word.comments', {
                                    slug: randomWordSlug,
                                })
                            "
                        >
                            {{t('global.random')}}?
                        </Link>
                    </template>
                </JetDropdown>

                <LanguageSelector class="cursor-pointer ml-4" />
            </ul>
        </nav>
    </header>
</template>

<style scoped>
input {
    width: 100%;
}

.nav-link:hover {
    background-color: rgb(19 79 230);
    color: white;
}

.nav-link {
    cursor: pointer;
    display: flex;
    justify-content: center;
    width: 100%;
    align-items: center;
    border-bottom-width: 1px;
    padding-left: 0.75rem;
    padding-right: 0.75rem;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    color: black;
    background-color: rgb(241 241 241);
}

.random {
    background-color: #ffe368;
    margin-bottom: 3px;
    cursor: pointer;
}
</style>