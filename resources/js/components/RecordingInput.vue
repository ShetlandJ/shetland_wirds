<script setup>
import { onMounted, ref } from "vue";
import { convertToBlogUri } from "@/utils/helpers";

const callback = () => null;
let device = null;
let recorder = null;
let chunks = [];
let recording = ref(false);
let blobObj = null;
let blob = null;
let justRecorded = ref(null);

const recordAudio = () => {
    recording.value = true;
    justRecorded.value = null;
    device = navigator.mediaDevices.getUserMedia({ audio: true })
    device.then((stream) => {
        recorder = new MediaRecorder(stream);
        recorder.ondataavailable = async (e) => {
            console.log(chunks, "chunks", e.data);
            chunks.push(e.data);
            if (recorder.state === "inactive") {
                blob = new Blob(chunks, { type: "audio/wav" });
                const file = await convertToBlogUri(blob);
                justRecorded.value = file;
                blobObj = blob;
                chunks = [];
                // blobObj = null;
            } else {
                // console.log(chunks, "chunks");
                // console.log(blob, "blob");
            }
        };
        // start
        recorder.start();
    });
};

const stop = () => {
    recording.value = false;
    recorder.stop();
    device = null;
};
</script>

<template>
<div>
    <div class="flex justify-between" style="width: 50%">
        <div>
            {{recording ? "Recording" : "Not Recording"}}
        </div>
        <button @click="recordAudio" id="record">Record</button>
        <button id="stop" @click="stop">Stop</button>
    </div>

    <div v-if="justRecorded">
        Your recording:
        <audio controls>
            <source :src="justRecorded" type="audio/wav">
        </audio>
    </div>
</div>
</template>;
