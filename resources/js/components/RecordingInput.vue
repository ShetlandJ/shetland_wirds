<script setup>
import { ref } from "vue";
import { useForm, usePage } from "@inertiajs/inertia-vue3";

import { convertToBlogUri } from "@/utils/helpers";
import MicIcon from "@/components/icons/MicIcon.vue";

const word = usePage().props.value.word;

let device = null;
let recorder = null;
let chunks = [];
let recordingAudio = ref(false);
let blob = null;
let userRecording = ref(null);
let blobToUpload = ref(null);

const recordAudio = async () => {
    recordingAudio.value = true;
    userRecording.value = null;
    device = navigator.mediaDevices.getUserMedia({ audio: true });

    device.then((stream) => {
        recorder = new MediaRecorder(stream);
        recorder.ondataavailable = async (e) => {
            chunks.push(e.data);
            if (recorder.state === "inactive") {
                blob = new Blob(chunks, { type: "audio/wav" });
                blobToUpload.value = blob;
                const file = await convertToBlogUri(blob);
                userRecording.value = file;
                chunks.value = [];
            }
        };

        recorder.start();
    });
};

const stop = () => {
    recordingAudio.value = false;
    recorder.stop();
    device = null;
};

const form = useForm({
    userRecording: "",
});

const submitRecording = () => {
    form.userRecording = blobToUpload;
    form.post(route("uploadFile", { word: word.word }), {
        userRecording: form.blobToUpload,
        onFinish: () => {
            form.reset();
            chunks = null;
            form.blobToUpload = null;
            form.userRecording = null;
            userRecording = null;
            recordingAudio.value = false;
            device = null;
        },
    });
};
</script>

<template>
    <div>
        <div class="flex">
            <button
                @click="recordingAudio ? stop() : recordAudio()"
                class="
                    btn
                    inline-block
                    px-6
                    py-2.5
                    bg-blue-600
                    text-white
                    font-medium
                    text-xs
                    leading-tight
                    uppercase
                    rounded
                    shadow-md
                    hover:bg-blue-700 hover:shadow-lg
                    focus:bg-blue-700
                    focus:shadow-lg
                    focus:outline-none
                    focus:ring-0
                    active:bg-blue-800 active:shadow-lg
                    transition
                    duration-150
                    ease-in-out
                    flex
                    items-center
                    disabled:opacity-50
                "
            >
                <div
                    class="recording"
                    :id="[recordingAudio ? 'rec' : 'not-rec']"
                ></div>
                <span v-if="!userRecording">{{
                    recordingAudio ? "Stop recording" : "Record"
                }}</span>
                <span v-else>{{
                    recordingAudio ? "Stop recording" : "Try again?"
                }}</span>
            </button>
        </div>
        <hr class="my-4" v-if="userRecording" />
        <div class="flex items-center justify-between" v-if="userRecording">
            <p>Your recording:</p>
            <audio controls>
                <source :src="userRecording" type="audio/wav" />
            </audio>

            <div v-if="userRecording" style="height: fit-content">
                <button
                    @click="submitRecording"
                    class="
                        btn
                        inline-block
                        px-6
                        py-2.5
                        bg-blue-600
                        text-white
                        font-medium
                        text-xs
                        leading-tight
                        uppercase
                        rounded
                        shadow-md
                        hover:bg-blue-700 hover:shadow-lg
                        focus:bg-blue-700
                        focus:shadow-lg
                        focus:outline-none
                        focus:ring-0
                        active:bg-blue-800 active:shadow-lg
                        transition
                        duration-150
                        ease-in-out
                        flex
                        items-center
                        disabled:opacity-50
                    "
                    type="submit"
                >
                    Save
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.recording {
    width: 10px;
    height: 10px;
    font-size: 0;
    background-color: red;
    border: 0;
    margin-right: 5px;
    margin-bottom: 2px;
    border-radius: 35px;
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