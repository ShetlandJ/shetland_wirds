<script setup>
defineProps({
    word: Object,
});
</script>

<template>
    <div className="bg-white shadow-lg rounded-lg mx-4 md:mx-auto my-8 max-w-md md:max-w-2xl">
            <div className="flex items-start px-4 py-6">
                <div>
                    <div className="flex items-center justify-between mb-2">
                        <h2 className="text-lg font-semibold text-gray-900 -mt-1">{{word.word}}</h2>
                    </div>
                    <p className="text-gray-700 mb-2">{{word.translation}} </p>
                    <div v-if="word.example_sentence">
                        <p className="mt-3 text-gray-700 mb-2">
                            {{
                                word.example_sentence ?
                                    `Usage: ${word.example_sentence}` :
                                    'No example sentence exists'
                            }}
                        </p>
                    </div>
                    <!-- {showSeeAlso && (
                        <div className="flex align-items-end">
                            <span className="text-gray-900 mr-2">
                                See also:
                            </span>
                            {word.see_also.map((seeAlso: string) => (
                                <span
                                    className="cursor-pointer underline hover:text-blue-800 visited:text-purple-600 mr-2"
                                    key={seeAlso}
                                    onClick={() => goToSeeAlso(seeAlso)}
                                >
                                    {seeAlso}
                                </span>
                            ))}
                        </div>
                    )} -->

                    <!-- <div className="mt-4 flex items-center">
                        <div className="flex mr-2 text-gray-700 text-sm mr-3 cursor-pointer">
                            <svg
                                fill="none"
                                viewBox="0 0 24 24"
                                className="w-4 h-4 mr-1"
                                stroke="red"
                                onClick={() => handleWordLike(word.uuid)}
                            >
                                {isLiked && (
                                    <path fill="red" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                )}
                                {!isLiked && (
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                )}
                            </svg>
                            <span>{word.likes ? word.likes : 0}</span>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
</template>