<script setup>
import { ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { Head, Link, usePage } from "@inertiajs/inertia-vue3";
import JetApplicationMark from "@/Jetstream/ApplicationMark.vue";
import JetBanner from "@/Jetstream/Banner.vue";
import JetDropdown from "@/Jetstream/Dropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink.vue";
import JetNavLink from "@/Jetstream/NavLink.vue";
import JetResponsiveNavLink from "@/Jetstream/ResponsiveNavLink.vue";

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    Inertia.put(
        route("current-team.update"),
        {
            team_id: team.id,
        },
        {
            preserveState: false,
        }
    );
};

const logout = () => {
    Inertia.post(route("logout"));
};

const { reports } = usePage().props.value;
</script>

<template>
    <div>
        <Head :title="title" />

        <JetBanner />

        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <JetApplicationMark
                                        class="block h-9 w-auto"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="
                                    hidden
                                    space-x-8
                                    sm:-my-px sm:ml-10 sm:flex
                                "
                            >
                                <JetNavLink
                                    :href="route('home')"
                                    :active="route().current('home')"
                                >
                                    Dictionary
                                </JetNavLink>
                                <JetNavLink
                                    :href="route('word-admin')"
                                    :active="route().current('word-admin')"
                                >
                                    Word admin
                                </JetNavLink>

                                <JetDropdown align="right" width="48">
                                    <template #trigger>
                                        <button
                                            type="button"
                                            class="
                                                inline-flex
                                                items-center
                                                border border-transparent
                                                text-sm
                                                mt-3
                                                leading-4
                                                font-medium
                                                rounded-md
                                                text-gray-500
                                                bg-white
                                                hover:text-gray-700
                                                focus:outline-none
                                                transition
                                            "
                                        >
                                            Pending

                                            <svg
                                                class="ml-2 -mr-0.5 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <JetNavLink
                                            :href="route('approval')"
                                            :active="
                                                route().current('approval')
                                            "
                                        >
                                            Pending words
                                        </JetNavLink>
                                        <JetNavLink
                                            :href="route('recordings')"
                                            :active="
                                                route().current('recordings')
                                            "
                                        >
                                            Pending recordings
                                        </JetNavLink>
                                        <JetNavLink
                                            :href="route('new-definitions')"
                                            :active="
                                                route().current(
                                                    'new-definitions'
                                                )
                                            "
                                        >
                                            Pending definitions
                                        </JetNavLink>
                                    </template>
                                </JetDropdown>

                                <JetDropdown align="right" width="48">
                                    <template #trigger>
                                        <button
                                            type="button"
                                            class="
                                                inline-flex
                                                items-center
                                                border border-transparent
                                                text-sm
                                                mt-3
                                                leading-4
                                                font-medium
                                                rounded-md
                                                text-gray-500
                                                bg-white
                                                hover:text-gray-700
                                                focus:outline-none
                                                transition
                                            "
                                        >
                                            Latest

                                            <svg
                                                class="ml-2 -mr-0.5 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <JetNavLink
                                            :href="route('latest.words')"
                                            :active="
                                                route().current('latest.words')
                                            "
                                        >
                                            Latest Words
                                        </JetNavLink>
                                        <JetNavLink
                                            :href="route('latest.recordings')"
                                            :active="
                                                route().current('latest.recordings')
                                            "
                                        >
                                            Latest Recordings
                                        </JetNavLink>
                                        <JetNavLink
                                            :href="route('latest.comments')"
                                            :active="
                                                route().current(
                                                    'latest.comments'
                                                )
                                            "
                                        >
                                            Latest Comments
                                        </JetNavLink>
                                    </template>
                                </JetDropdown>


                                <JetNavLink
                                    :href="route('wotd')"
                                    :active="route().current('wotd')"
                                >
                                    Word of the Day
                                </JetNavLink>
                                <JetNavLink
                                    :href="route('rejected')"
                                    :active="route().current('rejected')"
                                >
                                    Rejected
                                </JetNavLink>
                                <JetNavLink
                                    :href="route('revisions')"
                                    :active="route().current('revisions')"
                                >
                                    Revisions
                                </JetNavLink>
                                <JetNavLink
                                    :href="route('users')"
                                    :active="route().current('users')"
                                >
                                    Users
                                </JetNavLink>
                                <JetNavLink
                                    :href="route('reports')"
                                    :active="route().current('reports')"
                                >
                                    Reports
                                    <span v-if="reports.length > 0">
                                        <span class="text-red-500 ml-1">
                                            ({{ reports.length }})
                                        </span>
                                    </span>
                                </JetNavLink>
                            </div>
                        </div>

                        <JetDropdown align="right" width="48">
                            <template #trigger>
                                <button
                                    v-if="
                                        $page.props.jetstream
                                            .managesProfilePhotos
                                    "
                                    class="
                                        flex
                                        text-sm
                                        border-2 border-transparent
                                        rounded-full
                                        focus:outline-none focus:border-gray-300
                                        transition
                                    "
                                >
                                    <img
                                        class="
                                            h-8
                                            w-8
                                            rounded-full
                                            object-cover
                                        "
                                        :src="
                                            $page.props.user.profile_photo_url
                                        "
                                        :alt="$page.props.user.name"
                                    />
                                </button>

                                <span v-else class="inline-flex rounded-md">
                                    <button
                                        type="button"
                                        class="
                                            inline-flex
                                            items-center
                                            px-3
                                            py-2
                                            border border-transparent
                                            text-sm
                                            leading-4
                                            font-medium
                                            rounded-md
                                            text-gray-500
                                            bg-white
                                            hover:text-gray-700
                                            focus:outline-none
                                            transition
                                        "
                                    >
                                        {{ $page.props.user.name }}

                                        <svg
                                            class="ml-2 -mr-0.5 h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                </span>
                            </template>

                            <template #content>
                                <!-- Account Management -->
                                <div
                                    class="
                                        block
                                        px-4
                                        py-2
                                        text-xs text-gray-400
                                    "
                                >
                                    Manage Account
                                </div>

                                <JetDropdownLink :href="route('profile.show')">
                                    Profile
                                </JetDropdownLink>

                                <JetDropdownLink
                                    v-if="$page.props.jetstream.hasApiFeatures"
                                    :href="route('api-tokens.index')"
                                >
                                    API Tokens
                                </JetDropdownLink>

                                <div class="border-t border-gray-100" />

                                <!-- Authentication -->
                                <form @submit.prevent="logout">
                                    <JetDropdownLink as="button">
                                        Log Out
                                    </JetDropdownLink>
                                </form>
                            </template>
                        </JetDropdown>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button
                                class="
                                    inline-flex
                                    items-center
                                    justify-center
                                    p-2
                                    rounded-md
                                    text-gray-400
                                    hover:text-gray-500 hover:bg-gray-100
                                    focus:outline-none
                                    focus:bg-gray-100
                                    focus:text-gray-500
                                    transition
                                "
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <JetResponsiveNavLink
                            :href="route('home')"
                            :active="route().current('home')"
                        >
                            Dictionary
                        </JetResponsiveNavLink>
                        <JetResponsiveNavLink
                            :href="route('word-admin')"
                            :active="route().current('word-admin')"
                        >
                            Word admin
                        </JetResponsiveNavLink>
                        <JetResponsiveNavLink
                            :href="route('approval')"
                            :active="route().current('approval')"
                        >
                            Pending words
                        </JetResponsiveNavLink>
                        <JetResponsiveNavLink
                            :href="route('recordings')"
                            :active="route().current('recordings')"
                        >
                            Pending recordings
                        </JetResponsiveNavLink>

                        <JetResponsiveNavLink
                            :href="route('latest.words')"
                            :active="
                                route().current('latest.words')
                            "
                        >
                            Latest Words
                        </JetResponsiveNavLink>
                        <JetResponsiveNavLink
                            :href="route('latest.recordings')"
                            :active="
                                route().current('latest.recordings')
                            "
                        >
                            Latest Recordings
                        </JetResponsiveNavLink>
                        <JetResponsiveNavLink
                            :href="route('latest.comments')"
                            :active="
                                route().current(
                                    'latest.comments'
                                )
                            "
                        >
                            Latest Comments
                        </JetResponsiveNavLink>

                        <JetResponsiveNavLink
                            :href="route('new-definitions')"
                            :active="route().current('new-definitions')"
                        >
                            Pending definitions (Beta)
                        </JetResponsiveNavLink>
                        <JetResponsiveNavLink
                            :href="route('rejected')"
                            :active="route().current('rejected')"
                        >
                            Rejected (Beta)
                        </JetResponsiveNavLink>
                        <JetResponsiveNavLink
                            :href="route('wotd')"
                            :active="route().current('wotd')"
                        >
                            Word of the Day
                        </JetResponsiveNavLink>
                        <JetResponsiveNavLink
                            :href="route('revisions')"
                            :active="route().current('revisions')"
                        >
                            Revisions
                        </JetResponsiveNavLink>
                        <JetResponsiveNavLink
                            :href="route('users')"
                            :active="route().current('users')"
                        >
                            Users
                        </JetResponsiveNavLink>
                        <JetResponsiveNavLink
                            :href="route('reports')"
                            :active="route().current('reports')"
                        >
                            Reports
                            <span v-if="reports.length > 0">
                                <span class="text-red-500 ml-1">
                                    ({{ reports.length }})
                                </span>
                            </span>
                        </JetResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <!-- <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div
                                v-if="
                                    $page.props.jetstream.managesProfilePhotos
                                "
                                class="shrink-0 mr-3"
                            >
                                <img
                                    class="h-10 w-10 rounded-full object-cover"
                                    :src="$page.props.user.profile_photo_url"
                                    :alt="$page.props.user.name"
                                />
                            </div>

                            <div>
                                <div
                                    class="font-medium text-base text-gray-800"
                                >
                                    {{ $page.props.user.name }}
                                </div>
                                <div class="font-medium text-sm text-gray-500">
                                    {{ $page.props.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <JetResponsiveNavLink
                                :href="route('profile.show')"
                                :active="route().current('profile.show')"
                            >
                                Profile
                            </JetResponsiveNavLink>

                            <JetResponsiveNavLink
                                v-if="$page.props.jetstream.hasApiFeatures"
                                :href="route('api-tokens.index')"
                                :active="route().current('api-tokens.index')"
                            >
                                API Tokens
                            </JetResponsiveNavLink>

                            <form method="POST" @submit.prevent="logout">
                                <JetResponsiveNavLink as="button">
                                    Log Out
                                </JetResponsiveNavLink>
                            </form>

                            <template
                                v-if="$page.props.jetstream.hasTeamFeatures"
                            >
                                <div class="border-t border-gray-200" />

                                <div
                                    class="
                                        block
                                        px-4
                                        py-2
                                        text-xs text-gray-400
                                    "
                                >
                                    Manage Team
                                </div>

                                <JetResponsiveNavLink
                                    :href="
                                        route(
                                            'teams.show',
                                            $page.props.user.current_team
                                        )
                                    "
                                    :active="route().current('teams.show')"
                                >
                                    Team Settings
                                </JetResponsiveNavLink>

                                <JetResponsiveNavLink
                                    v-if="$page.props.jetstream.canCreateTeams"
                                    :href="route('teams.create')"
                                    :active="route().current('teams.create')"
                                >
                                    Create New Team
                                </JetResponsiveNavLink>

                                <div class="border-t border-gray-200" />

                                <div
                                    class="
                                        block
                                        px-4
                                        py-2
                                        text-xs text-gray-400
                                    "
                                >
                                    Switch Teams
                                </div>

                                <template
                                    v-for="team in $page.props.user.all_teams"
                                    :key="team.id"
                                >
                                    <form @submit.prevent="switchToTeam(team)">
                                        <JetResponsiveNavLink as="button">
                                            <div class="flex items-center">
                                                <svg
                                                    v-if="
                                                        team.id ==
                                                        $page.props.user
                                                            .current_team_id
                                                    "
                                                    class="
                                                        mr-2
                                                        h-5
                                                        w-5
                                                        text-green-400
                                                    "
                                                    fill="none"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                    />
                                                </svg>
                                                <div>{{ team.name }}</div>
                                            </div>
                                        </JetResponsiveNavLink>
                                    </form>
                                </template>
                            </template>
                        </div>
                    </div> -->
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow">
                <div class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
