<template>
    <div class="w-full p-4">
        <div class="grid grid-cols-1 md:grid-cols-2">
            <div class="hidden md:block">
                <img :src="`/assets/images/terms-conditions.png`" alt="" class="w-full">
            </div>
            <div class="">
                <h3 class="text-3xl mb-6">{{ $t(title) }}</h3>
                <template v-for="(section, index) in sections" :key="index">
                    <h4 class="text-2xl">{{ fill(section[0]) }}</h4>
                    <br>
                    <p v-for="(paragraph, p) in section[1]" :key="p" class="mb-2">{{ fill(paragraph) }}</p>
                    <br>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
// One renderer for every legal page: it picks the language, then swaps the
// placeholders for the live values from the admin settings, so the text never
// disagrees with the bonus or the limits that are actually configured.
import {useSettingStore} from "@/Stores/SettingStore.js";
import {getActiveLanguage} from "laravel-vue-i18n";
import {normalize, FALLBACK} from "@/Services/Locale.js";

export default {
    props: {
        title: {type: String, required: true},
        content: {type: Object, required: true},
    },
    data() {
        return {
            language: normalize(getActiveLanguage()) || FALLBACK,
        }
    },
    computed: {
        setting() {
            return useSettingStore().setting;
        },
        sections() {
            return this.content[this.language] || this.content[FALLBACK];
        },
        replacements() {
            const s = this.setting || {};
            const symbol = s.prefix || '';
            const money = value => (value === undefined || value === null || value === '')
                ? ''
                : `${symbol}${Number(value).toFixed(2)}`;
            const plain = value => (value === undefined || value === null || value === '')
                ? ''
                : String(value);

            /// the qualifying deposit is the bonus rule when one is set, otherwise
            /// the cashier minimum
            const qualifying = Number(s.bonus_min_deposit) > 0 ? s.bonus_min_deposit : s.min_deposit;

            return {
                ':site': s.software_name || '',
                ':bonus': plain(s.initial_bonus),
                ':rollover': plain(s.rollover),
                ':min': money(qualifying),
                ':bonusmax': money(s.bonus_max),
                ':maxbet': money(s.bonus_max_bet),
                ':days': plain(s.bonus_days),
            };
        },
    },
    mounted() {
        window.addEventListener('site-language', this.onLanguage);
    },
    beforeUnmount() {
        window.removeEventListener('site-language', this.onLanguage);
    },
    methods: {
        onLanguage(event) {
            this.language = normalize(event.detail) || FALLBACK;
        },
        fill(text) {
            let out = text;
            /// longest key first, or ":bonus" would eat the front of ":bonusmax"
            const keys = Object.keys(this.replacements).sort((a, b) => b.length - a.length);
            for (const key of keys) {
                const value = this.replacements[key];
                if (value !== '') {
                    out = out.split(key).join(value);
                }
            }
            return out;
        },
    },
};
</script>
