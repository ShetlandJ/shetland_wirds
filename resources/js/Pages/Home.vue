<script setup>
import { ref, computed } from "vue";
import { Head, useForm, Link } from "@inertiajs/inertia-vue3";
import WordResult from "../components/WordResult.vue";
import NavBar from "../components/NavBar.vue";

import formatDistanceToNow from "date-fns/formatDistanceToNow";
import format from "date-fns/format";
const DATE_FORMAT = "d MMM yy";

const humanReadable = (d) => {
    return formatDistanceToNow(d);
};

import { useI18n } from "vue-i18n";
const { t } = useI18n();

let searchString = "";

const newWord = useForm({
    word: "",
    translation: "",
    example_sentence: "",
});

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    isLoggedIn: Boolean,
    words: Array,
    pagination: Object,
    searchString: String,
    letter: String,
    randomWord: String,
    featuredWord: Object,
    latestContent: Array,
});

const form = useForm({
    page: 1,
    searchString: "",
});

const today = () => {
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date().toLocaleDateString(undefined, options);
};

const convertMonthToI18n = (dateString) => {
    const monthName = dateString.replace(/[0-9]/g, "").replace(" ,", "").trim();
    const i18nMonth = t(`months.${monthName.toLowerCase()}`);

    return dateString.replace(monthName, i18nMonth);
};

const latestContentFeature = ref(props.latestContent[0]);

const progress = ref(0);

// when progress is zero, increase it to 100 over 7 seconds
const progressInterval = setInterval(() => {
    if (progress.value < 100) {
        progress.value += 1;
    } else {
        clearInterval(progressInterval);
    }
}, 100);

setInterval(() => {
    progress.value = 0;
    const latestContentWithoutFeature = props.latestContent.filter(
        (content) => content.id !== latestContentFeature.value.id
    );

    const randomIndex = Math.floor(
        Math.random() * latestContentWithoutFeature.length
    );
    latestContentFeature.value = latestContentWithoutFeature[randomIndex];
}, 10000);
</script>

<template>
    <Head title="Shaetlan dictionary" />

    <div
        class="
            items-top
            justify-center
            min-h-screen
            bg-gray-100
            dark:bg-gray-900
            sm:items-center sm:pt-0
        "
        style="padding-bottom: 25px"
    >
        <NavBar
            :can-login="canLogin"
            :can-register="canRegister"
            :is-logged-in="isLoggedIn"
            @set-search="searchString = $event"
            @suggest-word="toggleSuggestWordForm(true)"
        />

        <Container>
            <div class="text-center">
                <p class="flex text-xl justify-center mb-2 dark:text-white">
                    <b>Da Spaektionary</b>
                </p>
                <p class="dark:text-white text-lg mb-4">
                    {{ t("home.welcome1") }}
                </p>

                <p class="dark:text-white text-lg mb-4">
                    {{ t("home.welcome2") }}
                </p>

                <p class="dark:text-white text-lg mb-4">
                    {{ t("home.welcome3") }}
                </p>

                <div
                    class="dark:text-white text-lg mb-4"
                    style="display: block"
                >
                    <i18n-t keypath="home.welcome4" tag="span" for="here">
                        <template #here>
                            <Link
                                class="text-sm text-gray-700 underline"
                                :href="route('register')"
                                style="display: inline-flex !important"
                            >
                                <h2
                                    className="
                                font-semibold
                                text-lg
                                text-gray-900
                                -mt-1
                                dark:text-white
                                dark:border-b
                            "
                                >
                                    {{ t("global.here") }}
                                </h2>
                            </Link>
                        </template>
                    </i18n-t>
                </div>

                <Alert v-if="featuredWord" class="mb-4">
                    <div class="dark:text-white flex" style="display: block">
                        <i18n-t keypath="home.featured" tag="span">
                            <template #today>{{
                                convertMonthToI18n(today())
                            }}</template>
                            <template #featuredWord>
                                <Link
                                    v-if="featuredWord"
                                    :href="
                                        route('word.comments', {
                                            slug: featuredWord.slug,
                                        })
                                    "
                                    class="text-sm text-blue-700 underline"
                                    style="display: inline-flex !important"
                                >
                                    <h2
                                        className="
                                font-semibold
                                text-lg
                                -mt-1
                                dark:text-white
                                dark:border-b
                            "
                                    >
                                        {{ featuredWord.word }}
                                    </h2> </Link
                                >.
                            </template>
                        </i18n-t>

                        <span>{{ t("home.findOutMore") }}</span>
                    </div>
                </Alert>

                <Alert variant="success">
                    <div class="text-lg justify-center dark:text-white flex">
                        {{t('home.newlyAdded')}}
                        {{ t(`word.${latestContentFeature.content_type}`) }}
                        <!-- {{ latestContentFeature.content_type }} -->
                        <span
                            v-if="latestContentFeature.content_type === 'word'"
                            class="ml-1"
                        >
                            <a
                                class="underline"
                                :href="`/word/${latestContentFeature.slug}`"
                            >
                                {{ latestContentFeature.word }}
                            </a>
                            <span class="text-sm ml-1"
                                >({{
                                    humanReadable(
                                        new Date(
                                            latestContentFeature.created_at
                                        )
                                    )
                                }}
                                ago)</span
                            >
                        </span>
                        <span
                            v-if="
                                latestContentFeature.content_type ===
                                'recording'
                            "
                            class="ml-1"
                        >
                            {{t('home.forTheWord')}}
                            <a
                                class="underline"
                                :href="`/word/${latestContentFeature.slug}`"
                                >{{ latestContentFeature.word }}</a
                            >
                            <span class="text-sm ml-1"
                                >({{
                                    humanReadable(
                                        new Date(
                                            latestContentFeature.created_at
                                        )
                                    )
                                }}
                                ago)</span
                            >
                        </span>
                        <span
                            v-if="
                                latestContentFeature.content_type === 'comment'
                            "
                            class="ml-1"
                        >
                            {{t('home.forTheWord')}}
                            <a
                                class="underline"
                                :href="`/word/${latestContentFeature.slug}`"
                                >{{ latestContentFeature.word }}</a
                            >
                            <span class="text-sm ml-1"
                                >({{
                                    humanReadable(
                                        new Date(
                                            latestContentFeature.created_at
                                        )
                                    )
                                }}
                                ago)</span
                            >
                        </span>
                    </div>
                    <progress
                        style="height: 3px; width: 100%"
                        id="progress-bar"
                        max="100"
                        :value="progress"
                    />
                </Alert>
            </div>
        </Container>

        <Container>
            <div class="flex justify-center">
                <iframe
                    width="560"
                    height="315"
                    src="https://www.youtube.com/embed/Is7EIylRMvM"
                    title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                />
            </div>

            <div class="flex justify-center mt-4">
                <p class="text-lg">
                    <a class="underline dark:text-white" href="/tutorial">
                        {{ t("home.tutorial") }}
                    </a>
                </p>
            </div>
        </Container>
    </div>
</template>

<style>
progress::-webkit-progress-bar {
    background-color: white;
}

progress::-webkit-progress-value {
  transition: width 0.1s;
}

.bg-gray-100 {
    background-color: #f7fafc;
    background-color: rgba(247, 250, 252, var(--tw-bg-opacity));
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
}
</style>
