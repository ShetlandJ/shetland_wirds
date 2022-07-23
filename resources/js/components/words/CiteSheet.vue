<script setup>
import format from "date-fns/format";
const APA_DATE_FORMAT = "yyyy MMMM d";
const CHICAGO_DATE_FORMAT = "MMMM d, yyyy";
const BIBTEX_YEAR = "yyyy";
const BIBTEX_DATE = "d-MMMM-yyyy";
const HOUR_MINUTE_FORMAT = "h:mm a";

defineProps({
    word: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="dark:text-white">
        <div class="flex mb-4">
            <a
                class="underline w-40"
                href="https://en.wikipedia.org/wiki/APA_style"
                target="_blank"
                >APA</a
            >
            <p class="text-sm w-full">
                Spaektionary. (2022). {{ word.word }}. Retrieved
                {{ format(new Date(word.updated_at), HOUR_MINUTE_FORMAT) }},
                {{ format(new Date(word.updated_at), CHICAGO_DATE_FORMAT) }},
                from
                {{ citeURL }}
            </p>
        </div>
        <!-- <div class="flex mb-4">
            <a
                class="underline w-40"
                href="https://en.wikipedia.org/wiki/The_Chicago_Manual_of_Style"
                target="_blank"
                >Chicago</a
            >
            <p class="text-sm w-full">
                Spaektionary, "<i>{{ word.word }}</i
                >",
                {{ citeURL }}
                (accessed
                {{ format(new Date(), CHICAGO_DATE_FORMAT) }}).
            </p>
        </div> -->
        <div class="flex mb-4">
            <a
                class="underline w-40"
                href="https://en.wikipedia.org/wiki/BibTeX"
                target="_blank"
                >BibTeX</a
            >
            <code class="text-sm w-full">
                @misc{<br />
                author = "{Spaektionary}",<br />
                title = "{{ word.word }} --- {Spaektionary},<br />
                year = "{{ format(new Date(), BIBTEX_YEAR) }}",<br />
                url = "{{ citeURL }}",<br />
                note = "[Online; accessed
                {{ format(new Date(), BIBTEX_DATE) }}]<br />
                }
            </code>
        </div>
    </div>
</template>