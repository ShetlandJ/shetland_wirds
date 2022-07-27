<script setup>
import { Head, useForm } from '@inertiajs/inertia-vue3';
import JetAuthenticationCard from '@/Jetstream/AuthenticationCard.vue';
import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo.vue';
import JetButton from '@/Jetstream/Button.vue';
import JetInput from '@/Jetstream/Input.vue';
import JetLabel from '@/Jetstream/Label.vue';
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue';

import { useI18n } from "vue-i18n";
const { t } = useI18n();

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot Password" />

    <JetAuthenticationCard>
        <template #logo>

        </template>

        <div class="mb-4 text-sm text-gray-600">
           {{t('forgot.desc')}}
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <JetValidationErrors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <JetLabel for="email" :value="t('forgot.email')" />
                <JetInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                />
            </div>

            <div class="flex items-center justify-end mt-4">
                <JetButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{t('forgot.action')}}
                </JetButton>
            </div>
        </form>
    </JetAuthenticationCard>
</template>
