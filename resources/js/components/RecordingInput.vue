<script setup>
import { ref } from "vue";
import { convertToBlogUri } from "@/utils/helpers";
import MicIcon from "@/components/icons/MicIcon.vue";

let device = null;
let recorder = null;
let chunks = [];
let recording = ref(false);
let blob = null;
let justRecorded = ref(null);

const recordAudio = () => {
    recording.value = true;
    justRecorded.value = null;
    device = navigator.mediaDevices.getUserMedia({ audio: true });

    device.then((stream) => {
        recorder = new MediaRecorder(stream);
        recorder.ondataavailable = async (e) => {
            chunks.push(e.data);
            if (recorder.state === "inactive") {
                blob = new Blob(chunks, { type: "audio/wav" });
                const file = await convertToBlogUri(blob);
                justRecorded.value = file;
                chunks.value = [];
            }
        };

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
            <button
                :disabled="recording"
                @click="recordAudio"
                class="
                    flex
                    border border-teal-500
                    text-teal-500
                    block
                    rounded-sm
                    font-bold
                    px-6
                    mr-2
                    flex
                    items-center
                    hover:bg-teal-500 hover:text-white
                    disabled:opacity-50
                    cursor-pointer
                "
            >
                <div class="recording" :id="[recording ? 'rec' : 'not-rec']">
                    <div class="icon flex justify-center mt-1">
                        <MicIcon style="width: 25px; height: 25px" />
                    </div>
                </div>
                <span>Record{{ recording ? "ing" : "" }}</span>
            </button>
            <button
            :disabled="!recording"
                class="
                    flex
                    border border-teal-500
                    text-teal-500
                    block
                    rounded-sm
                    font-bold
                    px-6
                    mr-2
                    flex
                    items-center
                    hover:bg-teal-500 hover:text-white
                    disabled:opacity-50
                    cursor-pointer
                "
                id="stop"
                @click="stop"
            >
                Stop
            </button>
        </div>

        <div v-if="justRecorded">
            Your recording:
            <audio controls>
                <source :src="justRecorded" type="audio/wav" />
            </audio>
        </div>
    </div>
</template>

<style scoped>
.recording {
    width: 35px;
    height: 35px;
    font-size: 0;
    /* background-color: red; */
    border: 0;
    border-radius: 35px;
    margin: 18px;
    outline: none;
}

#not-rec {
    /* background-color: darkred; */
}

#rec {
    animation-name: pulse;
    animation-duration: 1.5s;
    animation-iteration-count: infinite;
    animation-timing-function: linear;
}

@keyframes pulse {
    0% {
        box-shadow: 0px 0px 5px 0px rgba(173, 0, 0, 0.3);
    }
    65% {
        box-shadow: 0px 0px 5px 13px rgba(173, 0, 0, 0.3);
    }
    90% {
        box-shadow: 0px 0px 5px 13px rgba(173, 0, 0, 0);
    }
}
</style>