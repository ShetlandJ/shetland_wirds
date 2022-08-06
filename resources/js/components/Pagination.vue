<script setup>
import { useI18n } from "vue-i18n";
const { t } = useI18n();
import { ref } from 'vue';

const emit = defineEmits(["page-change"]);

const props = defineProps({
    pagination: Object,
});

const changePage = (pageNumber) => {
    if (pageNumber < 1 || pageNumber > props.pagination.pages) {
        return;
    }

    emit("page-change", Number(pageNumber));
};

const newPage = ref(props.pagination.page);
</script>

<template>
    <div class="mx-2">
        <div class="flex mb-4">
            <button
                class="
                    flex
                    items-center
                    disabled:opacity-50
                    cursor-pointer
                    dark:text-white
                "
                @click="changePage(Number(pagination.page - 1))"
                :disabled="pagination.page === 1"
            >
                <svg
                    class="h-5 w-5 mr-2 fill-current"
                    version="1.1"
                    id="Layer_1"
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink"
                    x="0px"
                    y="0px"
                    viewBox="-49 141 512 512"
                    style="enable-background: new -49 141 512 512"
                    xml:space="preserve"
                >
                    <path
                        id="XMLID_10_"
                        d="M438,372H36.355l72.822-72.822c9.763-9.763,9.763-25.592,0-35.355c-9.763-9.764-25.593-9.762-35.355,0 l-115.5,115.5C-46.366,384.01-49,390.369-49,397s2.634,12.989,7.322,17.678l115.5,115.5c9.763,9.762,25.593,9.763,35.355,0 c9.763-9.763,9.763-25.592,0-35.355L36.355,422H438c13.808,0,25-11.193,25-25S451.808,372,438,372z"
                    ></path>
                </svg>
                {{ t("nav.previousPage") }}
            </button>

            <div class="mx-4 flex items-center dark:text-white text-center">
                <i18n-t keypath="nav.pagination" type="span">
                    <template #page> {{ pagination.page }} </template>
                    <template #totalPages> {{ pagination.pages }} </template>
                </i18n-t>
            </div>

            <button
                class="
                    flex
                    items-center
                    disabled:opacity-50
                    cursor-pointer
                    dark:text-white
                "
                :disabled="pagination.page === pagination.pages"
                @click="changePage(pagination.page + 1)"
            >
                {{ t("nav.nextPage") }}
                <svg
                    class="h-5 w-5 ml-2 fill-current"
                    clasversion="1.1"
                    id="Layer_1"
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink"
                    x="0px"
                    y="0px"
                    viewBox="-49 141 512 512"
                    style="enable-background: new -49 141 512 512"
                    xml:space="preserve"
                >
                    <path
                        id="XMLID_11_"
                        d="M-24,422h401.645l-72.822,72.822c-9.763,9.763-9.763,25.592,0,35.355c9.763,9.764,25.593,9.762,35.355,0
                    l115.5-115.5C460.366,409.989,463,403.63,463,397s-2.634-12.989-7.322-17.678l-115.5-115.5c-9.763-9.762-25.593-9.763-35.355,0
                    c-9.763,9.763-9.763,25.592,0,35.355l72.822,72.822H-24c-13.808,0-25,11.193-25,25S-37.808,422-24,422z"
                    />
                </svg>
            </button>
        </div>
        <div class="flex justify-center">
            <span class="flex items-center">Jump:</span>
            <input
                type="number"
                class="flex items-center mx-2 dark:text-white w-1/4"
                v-model="newPage"
            />
            <ActionButton @click="changePage(newPage)">
                Go
            </ActionButton>
        </div>
    </div>
</template>