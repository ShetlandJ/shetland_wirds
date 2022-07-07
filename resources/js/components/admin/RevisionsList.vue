<script setup>
import { reactive, ref } from "@vue/runtime-core";
import format from "date-fns/format";

const DATE_FORMAT = "d MMM yy";

defineProps({
    revisions: {
        type: Array,
        default: () => [],
    },
});

const getDiff = (text1, text2) => {
    //Takes in two strings
    //Returns an array of the span of the differences
    //So if given:
    // text1: "that is number 124"
    // text2: "this is number 123"
    //It will return:
    // [[2,4],[17,18]]
    //If the strings are of different lengths, it will check up to the end of text1
    //If you want it to do case-insensitive difference, just convert the texts to lowercase before passing them in
    var diffRange = [];
    var currentRange = undefined;
    for (var i = 0; i < text1.length; i++) {
        if (text1[i] != text2[i]) {
            //Found a diff!
            if (currentRange == undefined) {
                //Start a new range
                currentRange = [i];
            }
        }
        if (currentRange != undefined && text1[i] == text2[i]) {
            //End of range!
            currentRange.push(i);
            diffRange.push(currentRange);
            currentRange = undefined;
        }
    }
    //Push any last range if there's still one at the end
    if (currentRange != undefined) {
        currentRange.push(i);
        diffRange.push(currentRange);
    }
    return diffRange;
};

const strCompare = (text1, text2, added = false) => {
    var diffRange = getDiff(text1, text2);
    //Now we just need to generate the html according to the diff ranges we have
    var element = "";
    //We have to loop backwards, because
    for (var i = diffRange.length - 1; i >= 0; i--) {
        var range = diffRange[i];
        //Inject spans around the range
        var s = range[0]; //start
        var e = range[1]; //end
        text1 =
            text1.slice(0, s) +
            `<span class="${added ? "highlight" : "highlight-removed"}">` +
            text1.slice(s, e) +
            "</span>" +
            text1.slice(e);
    }

    return text1;
};

const comparisonExists = (text1, text2) => {
    if (strCompare(text1, text2) === text1) {
        return strCompare(text2, text1, true);
    }

    return strCompare(text1, text2);
};
</script>

<template>
    <div class="bg-white p-8 rounded-md w-full" v-if="revisions">
        <div class="flex items-center justify-between pb-6">
            <div>
                <h2 class="text-gray-600 font-semibold">Revisions</h2>
            </div>
        </div>
        <div>
            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                <div
                    class="
                        inline-block
                        min-w-full
                        shadow
                        rounded-lg
                        overflow-hidden
                    "
                >
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th
                                    v-for="type in [
                                        'Word',
                                        'Definitions',
                                        'User',
                                        'Date'
                                    ]"
                                    :key="type"
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
                                    {{ type }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="revision in revisions"
                                :key="revision.id"
                            >
                                <td
                                    class="
                                        px-5
                                        py-5
                                        border-b border-gray-200
                                        bg-white
                                        text-sm
                                    "
                                >
                                    <span
                                        v-if="revision.revisions?.word.changed"
                                    >
                                        <SanitisedHtml
                                            :htmlString="
                                                comparisonExists(
                                                    revision.revisions.word
                                                        .original,
                                                    revision.revisions.word
                                                        .updated
                                                )
                                            "
                                        />
                                    </span>
                                    <span v-else>{{ revision.word }}</span>
                                </td>
                                <td
                                    class="
                                        px-5
                                        py-5
                                        border-b border-gray-200
                                        bg-white
                                        text-sm
                                    "
                                >
                                    <div
                                        v-for="definition in revision.revisions
                                            .definitions"
                                        :key="definition.id"
                                    >
                                        <SanitisedHtml
                                            :htmlString="
                                                comparisonExists(
                                                    definition['original'],
                                                    definition['updated']
                                                )
                                            "
                                        />
                                    </div>
                                </td>

                                <td
                                    class="
                                        px-5
                                        py-5
                                        border-b border-gray-200
                                        bg-white
                                        text-sm
                                    "
                                >
                                    <span v-if="revision.user">{{
                                        revision.user
                                    }}</span>
                                    <span v-else>-</span>
                                </td>
                                <td
                                    class="
                                        px-5
                                        py-5
                                        border-b border-gray-200
                                        bg-white
                                        text-sm
                                    "
                                >
                                    {{format(new Date(revision.created_at), DATE_FORMAT)}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.highlight {
    background-color: rgb(171, 242, 188);
}

.highlight-removed {
    background-color: rgba(255, 129, 130, 0.4);
}
</style>