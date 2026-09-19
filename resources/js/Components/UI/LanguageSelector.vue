<template>
    <button @click.prevent="toggleModal" class="text-[28px] text-gray-500 dark:text-gray-500 mr-3 mt-1" :title="$t('Select language')">
        <i class="fa-light fa-earth-americas"></i>
    </button>

    <div :id="drawerId" class="fixed top-0 right-0 z-40 h-screen p-4 overflow-y-auto transition-transform translate-x-full bg-white w-80 dark:bg-gray-800" tabindex="-1" >
        <h5 class="inline-flex items-center mb-4 text-base font-semibold text-gray-500 dark:text-gray-400">
            <svg class="w-4 h-4 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            {{ $t('Select language') }}
        </h5>
        <button @click.prevent="toggleModal" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 absolute top-2.5 right-2.5 inline-flex items-center justify-center dark:hover:bg-gray-600 dark:hover:text-white" >
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
            <span class="sr-only">{{ $t('Close') }}</span>
        </button>

        <div class="relative h-[calc(100%_-_64px)] w-full px-4">
            <div class="grid grid-cols-3 gap-y-2 py-6">
                <div v-for="lang in languages" :key="lang.code" class="relative my-2 flex flex-col items-center justify-center">
                    <div class="relative">
                        <input type="radio" :value="lang.code" v-model="language" :name="drawerId + '_selection'" :aria-label="lang.label" class="peer absolute start-0 top-0 z-20 h-full w-full cursor-pointer opacity-0" />
                        <div class="border-gray-200 peer-checked:border-primary-500 dark:border-gray-600 flex h-14 w-14 items-center justify-center rounded-full border-2 shadow-lg transition-all duration-300">
                            <img class="h-10 w-10 rounded-full" :src="lang.flag" :alt="lang.label" />
                        </div>
                        <div class="bg-primary-500 dark:border-gray-800 absolute -end-1 -top-1 hidden h-7 w-7 items-center justify-center rounded-full border-4 border-white text-white peer-checked:flex">
                            <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" class="icon h-3 w-3" width="1em" height="1em" viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 6L9 17l-5-5"></path>
                            </svg>
                        </div>
                    </div>
                    <span class="mt-2 text-xs text-gray-600 dark:text-gray-300">{{ lang.label }}</span>
                </div>
            </div>
            <div>
                <img :src="`/assets/images/lang/translation.svg`" class="mx-auto w-full max-w-[280px] dark:hidden" alt="" />
                <img :src="`/assets/images/lang/translation-dark.svg`" class="mx-auto hidden w-full max-w-[280px] dark:block" alt="" />
            </div>
        </div>

    </div>
</template>

<script>
    import { Drawer } from 'flowbite';
    import HttpApi from "@/Services/HttpApi.js";
    import { loadLanguageAsync, getActiveLanguage } from 'laravel-vue-i18n';
    import { LANGUAGES, normalize, storedLanguage, storeLanguage, setCurrentLanguage } from "@/Services/Locale.js";
    import { useAuthStore } from "@/Stores/Auth.js";

    let instances = 0;

    export default {
        props: [],
        data() {
            return {
                drawer: null,
                // the component is mounted in more than one place; ids must stay unique
                drawerId: 'drawer-language-' + (++instances),
                languages: LANGUAGES,
                language: normalize(getActiveLanguage()) || 'en',
                applying: false,
            }
        },
        mounted() {
            const el = document.getElementById(this.drawerId);
            if(el) {
                this.drawer = new Drawer(el, {
                    placement: 'right',
                    backdrop: true,
                    bodyScrolling: false,
                    edge: false,
                    edgeOffset: '',
                    backdropClasses: 'bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-30',
                });
            }

            window.addEventListener('site-language', this.onLanguageEvent);

            // ?lang=es links (landing pages, ads) win over everything
            const param = normalize(new URLSearchParams(window.location.search).get('lang'));
            if(param) {
                this.select(param, true);
                return;
            }

            this.syncWithAccount();
        },
        beforeUnmount() {
            window.removeEventListener('site-language', this.onLanguageEvent);
        },
        watch: {
            language(newValue, oldValue) {
                if(this.applying || newValue === oldValue) return;
                this.select(newValue, true);
            },
        },
        methods: {
            onLanguageEvent(event) {
                if(event.detail === this.language) return;
                this.applying = true;
                this.language = event.detail;
                this.$nextTick(() => { this.applying = false; });
            },
            toggleModal() {
                if(this.drawer) this.drawer.toggle();
            },
            // show a language; `save` = the player chose it
            async select(code, save) {
                code = normalize(code) || 'en';

                this.applying = true;
                this.language = code;
                this.$nextTick(() => { this.applying = false; });

                setCurrentLanguage(code);
                window.dispatchEvent(new CustomEvent('site-language', { detail: code }));
                await loadLanguageAsync(code);

                if(save) {
                    storeLanguage(code);
                    HttpApi.put('/profile/updateLanguage', { language: code }).catch(() => {});
                    try { if(this.drawer && this.drawer.isVisible()) this.drawer.hide(); } catch (e) {}
                }
            },
            // a logged-in player gets the language saved on the account (other devices)
            syncWithAccount() {
                const auth = useAuthStore();
                if(!auth.getToken()) return;

                HttpApi.post('/profile/getLanguage', {})
                    .then(response => {
                        const data = response.data || {};
                        const local = storedLanguage();

                        if(local) {
                            // a language picked on this device wins; keep the account in step
                            if(!data.saved || normalize(data.language) !== local) {
                                HttpApi.put('/profile/updateLanguage', { language: local }).catch(() => {});
                            }
                        } else if(data.saved && normalize(data.language) !== normalize(getActiveLanguage())) {
                            // new device: use the language saved on the account
                            this.select(data.language, false);
                            storeLanguage(normalize(data.language));
                        }
                    })
                    .catch(() => {});
            },
        },
    };
</script>
