<script setup>
import { useI18n } from "vue-i18n";
const { t } = useI18n();
</script>

<template>
    <div>
        <p class="dark:text-white mb-4 text-lg font-bold">
            {{ t("faq.question1") }}
        </p>
        <p class="dark:text-white mb-4">
            <i18n-t keypath="faq.answer1" type="span">
                <template #about>
                    <a class="underline" href="/about">{{ t("nav.about") }}</a>
                </template>
            </i18n-t>
        </p>

        <p class="dark:text-white mb-4 text-lg font-bold">
            {{ t("faq.question2") }}
        </p>
        <p class="dark:text-white mb-2">
            <i18n-t keypath="faq.answer2p-1" type="span">
                <template #here>
                    <a
                        class="underline"
                        href="https://www.iheardee.com/english/history-of-shaetlan"
                        >{{ t("global.here") }}</a
                    >
                </template>
            </i18n-t>
        </p>
        <p class="dark:text-white mb-4">
            <i18n-t keypath="faq.answer2p-2" type="span">
                <template #here>
                    <a
                        class="underline"
                        href="https://www.iheardee.com/english/spelling-in-shaetlan"
                        >{{ t("global.here") }}</a
                    >
                </template>
            </i18n-t>
        </p>

        <p class="dark:text-white mb-4 text-lg font-bold">
            {{ t("faq.question3") }}
        </p>
        <p class="dark:text-white mb-4">
            <i18n-t keypath="faq.answer3" type="span">
                <template #worldAtlasLink>
                    <a
                        class="underline"
                        href="https://ewave-atlas.org"
                        >
                        World Atlas of Varieties of English
                    </a>
                </template>

                <template #micronesia>
                    <a
                        class="underline"
                        href="http://english-in-micronesia.weebly.com"
                        >
                        English in Micronesia
                    </a>
                </template>

                <template #dialectsArchive>
                    <a
                        class="underline"
                        href="https://www.dialectsarchive.com"
                        >
                        International Dialects of English Archive
                    </a>
                </template>

                <template #here>
                    <a
                        class="underline"
                        href="/Velupillai_NS_ShaetlanSpellingKeyboard.pdf"
                        >{{ t("global.here") }}</a
                    >
                </template>
            </i18n-t>
        </p>

        <p class="dark:text-white mb-4 text-lg font-bold">
            {{ t("faq.question4") }}
        </p>
        <p class="dark:text-white mb-4">
            {{ t("faq.answer4") }}
        </p>

        <p class="dark:text-white mb-4 text-lg font-bold">
            {{ t("faq.question5") }}
        </p>
        <div class="dark:text-white mb-4">
            <p class="dark:text-white mb-2">
                {{ t("faq.answer5-p1") }}
            </p>
            <p class="dark:text-white mb-2">
                {{ t("faq.answer5-p2") }}
            </p>
            <p class="dark:text-white mb-2">
                {{ t("faq.answer5-p3") }}
            </p>
            <p class="dark:text-white mb-2">
                <i18n-t keypath="faq.answer5-p4" type="span">
                    <template #here>
                        <a class="underline" href="/create">{{t('global.contribute')}}</a>
                    </template>
                </i18n-t>
            </p>
        </div>

        <p class="dark:text-white mb-4 text-lg font-bold">
            {{ t("faq.question6") }}
        </p>
        <p class="dark:text-white mb-4">
            <i18n-t keypath="faq.answer6" type="span">
                <template #ihd>
                    <a class="underline" href="/https://www.iheardee.com">I Hear Dee</a>
                </template>
            </i18n-t>
        </p>
    </div>
</template>