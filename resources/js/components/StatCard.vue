<script setup>
import WaitingIcon from '../components/icons/WaitingIcon.vue';
import RejectedIcon from '../components/icons/RejectedIcon.vue';
import CheckIcon from '../components/icons/CheckIcon.vue';

import HeartIcon from '../components/icons/HeartIcon.vue';
import CommentIcon from '../components/icons/CommentIcon.vue';

import {ref} from 'vue';

const props = defineProps({
    name: String,
    stat: [String, Number],
    type: String,
});

let icon = ref(WaitingIcon);

switch (props.type) {
    case 'pending':
        icon = WaitingIcon;
        break;
    case 'rejected':
        icon = RejectedIcon;
        break;
    case 'likes':
        icon = HeartIcon;
        break;
    case 'comments':
        icon = CommentIcon;
        break;
    default:
        icon = CheckIcon;
        break;
}

</script>

<template>
    <div
        class="
            min-w-0
            rounded-lg
            shadow-xs
            overflow-hidden
            bg-white
            dark:bg-gray-800
        "
    >
        <div class="p-4 flex items-center">
            <div style="height: 45px; width: 45px" class="mr-6">
                <component :is="icon" />
            </div>
            <div>
                <p
                    class="
                        mb-2
                        text-md
                        font-medium
                        text-gray-600
                        dark:text-gray-400
                    "
                >
                    {{name}}
                </p>
                <h4
                    class="
                        text-3xl
                        font-semibold
                        text-gray-700
                        dark:text-gray-200
                    "
                >
                    {{stat}}
                </h4>
            </div>
        </div>
    </div>
</template>