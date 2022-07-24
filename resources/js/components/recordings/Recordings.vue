<script setup>
import { usePage } from "@inertiajs/inertia-vue3";
import { reactive } from "@vue/runtime-core";
import Recording from "./Recording.vue";
import RecordingInput from "./RecordingInput.vue";

import { useI18n } from "vue-i18n";
const { t } = useI18n();

const { word, isLoggedIn, success, userHasPendingRecordings, user } = usePage().props.value;
</script>

<template>
    <div v-if="word">
        <Alert
            v-if="success && !user.is_trusted"
            variant="success"
            :message="t('word.recordings.successAlert')"
        />

        <template v-else>
            <Alert
                v-if="!word.recordings.length"
                :message="t('word.recordings.noRecordings')"
            />

            <Alert v-if="userHasPendingRecordings && !user.is_trusted" variant="success" class="mb-4">
                {{t('word.recordings.pendingAlert')}}
            </Alert>

            <Recording
                v-for="(recording, index) in word.recordings"
                :recording="recording"
                :key="recording.id"
                :index="index"
            />

            <h4
                class="
                    my-5
                    uppercase
                    tracking-wide
                    text-gray-400
                    font-bold
                    text-xs
                "
            >
                {{t('word.recordings.addYourOwn')}}
            </h4>
            <template v-if="isLoggedIn">
                <p class="mb-2 dark:text-white">
                     {{t('word.recordings.instruction1')}}
                </p>
                <p class="mb-2 dark:text-white">
                     {{t('word.recordings.instruction2')}}
                </p>

                <RecordingInput :key="word.recordings.length" />
            </template>

            <Alert v-else :message="t('word.recordings.signUp')" />
        </template>
    </div>
</template>