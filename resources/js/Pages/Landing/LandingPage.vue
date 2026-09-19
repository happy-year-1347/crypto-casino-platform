<template>
    <LandingLayout>
        <LoadingComponent :isLoading="isLoading">
            <div class="text-center">
                <span>{{ $t('Loading...') }}</span>
            </div>
        </LoadingComponent>

        <div v-if="!isAuthenticated && spinEnabled" class="container mx-auto p-4 mt-16">
            <div class="grid mb-4 mt-1">
                <div class="relative" style="height: 100vh;">
                    <LandingModal :showModal="showModal" @close="CloseModal" @loaded="HandleLoaded"/>
                </div>
            </div>
        </div>
    </LandingLayout>
</template>

<script>
    import { ref } from "vue";

    import LandingLayout from '@/Layouts/LandingLayout.vue';
    import { useAuthStore } from "@/Stores/Auth.js";
    import { useSettingStore } from "@/Stores/SettingStore.js";
    import LoadingComponent from "@/Components/UI/LoadingComponent.vue";
    import LandingModal from '@/Components/LandingSpin/LandingModal.vue';

    export default {
        props: [],
        components: {
            LoadingComponent,
            LandingLayout,
            LandingModal
        },
        data() {
            return {
                isLoading: true,
                showModal: false
            }
        },
        setup(props) {
            const ckCarouselOriginals = ref(null);
            return {
                ckCarouselOriginals
            };
        },
        computed: {
            isAuthenticated() {
                const authStore = useAuthStore();
                return authStore.isAuth;
            },
            spinEnabled() {
                const setting = useSettingStore().setting;
                return !!(setting && setting.disable_spin);
            }
        },
        mounted() {
            // the wheel is a switch in Admin > Settings; when it is off this page
            // has nothing to show, so send the visitor to the lobby instead
            if (this.isAuthenticated || !this.spinEnabled) {
                location.href='/';
            }
        },
        methods: {
            CloseModal() {
                this.showModal=false;
                location.href='/';
            },
            HandleLoaded() {
                this.showModal = true;
                this.isLoading = false;
            }
        }
    };
</script>

<style>
    html, document, body {
        user-select: none;
    }
</style>
