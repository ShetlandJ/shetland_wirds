<script setup>
import { useForm } from "@inertiajs/inertia-vue3";

const form = useForm({
    newWord: "",
    translation: "",
    example_sentence: "",
    word_type: null,
});
</script>

<template>
    <div
        class="
            block
            p-6
            md:mx-auto
            my-8
            max-w-md
            md:max-w-2xl
            rounded-lg
            shadow-lg
            bg-white
            max-w-sm
        "
    >
        <form>
            <div class="form-group mb-6">
                <label
                    for="wordInput"
                    class="form-label inline-block mb-2 text-gray-700"
                    >Suggested word</label
                >
                <input
                    type="text"
                    class="
                        form-control
                        block
                        w-full
                        px-3
                        py-1.5
                        text-base
                        font-normal
                        text-gray-700
                        bg-white bg-clip-padding
                        border border-solid border-gray-300
                        rounded
                        transition
                        ease-in-out
                        m-0
                        focus:text-gray-700
                        focus:bg-white
                        focus:border-blue-600
                        focus:outline-none
                    "
                    id="wordInput"
                    aria-describedby="wordHelp"
                    placeholder="New word here"
                />
                <small id="wordHelp" class="block mt-1 text-xs text-gray-600">
                    Enter the new word you wish to add to the dictionary
                </small>
            </div>
            <div class="form-group mb-6">
                <label
                    for="translationInput"
                    class="form-label inline-block mb-2 text-gray-700"
                >
                    What does it mean in English?
                </label>
                <input
                    type="text"
                    class="
                        form-control
                        block
                        w-full
                        px-3
                        py-1.5
                        text-base
                        font-normal
                        text-gray-700
                        bg-white bg-clip-padding
                        border border-solid border-gray-300
                        rounded
                        transition
                        ease-in-out
                        m-0
                        focus:text-gray-700
                        focus:bg-white
                        focus:border-blue-600
                        focus:outline-none
                    "
                    id="translationInput"
                    placeholder="English language translation"
                />
            </div>
            <div class="form-group mb-6">
                <label
                    for="exampleSentenceInput"
                    class="form-label inline-block mb-2 text-gray-700"
                >
                    Can you provide an example Shetland language sentence?
                    (Optional)
                </label>
                <input
                    type="text"
                    class="
                        form-control
                        block
                        w-full
                        px-3
                        py-1.5
                        text-base
                        font-normal
                        text-gray-700
                        bg-white bg-clip-padding
                        border border-solid border-gray-300
                        rounded
                        transition
                        ease-in-out
                        m-0
                        focus:text-gray-700
                        focus:bg-white
                        focus:border-blue-600
                        focus:outline-none
                    "
                    id="exampleSentenceInput"
                    placeholder="e.g. 'He's a lok bigger as his faider.'"
                />
            </div>
            <div class="form-group form-check mb-6">
                <select
                    v-model="form.word_type"
                    placeholder="What type of word is this?"
                    class="
                        form-check-input
                        appearance-none
                        h-4
                        w-4
                        border border-gray-300
                        rounded-sm
                        bg-white
                        checked:bg-blue-600 checked:border-blue-600
                        focus:outline-none
                        transition
                        duration-200
                        mt-1
                        align-top
                        bg-no-repeat bg-center bg-contain
                        float-left
                        mr-2
                        cursor-pointer
                    "
                    id="exampleCheck1"
                >
                    <option :value="null">
                        -- What type of word is this? (optional) --
                    </option>
                    <option value="v">Verb</option>
                </select>
                <!-- <label
                    class="form-check-label inline-block text-gray-800"
                    for="exampleCheck1"
                    >Check me out</label
                > -->
            </div>
            <button
                type="submit"
                class="
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
                "
            >
                Submit
            </button>
        </form>
    </div>
</template>