import de from './langs/de.json'
import en from './langs/en.json';
import es from './langs/es.json';
import fr from './langs/fr.json';
import no from './langs/no.json';
import shet from './langs/shet.json'

import { createI18n } from "vue-i18n";

const i18n = createI18n({
    locale: localStorage.getItem("spaekationary-locale") || "shet",
    fallbackLocale: "en",
    messages: {
        de,
        en,
        es,
        fr,
        no,
        shet
    },
});

export default i18n;
