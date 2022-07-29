<script setup>
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";
import JetAuthenticationCard from "@/Jetstream/AuthenticationCard.vue";
import JetAuthenticationCardLogo from "@/Jetstream/AuthenticationCardLogo.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import { ref } from "vue";

const Filter = require("bad-words");
const swearFilter = new Filter();

import { useI18n } from "vue-i18n";
const { t } = useI18n();

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    terms: false,
    can_contact: false,
});

const inappropriateName = ref(false);

const submit = () => {
    // check if bad words are in the name
    if (swearFilter.isProfane(form.name)) {
        inappropriateName.value = true;
        return;
    }

    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head title="Register" />

    <JetAuthenticationCard>
        <div class="text-center">
            <h1 class="leading-none text-2xl text-grey-darkest mb-3">
                <p
                    class="
                        no-underline
                        text-grey-darkest
                        hover:text-black
                        dark:text-white
                    "
                    :href="route('home')"
                >
                    <span class="dark:text-white">Da Spaektionary</span>
                </p>
            </h1>

            <p>
                {{ t("register.desc") }}
            </p>
        </div>

        <Alert
            v-if="inappropriateName"
            variant="warning"
            class="my-4"
            :message="t('register.badLanguage')"
        />

        <form @submit.prevent="submit">
            <div>
                <JetLabel for="name" :value="t('register.name')" />
                <JetInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="name"
                />
            </div>

            <div class="mt-4">
                <JetLabel for="email" :value="t('register.email')" />
                <JetInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                />
            </div>

            <div class="mt-4">
                <JetLabel for="password" :value="t('register.password')" />
                <JetInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
            </div>

            <div class="mt-4">
                <JetLabel
                    for="password_confirmation"
                    :value="t('register.confirmPassword')"
                />
                <JetInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
            </div>

            <div class="mt-4 flex">
                <input
                    type="checkbox"
                    class="form-check-input mt-2"
                    id="can_contact"
                    v-model="form.can_contact"
                />
                <label
                    class="ml-2 form-check-label dark:text-white"
                    for="can_contact"
                >
                    {{ t("register.canContact") }}
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 hover:text-gray-900"
                >
                    {{ t("register.alreadyRegistered") }}?
                </Link>

                <JetButton
                    class="ml-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ t("register.register") }}
                </JetButton>
            </div>
        </form>
    </JetAuthenticationCard>
</template>
