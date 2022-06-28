<script setup>
import { usePage } from "@inertiajs/inertia-vue3";
import { ref, reactive, onMounted, computed, onBeforeUnmount } from "vue";
import LocationInput from "./LocationInput.vue";
const { isLoggedIn, userSelectedLocations, locations, word } =
    usePage().props.value;

let showAddForm = ref(false);

const toggleShowAddForm = () => (showAddForm.value = !showAddForm.value);

let elementHeight = reactive({ height: 0 });
let elementWidth = reactive({ width: 0 });

import h337 from "heatmap.js";

onMounted(() => {
    var heatmapInstance = h337.create({
        // required container
        container: document.querySelector(".heatmap"),
        // backgroundColor to cover transparent areas
        backgroundColor: "rgba(0,0,0,0)",
        // custom gradient colors
        gradient: {
            // enter n keys between 0 and 1 here
            // for gradient color customization
            ".5": "red",
            ".8": "orange",
            ".95": "green",
        },
        // the maximum opacity (the value with the highest intensity will have it)
        maxOpacity: 0.5,
        // minimum opacity. any value > 0 will produce
        // no transparent gradient transition
        minOpacity: 0.2,
    });

    // now generate some random data
    var points = [];
    var max = 0;
    console.log(document.querySelector('img').offsetHeight);
    var width = 840;
    var height = 400;
    var len = 300;

    while (len--) {
        var val = Math.floor(Math.random() * 100);
        var radius = Math.floor(Math.random() * 70);

        max = Math.max(max, val);
        var point = {
            x: Math.floor(Math.random() * width),
            y: Math.floor(Math.random() * height),
            value: val,
            radius: radius,
        };
        points.push(point);
    }
    // heatmap data format
    var data = {
        max: 99,
        data: points,
    };
    // if you have a set of datapoints always use setData instead of addData
    // for data initialization
    heatmapInstance.setData(data);
});
</script>

<template>
    <div v-if="word">
        <p class="mb-2" v-if="isLoggedIn">
            Where in Shetland is this word spoken?
            <span v-if="showAddForm">Add your own below or&nbsp;</span>
            <button
                v-if="showAddForm"
                @click="toggleShowAddForm"
                class="underline"
            >
                see chart
            </button>
        </p>

        <div v-if="!showAddForm">
            <div class="heatmap" style="height: 500px">
                <img src="/shetland.gif" style="width: 100%" />
            </div>
            <p>
                You have previously selected locations for <b>{{ word.word }}</b
                >. If you would like to amend your selection, please click
                <button class="underline" @click="toggleShowAddForm">
                    here</button
                >.
            </p>
        </div>

        <LocationInput
            v-else
            :user-selected-locations="userSelectedLocations"
            :locations="locations"
            :word="word"
        />
    </div>
</template>shetland.gif