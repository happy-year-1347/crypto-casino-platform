<template>
    <BaseLayout>
        <div class="md:w-4/6 2xl:w-4/6 mx-auto my-10 p-4">
            <h3 class="text-3xl mb-2">{{ $t('Support') }}</h3>
            <p class="text-gray-500 mb-8">{{ $t('Answers to the questions we get most often. If yours is not here, write to us.') }}</p>

            <!-- contact details, shown only for the channels the owner filled in -->
            <div v-if="email || telegram" class="mb-10 p-4 rounded bg-gray-100 dark:bg-gray-800">
                <h4 class="text-xl mb-3">{{ $t('Contact us') }}</h4>
                <p v-if="email" class="mb-1">
                    <i class="fa-solid fa-envelope mr-2 text-gray-500"></i>
                    <a :href="`mailto:${email}`" class="underline">{{ email }}</a>
                </p>
                <p v-if="telegram">
                    <i class="fa-brands fa-telegram mr-2 text-gray-500"></i>
                    <a :href="telegram" target="_blank" rel="noopener" class="underline">{{ telegram }}</a>
                </p>
            </div>

            <div v-for="(section, index) in sections" :key="index" class="mb-8">
                <h4 class="text-2xl mb-3">{{ fill(section[0]) }}</h4>
                <p v-for="(paragraph, p) in section[1]" :key="p" class="mb-2 text-gray-600 dark:text-gray-300">{{ fill(paragraph) }}</p>
            </div>
        </div>
    </BaseLayout>
</template>

<script>

import BaseLayout from "@/Layouts/BaseLayout.vue";
import {useSettingStore} from "@/Stores/SettingStore.js";
import {getActiveLanguage} from "laravel-vue-i18n";
import {normalize, FALLBACK} from "@/Services/Locale.js";
import faq from "./support-faq.js";

export default {
    props: [],
    components: {BaseLayout},
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
            return faq[this.language] || faq[FALLBACK];
        },
        email() {
            return (this.setting && this.setting.support_email) || '';
        },
        telegram() {
            return (this.setting && this.setting.support_telegram) || '';
        },
        replacements() {
            const s = this.setting || {};
            const symbol = s.prefix || '';
            const min = s.min_deposit !== undefined && s.min_deposit !== null
                ? `${symbol}${Number(s.min_deposit).toFixed(2)}`
                : '';
            const qualifying = Number(s.bonus_min_deposit) > 0 ? s.bonus_min_deposit : s.min_deposit;
            const qualifyingText = (qualifying === undefined || qualifying === null)
                ? min
                : `${symbol}${Number(qualifying).toFixed(2)}`;

            return {
                ':site': s.software_name || '',
                ':bonus': s.initial_bonus !== undefined && s.initial_bonus !== null ? String(s.initial_bonus) : '',
                ':rollover': s.rollover !== undefined && s.rollover !== null ? String(s.rollover) : '',
                ':min': qualifyingText,
                ':bonusmax': (s.bonus_max === undefined || s.bonus_max === null) ? '' : `${symbol}${Number(s.bonus_max).toFixed(2)}`,
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
