<script setup>
import JetDropdown from "@/Jetstream/Dropdown.vue";
import ShetlandFlag from "./icons/ShetlandFlag.vue";
import { computed } from "@vue/runtime-core";

defineProps({
    showGuide: {
        type: Boolean,
        default: false,
    },
})

const setLanguage = (language) => {
    if (language === getLocale()) return;
    localStorage.setItem("spaekationary-locale", language);
    window.location.reload();
};

const getLocale = () => {
    return localStorage.getItem("spaekationary-locale") || "en";
};

const shetlandSelected = computed(() => {
    return getLocale() === "shet";
});

const flagEmoji = computed(() => {
    switch (getLocale()) {
        case "en":
            return "🏴󠁧󠁢󠁳󠁣󠁴󠁿";
        default:
            return "🏴󠁧󠁢󠁳󠁣󠁴󠁿";
    }
});

const languages = [
    {
        name: "English",
        value: "en",
        flag: "🏴󠁧󠁢󠁳󠁣󠁴󠁿",
    },
    {
        name: "Shetland",
        value: "shet",
    },
];
</script>

<template>
    <JetDropdown>
        <template #trigger>
            <template v-if="shetlandSelected">
               <div class="flex">
                    <ShetlandFlag />
                    <span v-if="showGuide" class="ml-2 dark:text-white">
                        Change language
                    </span>
                </div>
            </template>

            <template v-else>
                <div class="flex">
                    <span style="font-size: 1.7rem">{{ flagEmoji }}</span>
                    <span v-if="showGuide" class="ml-2 dark:text-white">
                        Change language
                    </span>
                </div>
            </template>
        </template>
        <template #content>
            <div class="m-2">
                <p class="mb-4 dark:text-white">Select your language:</p>

                <template v-for="(language, index) in languages" :key="index">
                    <a
                        @click="setLanguage(language.value)"
                        class="
                            flex
                            items-center
                            hover:bg-gray-100
                            dark:bg-gray-900
                        "
                    >
                        <div class="flex items-center dark:text-white">
                            <span
                                v-if="language.value !== 'shet'"
                                class="text-4xl mr-2"
                                >{{language.flag}}</span
                            >
                            <span v-if="language.value === 'shet'" class="mr-2"
                                ><ShetlandFlag
                            /></span>
                            {{ language.name }}
                        </div>
                    </a>
                </template>
            </div>
        </template>
    </JetDropdown>
</template>
