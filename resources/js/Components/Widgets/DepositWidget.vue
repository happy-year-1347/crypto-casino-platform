<template>
    <div class="block">
        <!-- method picker -->
        <div v-if="(paymentType == null || paymentType === '') && wallet && setting">
            <ul>
                <li v-if="cryptoEnabled" @click="setPaymentMethod('crypto')" class="bg-white dark:bg-gray-900 cursor-pointer flex justify-between hover:bg-green-700/20 px-4 py-3 mb-2 rounded">
                    <div class="flex items-center gap-3">
                        <i class="fa-brands fa-bitcoin text-4xl text-orange-500"></i>
                        <div>
                            <span class="font-semibold block">{{ $t('Cryptocurrency') }}</span>
                            <span class="text-xs text-gray-500">{{ currencyCodes }}</span>
                        </div>
                    </div>
                    <div class="flex justify-center items-center gap-4 text-gray-500">
                        <i class="fa-solid fa-chevron-right ml-2"></i>
                    </div>
                </li>
                <li v-else class="bg-white dark:bg-gray-900 px-4 py-3 mb-2 rounded text-sm text-gray-500">
                    {{ $t('No deposit method is available at the moment') }}
                </li>
            </ul>
        </div>

        <!-- crypto form -->
        <div v-if="paymentType === 'crypto' && cryptoEnabled" class="p-4">
            <div v-if="!showCryptoPayment">
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $t('Select Cryptocurrency') }}</label>
                    <select v-model="cryptoCurrency" @change="requestEstimate" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option v-for="coin in currencies" :key="coin.code" :value="coin.code">{{ coin.label }}</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $t('Amount') }} ({{ priceCurrency }})</label>
                    <input type="number"
                           v-model="cryptoAmount"
                           @input="amountTouched = true; requestEstimate()"
                           class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                           :min="effectiveMin"
                           :max="setting.max_deposit"
                           step="0.01"
                           :placeholder="$t('Enter amount') + ' (' + priceCurrency + ')'"
                           required
                    >
                    <p class="text-xs text-gray-500 mt-1">{{ $t('Min') }}: {{ effectiveMin.toFixed(2) }} {{ priceCurrency }}<span v-if="parseFloat(setting.max_deposit) > 0"> / {{ $t('Max') }}: {{ setting.max_deposit }} {{ priceCurrency }}</span></p>
                </div>

                <div class="mb-4 p-3 rounded bg-gray-100 dark:bg-gray-800 text-sm min-h-[52px]">
                    <div v-if="estimateLoading" class="text-gray-500">{{ $t('Getting live rate') }}...</div>
                    <div v-else-if="estimate && estimate.estimated_amount">
                        <div>{{ $t('You will send approximately') }} <strong>{{ estimate.estimated_amount }} {{ payTicker }}</strong></div>
                        <div v-if="estimate.min && estimate.min.fiat" class="text-xs mt-1" :class="belowProviderMinimum ? 'text-red-500' : 'text-gray-500'">
                            {{ $t('Provider minimum for this coin') }}: {{ estimate.min.crypto }} {{ payTicker }} (~{{ Number(estimate.min.fiat).toFixed(2) }} {{ priceCurrency }})
                        </div>
                    </div>
                    <div v-else-if="estimateError" class="text-red-500">{{ estimateError }}</div>
                    <div v-else class="text-gray-500">{{ $t('Enter an amount to see the live rate') }}</div>
                </div>

                <button @click.prevent="createCryptoPayment" :disabled="isLoading" class="ui-button-blue rounded w-full">
                    <span v-if="isLoading">{{ $t('Loading') }}...</span>
                    <span v-else>{{ $t('Generate Payment Address') }}</span>
                </button>
                <button @click.prevent="paymentType = null" class="mt-2 text-sm text-gray-500 w-full">{{ $t('Back') }}</button>
            </div>

            <!-- payment details -->
            <div v-if="showCryptoPayment && cryptoPaymentData" class="flex flex-col">
                <div class="w-full p-4 bg-white dark:bg-gray-700 rounded mb-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-semibold">{{ $t('Send') }} {{ cryptoPaymentData.pay_amount }} {{ payTicker }}</h2>
                            <p class="text-xs text-gray-500">= {{ cryptoPaymentData.price_amount }} {{ cryptoPaymentData.price_currency }}</p>
                        </div>
                        <i class="fa-brands fa-bitcoin text-4xl text-orange-500"></i>
                    </div>
                </div>
                <div class="w-full p-4 bg-white dark:bg-gray-900 rounded">
                    <div class="mb-4">
                        <p class="font-bold mb-2">{{ $t('Payment Address') }}:</p>
                        <div class="p-3 flex justify-center items-center bg-white rounded">
                            <QRCodeVue3 :value="cryptoPaymentData.pay_address" :width="180" :height="180"/>
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-gray-500 mb-2">{{ $t('Copy and paste this address in your crypto wallet') }}:</p>
                        <input type="text"
                               :value="cryptoPaymentData.pay_address"
                               readonly
                               class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <div v-if="cryptoPaymentData.payin_extra_id" class="mt-2 text-sm">
                            <strong>{{ $t('Memo / Tag') }}:</strong> {{ cryptoPaymentData.payin_extra_id }}
                        </div>
                    </div>
                    <div class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded">
                        <p class="text-sm"><strong>{{ $t('Amount to send') }}:</strong> {{ cryptoPaymentData.pay_amount }} {{ payTicker }}</p>
                        <p class="text-sm mt-1"><strong>{{ $t('Network') }}:</strong> {{ networkLabel }}</p>
                        <p class="text-sm mt-1 font-medium">
                            {{ $t('Send :coin on the :network network only. Anything else sent to this address is lost.', {coin: payTicker, network: networkLabel}) }}
                        </p>
                        <p class="text-sm mt-1"><strong>{{ $t('Status') }}:</strong> <span class="uppercase">{{ $t(statusLabel) }}</span></p>
                        <p v-if="expiresIn" class="text-sm mt-1"><strong>{{ $t('Address valid for') }}:</strong> {{ expiresIn }}</p>
                        <p class="text-xs mt-2 text-gray-500">{{ $t('Send the exact amount in one transaction. The balance is credited automatically after network confirmation.') }}</p>
                    </div>
                    <div class="flex gap-2">
                        <button @click.prevent="copyCryptoAddress" class="ui-button-blue flex-1">
                            <i class="fa-solid fa-copy mr-2"></i> {{ $t('Copy Address') }}
                        </button>
                        <button @click.prevent="checkCryptoStatus(true)" class="ui-button-green flex-1">
                            <i class="fa-solid fa-refresh mr-2"></i> {{ $t('Check Status') }}
                        </button>
                    </div>
                    <button @click.prevent="resetCrypto" class="mt-3 text-sm text-gray-500 w-full">{{ $t('New deposit') }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {useToast} from "vue-toastification";
    import HttpApi from "@/Services/HttpApi.js";
    import QRCodeVue3 from "qrcode-vue3";
    import {useAuthStore} from "@/Stores/Auth.js";
    import {useSettingStore} from "@/Stores/SettingStore.js";

    export default {
        props: ['showMobile', 'title', 'isFull'],
        components: { QRCodeVue3 },
        data() {
            return {
                isLoading: false,
                wallet: null,
                paymentType: null,

                /// crypto
                cryptoEnabled: false,
                priceCurrency: 'USD',
                currencies: [],
                cryptoCurrency: 'btc',
                cryptoAmount: null,
                amountTouched: false,
                estimate: null,
                estimateError: null,
                estimateLoading: false,
                estimateTimer: null,
                showCryptoPayment: false,
                cryptoPaymentData: null,
                currentStatus: 'waiting',
                pollTimer: null,
                expiresIn: null,
                expiryTimer: null,
            }
        },
        computed: {
            isAuthenticated() {
                const authStore = useAuthStore();
                return authStore.isAuth;
            },
            setting() {
                // reactive: the store fills it asynchronously on first load
                return useSettingStore().setting;
            },
            /// the provider answers with its own codes: "usdcmatic" for the coin and
            /// "matic" for the chain. Neither is what a player has in their wallet,
            /// and "MATIC" is also the name of a different coin, so show the names
            /// the server worked out instead and only fall back to the raw ones.
            payTicker() {
                const d = this.cryptoPaymentData || {};
                if (d.pay_ticker) return d.pay_ticker;
                const coin = this.currencies.find(c => c.code === this.cryptoCurrency);
                return coin && coin.ticker ? coin.ticker : String(d.pay_currency || '').toUpperCase();
            },
            networkLabel() {
                const d = this.cryptoPaymentData || {};
                if (d.network_label) return d.network_label;
                const coin = this.currencies.find(c => c.code === this.cryptoCurrency);
                if (coin && coin.network) return coin.network;
                return String(d.network || d.pay_currency || '').toUpperCase();
            },
            currencyCodes() {
                /// show the tickers people know, not the provider's glued-together codes
                return this.currencies.map(c => c.ticker || c.code.replace(/(trc20|erc20|bsc|matic)$/i, '').toUpperCase())
                    .filter((v, i, a) => a.indexOf(v) === i)
                    .join(', ');
            },
            belowProviderMinimum() {
                if(!this.estimate || !this.estimate.min || !this.estimate.min.fiat) return false;
                return parseFloat(this.cryptoAmount) < parseFloat(this.estimate.min.fiat);
            },
            /**
             * The real smallest deposit for the coin on screen.
             *
             * The site has its own minimum, but each coin also has one at the
             * provider that moves with the network fee: bitcoin sits far above
             * the others. Showing only the site figure told players they could
             * pay 10 when bitcoin would have been refused.
             */
            effectiveMin() {
                const siteMin = this.setting ? parseFloat(this.setting.min_deposit) : 0;
                const coinMin = (this.estimate && this.estimate.min && this.estimate.min.fiat)
                    ? Math.ceil(parseFloat(this.estimate.min.fiat))
                    : 0;
                return Math.max(siteMin || 0, coinMin || 0);
            },
            statusLabel() {
                switch (this.currentStatus) {
                    case 'waiting': return 'Waiting for payment';
                    case 'confirming': return 'Confirming on the network';
                    case 'confirmed':
                    case 'sending':
                    case 'finished': return 'Payment confirmed';
                    case 'partially_paid': return 'Partially paid';
                    case 'expired': return 'Expired';
                    case 'failed': return 'Failed';
                    default: return this.currentStatus;
                }
            }
        },
        beforeUnmount() {
            this.stopTimers();
            this.paymentType = null;
        },
        methods: {
            setPaymentMethod: function(type) {
                this.paymentType = type;
                if(type === 'crypto') {
                    this.cryptoAmount = this.setting ? this.setting.min_deposit : null;
                    this.amountTouched = false;
                    this.requestEstimate();
                }
            },
            resetCrypto: function() {
                this.stopTimers();
                this.showCryptoPayment = false;
                this.cryptoPaymentData = null;
                this.currentStatus = 'waiting';
                this.estimate = null;
                this.requestEstimate();
            },
            stopTimers: function() {
                clearTimeout(this.estimateTimer);
                clearInterval(this.pollTimer);
                clearInterval(this.expiryTimer);
            },
            getWallet: function() {
                const _this = this;
                const _toast = useToast();

                HttpApi.get('profile/wallet')
                    .then(response => {
                        _this.wallet = response.data.wallet;
                    })
                    .catch(error => {
                        try {
                            Object.entries(JSON.parse(error.request.responseText)).forEach(([key, value]) => {
                                _toast.error(`${value}`);
                            });
                        } catch (e) {}
                    });
            },
            getCurrencies: function() {
                const _this = this;
                HttpApi.get('crypto/currencies').then(response => {
                    _this.cryptoEnabled = !!response.data.enabled && response.data.currencies.length > 0;
                    _this.priceCurrency = response.data.price_currency || 'USD';
                    _this.currencies = response.data.currencies || [];
                    if(_this.currencies.length && !_this.currencies.find(c => c.code === _this.cryptoCurrency)) {
                        _this.cryptoCurrency = _this.currencies[0].code;
                    }
                }).catch(() => {
                    _this.cryptoEnabled = false;
                });
            },
            requestEstimate: function() {
                const _this = this;
                clearTimeout(this.estimateTimer);
                this.estimate = null;
                this.estimateError = null;

                const amount = parseFloat(this.cryptoAmount);
                if(!amount || amount <= 0) return;

                this.estimateLoading = true;
                this.estimateTimer = setTimeout(() => {
                    HttpApi.get('crypto/estimate', { params: { amount: amount, currency: _this.cryptoCurrency } })
                        .then(response => {
                            if(response.data.success) {
                                _this.estimate = response.data.data;
                                // the pre-filled amount follows the coin's minimum until the
                                // player types their own, downwards too: switching from bitcoin
                                // to a cheap coin should not leave bitcoin's figure in the box
                                const min = _this.estimate.min && parseFloat(_this.estimate.min.fiat);
                                if(!_this.amountTouched && amount > _this.effectiveMin && _this.effectiveMin > 0) {
                                    _this.cryptoAmount = _this.effectiveMin.toFixed(2);
                                    _this.requestEstimate();
                                    return;
                                }
                                if(!_this.amountTouched && min && amount < min) {
                                    const max = parseFloat(_this.setting.max_deposit);
                                    const bumped = Math.ceil(min);
                                    if(!(max > 0) || bumped <= max) {
                                        _this.cryptoAmount = bumped.toFixed(2);
                                        _this.requestEstimate();
                                    }
                                }
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
            createCryptoPayment: function() {
                const _this = this;
                const _toast = useToast();
                const amount = parseFloat(_this.cryptoAmount);

                if(!amount || amount <= 0) {
                    _toast.error(_this.$t('Please enter a valid amount'));
                    return;
                }
                if(amount < _this.effectiveMin) {
                    _toast.error(_this.$t('Minimum deposit amount is') + ' ' + _this.effectiveMin.toFixed(2) + ' ' + _this.priceCurrency);
                    return;
                }
                if(parseFloat(_this.setting.max_deposit) > 0 && amount > parseFloat(_this.setting.max_deposit)) {
                    _toast.error(_this.$t('Maximum deposit amount is') + ' ' + _this.setting.max_deposit + ' ' + _this.priceCurrency);
                    return;
                }
                if(_this.belowProviderMinimum) {
                    _toast.error(_this.$t('Amount is below the provider minimum for this coin'));
                    return;
                }

                _this.isLoading = true;

                HttpApi.post('crypto/payment', {
                    amount: amount,
                    currency: _this.cryptoCurrency
                }).then(response => {
                    if(response.data.success) {
                        _this.cryptoPaymentData = response.data.data;
                        _this.currentStatus = response.data.data.status || 'waiting';
                        _this.showCryptoPayment = true;
                        _this.startPolling();
                        _this.startExpiryCountdown();
                        _toast.success(_this.$t('Payment address generated successfully'));
                    } else {
                        _toast.error(response.data.message || _this.$t('Failed to create payment'));
                    }
                }).catch(error => {
                    let message = _this.$t('Error creating crypto payment');
                    try {
                        const body = JSON.parse(error.request.responseText);
                        if(body.message) message = body.message;
                        if(body.errors) message = Object.values(body.errors).flat().join(' ');
                    } catch (e) {}
                    _toast.error(message);
                }).finally(() => {
                    _this.isLoading = false;
                });
            },
            startPolling: function() {
                clearInterval(this.pollTimer);
                this.pollTimer = setInterval(() => this.checkCryptoStatus(false), 15000);
            },
            startExpiryCountdown: function() {
                clearInterval(this.expiryTimer);
                if(!this.cryptoPaymentData || !this.cryptoPaymentData.expires_at) {
                    this.expiresIn = null;
                    return;
                }
                const expiresAt = new Date(this.cryptoPaymentData.expires_at).getTime();
                const tick = () => {
                    const diff = Math.max(0, Math.floor((expiresAt - Date.now()) / 1000));
                    const m = Math.floor(diff / 60), s = diff % 60;
                    this.expiresIn = `${m}:${String(s).padStart(2, '0')}`;
                    if(diff <= 0) clearInterval(this.expiryTimer);
                };
                tick();
                this.expiryTimer = setInterval(tick, 1000);
            },
            copyCryptoAddress: function() {
                const _this = this;
                const _toast = useToast();

                if(!_this.cryptoPaymentData || !_this.cryptoPaymentData.pay_address) return;

                navigator.clipboard.writeText(_this.cryptoPaymentData.pay_address).then(() => {
                    _toast.success(_this.$t('Address copied to clipboard'));
                }).catch(() => {
                    _toast.error(_this.$t('Failed to copy address'));
                });
            },
            checkCryptoStatus: function(manual) {
                const _this = this;
                const _toast = useToast();

                if(!_this.cryptoPaymentData || !_this.cryptoPaymentData.payment_id) return;

                HttpApi.get('crypto/status/' + _this.cryptoPaymentData.payment_id).then(response => {
                    const status = response.data.status;
                    _this.currentStatus = status;

                    if(response.data.credited || status === 'finished' || status === 'confirmed') {
                        _this.stopTimers();
                        _toast.success(_this.$t('Payment confirmed!'));
                        _this.getWallet();
                        const authStore = useAuthStore();
                        if(authStore.checkToken) { authStore.checkToken().then(u => u && authStore.setUser(u)).catch(() => {}); }
                        _this.showCryptoPayment = false;
                        _this.paymentType = null;
                        _this.$emit('deposited');
                    } else if(status === 'failed' || status === 'expired') {
                        _this.stopTimers();
                        if(manual) _toast.error(_this.$t('Payment failed or expired'));
                    } else if(manual) {
                        _toast.info(_this.$t('Waiting for payment...'));
                    }
                }).catch(() => {
                    if(manual) _toast.error(_this.$t('Failed to check payment status'));
                });
            },
        },
        created() {
            if(this.isAuthenticated) {
                this.getWallet();
                this.getCurrencies();
            }
        },
    };
</script>

<style scoped>

</style>
