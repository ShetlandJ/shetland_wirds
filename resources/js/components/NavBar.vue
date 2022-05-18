<script setup>
import { Link, useForm, InertiaApp } from "@inertiajs/inertia-vue3";

const emit = defineEmits(["setSearch", "suggest-word"]);

const form = useForm({
    searchString: "",
});

const suggestWord = () => {
    emit("suggest-word");
};

const search = () => {
    emit("set-search", form.searchString);
    form.transform((data) => ({
        ...data,
    })).post(route("search", { searchTerm: form.searchString }), {
        searchString: form.searchString,
    });
};

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    isLoggedIn: Boolean,
});
</script>

<template>
    <header
        class="
            border-b
            md:flex md:items-center md:justify-between
            p-4
            pb-0
            shadow-lg
            md:pb-4
        "
    >
        <div class="flex items-center justify-between mb-4 md:mb-0">
            <h1 class="leading-none text-2xl text-grey-darkest">
                <a
                    class="no-underline text-grey-darkest hover:text-black"
                    href="#"
                >
                    <span class="font-bold">Wir</span>ds
                </a>
            </h1>

            <a class="text-black hover:text-orange md:hidden" href="#">
                <i class="fa fa-2x fa-bars"></i>
            </a>
        </div>

        <form class="mb-4 w-full md:mb-0 md:w-1/4" @submit.prevent="search">
            <div class="flex justify-center">
                <div class="xl:w-96">
                    <div class="input-group relative flex items-stretch w-full">
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
                </div>
            </div>
        </form>

        <nav>
            <ul class="list-reset md:flex md:items-center">
                <template v-if="!isLoggedIn">
                    <li class="md:ml-4" v-if="!isLoggedIn">
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
                            :href="route('login')"
                        >
                            Login
                        </Link>
                    </li>
                    <li class="md:ml-4" v-if="!isLoggedIn">
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
                            "
                            :href="route('register')"
                        >
                            Register
                        </Link>
                    </li>
                </template>
                <li class="md:ml-4" v-if="isLoggedIn">
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
                        "
                        :href="route('dashboard')"
                    >
                        Dashboard
                    </Link>
                </li>
                <li class="md:ml-4">
                    <button
                        class="
                            border-t
                            block
                            no-underline
                            hover:underline
                            py-2
                            text-grey-darkest
                            hover:text-black
                            md:border-none md:p-0
                        "
                        @click="suggestWord"
                    >
                        Add word/phrase
                    </button>
                </li>
            </ul>
        </nav>
    </header>
</template>

<style scoped>
input {
    width: 100%;
}
</style>