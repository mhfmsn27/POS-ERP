import { TokenService } from "@/services";

import { defineRule, configure } from "vee-validate";
import * as rules from "@vee-validate/rules";
import { localize } from "@vee-validate/i18n";
import id from "@vee-validate/i18n/dist/locale/id.json";
import ar from "@vee-validate/i18n/dist/locale/ar.json";
import en from "@vee-validate/i18n/dist/locale/en.json";
import es from "@vee-validate/i18n/dist/locale/es.json";
import fr from "@vee-validate/i18n/dist/locale/fr.json";
import hi from "@vee-validate/i18n/dist/locale/bn.json";
import ja from "@vee-validate/i18n/dist/locale/ja.json";
import ms from "@vee-validate/i18n/dist/locale/ms_MY.json";
import nl from "@vee-validate/i18n/dist/locale/nl.json";
import pt from "@vee-validate/i18n/dist/locale/pt_PT.json";
import zh from "@vee-validate/i18n/dist/locale/zh_CN.json";

var langCode = id;

if (TokenService.getLang() == "ar") {
    langCode = ar;
}

if (TokenService.getLang() == "en") {
    langCode = en;
}

if (TokenService.getLang() == "es") {
    langCode = es;
}

if (TokenService.getLang() == "fr") {
    langCode = fr;
}

if (TokenService.getLang() == "hi") {
    langCode = hi;
}

if (TokenService.getLang() == "ja") {
    langCode = ja;
}

if (TokenService.getLang() == "ms") {
    langCode = ms;
}

if (TokenService.getLang() == "nl") {
    langCode = nl;
}

if (TokenService.getLang() == "pt") {
    langCode = pt;
}

if (TokenService.getLang() == "zh") {
    langCode = zh;
}

configure({
    generateMessage: localize("id", langCode),
});

Object.keys(rules).forEach((rule) => {
    if (typeof rules[rule] === "function") {
        defineRule(rule, rules[rule]);
    }
});
