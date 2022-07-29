<script setup>
import JetDropdown from "@/Jetstream/Dropdown.vue";
import ScotlandFlag from "./icons/ScotlandFlag.vue";
import ShetlandFlag from "./icons/ShetlandFlag.vue";
import { computed } from "@vue/runtime-core";

import { useI18n } from "vue-i18n";
const { t } = useI18n();

defineProps({
    showGuide: {
        type: Boolean,
        default: false,
    },
});

const setLanguage = (language) => {
    if (language === getLocale()) return;
    localStorage.setItem("spaekationary-locale", language);
    window.location.reload();
};

const getLocale = () => {
    return localStorage.getItem("spaekationary-locale") || "shet";
};

const shetlandSelected = computed(() => {
    return getLocale() === "shet";
});

const languages = [
    {
        name: "English",
        value: "en",
    },
    {
        name: "Shaetlan",
        value: "shet",
    },
];
</script>

<template>
    <JetDropdown>
        <template #trigger>
            <div class="flex">
                <ShetlandFlag v-if="shetlandSelected" />
                <ScotlandFlag style="height: 22px; width: 33px" v-else />
                <span v-if="showGuide" class="ml-2 dark:text-white">
                    {{ t("language.change") }}
                </span>
            </div>
        </template>

        <template #content>
            <div class="m-2">
                <p class="mb-4 dark:text-white">{{ t("language.select") }}:</p>

                <template v-for="(language, index) in languages" :key="index">
                    <a
                        @click="setLanguage(language.value)"
                        class="
                            flex
                            items-center
                            hover:bg-gray-100
                            dark:bg-gray-900
                            my-2
                        "
                    >
                        <div class="flex items-center dark:text-white">
                            <span
                                v-if="language.value !== 'shet'"
                                class="text-4xl mr-2"
                            >
                                <ScotlandFlag
                                    style="height: 22px; width: 33px"
                                />
                            </span>
                            <span v-if="language.value === 'shet'" class="mr-2">
                                <ShetlandFlag />
                            </span>
                            {{ language.name }}
                        </div>
                    </a>
                </template>
            </div>
        </template>
    </JetDropdown>
</template>
