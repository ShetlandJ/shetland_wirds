<script setup>
import { useForm } from "@inertiajs/inertia-vue3";
import { formatDate } from "../../utils/formatters";
import { Inertia } from "@inertiajs/inertia";

defineProps({
    users: {
        type: Array,
        default: () => [],
    },
    roles: {
        type: Array,
        default: () => [],
    },
});

const fields = ["User", "Email", "Roles", "Joined", ""];

const cellClass = "px-5 py-5 border-b border-gray-200 bg-white text-sm";

const updateUserRoles = (userUuid, roleUuid) => {
    Inertia.patch(route("users.update"), {
        userUuid,
        roleUuid,
    });
};
</script>

<template>
    <div class="mx-4 mx-8 px-4 px-8 py-4 overflow-x-auto">
        <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            v-for="field in fields"
                            :key="field"
                            class="
                                px-5
                                py-3
                                border-b-2 border-gray-200
                                bg-gray-100
                                text-left text-xs
                                font-semibold
                                text-gray-600
                                uppercase
                                tracking-wider
                            "
                        >
                            {{ field }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users" :key="user.id">
                        <td :class="cellClass">
                            <div class="text-lg">
                                <b>{{ user.name }}</b>
                            </div>
                            <div v-if="user.word_count > 0">
                                {{ user.word_count }} word{{
                                    user.word_count === 1 ? "" : "s"
                                }}
                            </div>
                            <div v-else>No words submitted yet</div>
                        </td>

                        <td :class="cellClass">
                            {{ user.email }}
                        </td>
                        <td :class="cellClass">
                            <div
                                v-for="role in roles"
                                class="flex items-center"
                                :key="`${user.id}-${role.id}`"
                            >
                                <!-- Create a checkbox for each role -->
                                <input
                                    type="checkbox"
                                    :id="`${user.id}-${role.id}`"
                                    :value="role.id"
                                    :checked="
                                        user.roles
                                            .map((role) => role.id)
                                            .includes(role.id)
                                    "
                                    class="mr-2"
                                    @click="updateUserRoles(user.id, role.id)"
                                />
                                <label :for="`${user.id}-${role.id}`">
                                    {{ role.name }}
                                </label>
                            </div>
                            <!-- {{ user.roles.map(role => role.name) }} -->
                        </td>
                        <td :class="cellClass">
                            {{ formatDate(new Date(user.created_at)) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style>
td {
    vertical-align: top;
    text-align: left;
}
</style>