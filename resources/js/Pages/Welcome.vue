<script setup>
import { Head, Link } from "@inertiajs/inertia-vue3";
import SearchBar from '../components/SearchBar.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Welcome" />

    <div
        class="
            items-top
            justify-center
            min-h-screen
            bg-gray-100
            dark:bg-gray-900
            sm:items-center sm:pt-0
        "
    >
        <div
            v-if="canLogin"
            class="hidden fixed top-0 right-0 px-6 py-4 sm:block"
        >
            <Link
                v-if="$page.props.user"
                :href="route('dashboard')"
                class="text-sm text-gray-700 underline"
            >
                Dashboard
            </Link>

            <template v-else>
                <Link
                    :href="route('login')"
                    class="text-sm text-gray-700 underline"
                >
                    Log in
                </Link>

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="ml-4 text-sm text-gray-700 underline"
                >
                    Register
                </Link>
            </template>
        </div>

        <!-- <div class="flex justify-center">
            <div class="mb-3 xl:w-96">
                <div
                    class="input-group relative flex items-stretch w-full mb-4 mt-2"
                >
                    <input
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
                        placeholder="Search Shetland or English word"
                        aria-label="Search"
                        aria-describedby="button-addon3"
                    />
                    <button
                        class="
                            btn
                            inline-block
                            px-6
                            py-2
                            border-2 border-blue-600
                            text-blue-600
                            font-medium
                            text-xs
                            leading-tight
                            uppercase
                            rounded
                            hover:bg-black hover:bg-opacity-5
                            focus:outline-none focus:ring-0
                            transition
                            duration-150
                            ease-in-out
                        "
                        type="button"
                        id="button-addon3"
                    >
                        Search
                    </button>
                </div>
            </div> -->
        <!-- </div> -->
        <!-- component -->

        <div class="max-w-2xl mx-auto">
            <!-- <form>
                <label
                    for="default-search"
                    class="
                        mb-2
                        text-sm
                        font-medium
                        text-gray-900
                        sr-only
                        dark:text-gray-300
                    "
                    >Search</label
                >
                <div class="relative">
                    <div class="pt-2">
                    <div
                        class="
                            flex
                            absolute
                            inset-y-0
                            left-0
                            items-center
                            pl-3
                            pointer-events-none
                        "
                    >
                        <svg
                            class="w-5 h-5 text-gray-500 dark:text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            ></path>
                        </svg>
                    </div>
                    <input
                        type="search"
                        id="default-search"
                        class="
                            block
                            p-4
                            pl-10
                            w-full
                            text-sm text-gray-900
                            bg-gray-50
                            rounded-lg
                            border border-gray-300
                            focus:ring-blue-500 focus:border-blue-500
                            dark:bg-gray-700
                            dark:border-gray-600
                            dark:placeholder-gray-400
                            dark:text-white
                            dark:focus:ring-blue-500
                            dark:focus:border-blue-500
                        "
                        placeholder="Search a Shetland or English word..."
                        required
                    />
                    <button
                        type="submit"
                        class="
                            text-white
                            absolute
                            right-2.5
                            bottom-2.5
                            bg-blue-700
                            hover:bg-blue-800
                            focus:ring-4 focus:outline-none focus:ring-blue-300
                            font-medium
                            rounded-lg
                            text-sm
                            px-4
                            py-2
                            dark:bg-blue-600
                            dark:hover:bg-blue-700
                            dark:focus:ring-blue-800
                        "
                    >
                        Search
                    </button>
                    </div>
                </div>
            </form> -->
            <SearchBar />
        </div>
    </div>
</template>

<style scoped>
.bg-gray-100 {
    background-color: #f7fafc;
    background-color: rgba(247, 250, 252, var(--tw-bg-opacity));
}

.border-gray-200 {
    border-color: #edf2f7;
    border-color: rgba(237, 242, 247, var(--tw-border-opacity));
}

.text-gray-400 {
    color: #cbd5e0;
    color: rgba(203, 213, 224, var(--tw-text-opacity));
}

.text-gray-500 {
    color: #a0aec0;
    color: rgba(160, 174, 192, var(--tw-text-opacity));
}

.text-gray-600 {
    color: #718096;
    color: rgba(113, 128, 150, var(--tw-text-opacity));
}

.text-gray-700 {
    color: #4a5568;
    color: rgba(74, 85, 104, var(--tw-text-opacity));
}

.text-gray-900 {
    color: #1a202c;
    color: rgba(26, 32, 44, var(--tw-text-opacity));
}

@media (prefers-color-scheme: dark) {
    .dark\:bg-gray-800 {
        background-color: #2d3748;
        background-color: rgba(45, 55, 72, var(--tw-bg-opacity));
    }

    .dark\:bg-gray-900 {
        background-color: #1a202c;
        background-color: rgba(26, 32, 44, var(--tw-bg-opacity));
    }

    .dark\:border-gray-700 {
        border-color: #4a5568;
        border-color: rgba(74, 85, 104, var(--tw-border-opacity));
    }

    .dark\:text-white {
        color: #fff;
        color: rgba(255, 255, 255, var(--tw-text-opacity));
    }

    .dark\:text-gray-400 {
        color: #cbd5e0;
        color: rgba(203, 213, 224, var(--tw-text-opacity));
    }
}
</style>
