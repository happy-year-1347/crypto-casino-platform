<template>
    <BaseLayout>
        <div class="md:w-4/6 2xl:w-4/6 mx-auto p-4 mt-20">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="col-span-1 hidden md:block">
                    <WalletSideMenu/>
                </div>
                <div class="col-span-2 relative">
                    <div v-if="setting != null && wallet != null && isLoading === false" class="flex flex-col w-full bg-gray-200 shadow-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-300/20 dark:bg-gray-700 p-4 rounded">

                        <div v-if="!cryptoEnabled" class="p-4 text-sm text-gray-500">
                            {{ $t('Withdrawals are not available at the moment. Please contact support.') }}
                        </div>

                        <form v-else action="" @submit.prevent="submitWithdraw">
                            <div class="flex items-center justify-between">
                                <p class="text-gray-500">{{ $t('Withdraw') }}</p>
                                <button @click.prevent="$router.push('/profile/wallet')" type="button" class="flex justify-center items-center mr-3 pt-1">
                                    <div>{{ wallet.currency }}</div>
                                    <div class="mr-2 ml-2">
                                        <img :src="`/assets/images/coin/`+wallet.currency+`.png`" alt="" width="32" onerror="this.style.display='none'">
                                    </div>
                                </button>
                            </div>

                            <div class="mt-5">
                                <p class="mb-2 text-gray-500">{{ $t('Withdraw with') }}</p>
                                <div class="w-full flex items-center justify-between bg-white dark:bg-gray-900 rounded p-2">
                                    <div class="flex w-full items-center">
                                        <i class="fa-brands fa-bitcoin text-2xl text-orange-500"></i>
                                        <span class="ml-3">{{ $t('Cryptocurrency') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5">
                                <div class="dark:text-gray-400 mb-3">
                                    <label class="block mb-2">{{ $t('Select Cryptocurrency') }}</label>
                                    <select v-model="withdraw.crypto_currency" @change="requestEstimate" class="input" required>
                                        <option v-for="coin in currencies" :key="coin.code" :value="coin.code">{{ coin.label }}</option>
                                    </select>
                                </div>

                                <div class="dark:text-gray-400 mb-3">
                                    <label for="">{{ $t('Destination Address') }}</label>
                                    <input v-model="withdraw.crypto_address" type="text" class="input" :placeholder="$t('Enter your crypto address')" required minlength="20" maxlength="191">
                                    <p class="text-xs text-gray-500 mt-1">{{ $t('Make sure the address belongs to the selected coin and network') }}: {{ selectedNetwork }}</p>
                                </div>

                                <div class="dark:text-gray-400 mb-3">
                                    <div class="flex justify-between mb-3 text-sm">
                                        <p>{{ $t('Amount') }} ({{ setting.min_withdrawal }} ~ {{ setting.max_withdrawal }} {{ wallet.currency }})</p>
                                        <p>{{ $t('Balance') }}: {{ state.currencyFormat(parseFloat(wallet.balance_withdrawal), wallet.currency) }}</p>
                                    </div>
                                    <div class="flex bg-white dark:bg-gray-900">
                                        <input type="number"
                                               step="0.01"
                                               class="input"
                                               v-model="withdraw.amount"
                                               @input="requestEstimate"
                                               :min="setting.min_withdrawal"
                                               :max="setting.max_withdrawal"
                                               placeholder=""
                                               required>
                                        <div class="flex items-center pr-1">
                                            <div class="inline-flex shadow-sm" role="group">
                                                <button @click.prevent="setMinAmount" type="button" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200  hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                                                    min
                                                </button>
                                                <button @click.prevent="setPercentAmount(50)" type="button" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                                                    50%
                                                </button>
                                                <button @click.prevent="setPercentAmount(100)" type="button" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200  hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                                                    100%
                                                </button>
                                                <button @click.prevent="setMaxAmount" type="button" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200  hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                                                    max
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-between mt-2 text-sm">
                                        <p>{{ $t('Available') }}: {{ state.currencyFormat(parseFloat(wallet.balance_withdrawal), wallet.currency) }}</p>
                                        <p>{{ $t('Balance Rollback') }}: {{ state.currencyFormat(parseFloat(wallet.balance_bonus), wallet.currency) }}</p>
                                    </div>
                                    <div class="mt-3 p-3 rounded bg-white dark:bg-gray-900 text-sm">
                                        <span v-if="estimateLoading" class="text-gray-500">{{ $t('Getting live rate') }}...</span>
                                        <span v-else-if="estimate && estimate.estimated_amount">{{ $t('You will receive approximately') }} <strong>{{ estimate.estimated_amount }} {{ selectedTicker }}</strong> <span class="text-gray-500">({{ $t('rate is fixed when the payout is sent') }})</span></span>
                                        <span v-else-if="estimateError" class="text-red-500">{{ estimateError }}</span>
                                        <span v-else class="text-gray-500">{{ $t('Enter an amount to see the live rate') }}</span>
                                    </div>
                                </div>

                                <div class="mb-3 mt-5">
                                    <div class="flex items-center mb-4">
                                        <input id="accept_terms_checkbox" v-model="withdraw.accept_terms"
                                               type="checkbox"
                                               value=""
                                               class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="accept_terms_checkbox" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-400">
                                            {{ $t('I accept the transfer terms') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 w-full flex items-center justify-center">
                                <button type="submit" class="ui-button-blue w-full">
                                    <span class="uppercase font-semibold text-sm">{{ $t('Request withdrawal') }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div v-if="isLoading" role="status" class="absolute -translate-x-1/2 -translate-y-1/2 top-2/4 left-1/2">
                        <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-green-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/><path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/></svg>
                        <span class="sr-only">{{ $t('Loading') }}...</span>
                    </div>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>


<script>

import {RouterLink, useRouter} from "vue-router";
import BaseLayout from "@/Layouts/BaseLayout.vue";
import WalletSideMenu from "@/Pages/Profile/Components/WalletSideMenu.vue";
import HttpApi from "@/Services/HttpApi.js";
import {useToast} from "vue-toastification";
import {useSettingStore} from "@/Stores/SettingStore.js";

export default {
    props: [],
    components: {WalletSideMenu, BaseLayout, RouterLink},
    data() {
        return {
            isLoading: false,
            wallet: null,
            cryptoEnabled: false,
            currencies: [],
            estimate: null,
            estimateError: null,
            estimateLoading: false,
            estimateTimer: null,
            withdraw: {
                crypto_currency: 'btc',
                crypto_address: '',
                amount: '',
                type: 'crypto',
                currency: '',
                symbol: '',
                accept_terms: false
            },
        }
    },
    setup(props) {
        const router = useRouter();
        return {
            router
        };
    },
    computed: {
        setting() {
            // reactive: the store fills it asynchronously on first load
            return useSettingStore().setting;
        },
        selectedNetwork() {
            const coin = this.currencies.find(c => c.code === this.withdraw.crypto_currency);
            return coin ? coin.network : '';
        },
        /// the provider's code glues the chain onto the coin ("usdcmatic"), which
        /// is not what anyone has in their wallet, so show the plain ticker
        selectedTicker() {
            const coin = this.currencies.find(c => c.code === this.withdraw.crypto_currency);
            if (coin && coin.ticker) return coin.ticker;
            return String(this.withdraw.crypto_currency || '').toUpperCase();
        }
    },
    beforeUnmount() {
        clearTimeout(this.estimateTimer);
    },
    methods: {
        setMinAmount: function() {
            this.withdraw.amount = this.setting.min_withdrawal;
            this.requestEstimate();
        },
        setMaxAmount: function() {
            this.withdraw.amount = Math.min(parseFloat(this.setting.max_withdrawal), parseFloat(this.wallet.balance_withdrawal)).toFixed(2);
            this.requestEstimate();
        },
        setPercentAmount: function(percent) {
            this.withdraw.amount = (( percent / 100 ) * this.wallet.balance_withdrawal).toFixed(2);
            this.requestEstimate();
        },
        getWallet: function() {
            const _this = this;
            const _toast = useToast();

            HttpApi.get('profile/wallet')
                .then(response => {
                    _this.wallet = response.data.wallet;
                    _this.withdraw.currency = response.data.wallet.currency;
                    _this.withdraw.symbol = response.data.wallet.symbol;
                })
                .catch(error => {
                    try {
                        Object.entries(JSON.parse(error.request.responseText)).forEach(([key, value]) => {
                            _toast.error(`${value}`);
                        });
                    } catch (e) {}
                });
        },
        getSetting: function() {
            if(this.setting) {
                this.withdraw.amount = this.setting.min_withdrawal;
            }

            this.isLoading = false;
        },
        getCurrencies: function() {
            const _this = this;
            HttpApi.get('crypto/currencies').then(response => {
                _this.cryptoEnabled = !!response.data.enabled && response.data.currencies.length > 0;
                _this.currencies = response.data.currencies || [];
                if(_this.currencies.length && !_this.currencies.find(c => c.code === _this.withdraw.crypto_currency)) {
                    _this.withdraw.crypto_currency = _this.currencies[0].code;
                }
                _this.requestEstimate();
            }).catch(() => {
                _this.cryptoEnabled = false;
            });
        },
        requestEstimate: function() {
            const _this = this;
            clearTimeout(this.estimateTimer);
            this.estimate = null;
            this.estimateError = null;

            const amount = parseFloat(this.withdraw.amount);
            if(!amount || amount <= 0 || !this.cryptoEnabled) return;

            this.estimateLoading = true;
            this.estimateTimer = setTimeout(() => {
                HttpApi.get('crypto/estimate', { params: { amount: amount, currency: _this.withdraw.crypto_currency } })
                    .then(response => {
                        if(response.data.success) {
                            _this.estimate = response.data.data;
                        } else {
                            _this.estimateError = response.data.message;
                        }
                    })
                    .catch(error => {
                        try {
                            _this.estimateError = JSON.parse(error.request.responseText).message;
                        } catch (e) {
                            _this.estimateError = _this.$t('Could not get a rate right now');
                        }
                    })
                    .finally(() => { _this.estimateLoading = false; });
            }, 500);
        },
        submitWithdraw: function(event) {
            const _this = this;
            const _toast = useToast();

            if(!_this.withdraw.accept_terms) {
                _toast.error(_this.$t('You need to accept the terms'));
                return;
            }

            _this.isLoading = true;
            _this.withdraw.type = 'crypto';

            HttpApi.post('wallet/withdraw/request', _this.withdraw).then(response => {
                _this.isLoading = false;
                _this.withdraw.crypto_address = '';
                _this.withdraw.amount = '';
                _this.withdraw.accept_terms = false;

                _this.router.push({ name: 'profileTransactions' });
                _toast.success(_this.$t('Withdrawal requested. It will be sent after review.'));
            }).catch(error => {
                try {
                    Object.entries(JSON.parse(error.request.responseText)).forEach(([key, value]) => {
                        _toast.error(`${value}`);
                    });
                } catch (e) {
                    _toast.error(_this.$t('An error occurred'));
                }
                _this.isLoading = false;
            });
        }
    },
    created() {
        this.getWallet();
        this.getSetting();
        this.getCurrencies();
    },
    watch: {},
};
</script>

<style scoped>

</style>
