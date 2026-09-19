<template>
    <BaseLayout>
        <TermsContent title="Welcome Bonus" :content="welcomeBonus" />
    </BaseLayout>
</template>

<script>

import BaseLayout from "@/Layouts/BaseLayout.vue";
import TermsContent from "./TermsContent.vue";
import welcomeBonus from "./welcome-bonus.js";

export default {
    props: [],
    components: {BaseLayout, TermsContent},
    data() {
        return {
            welcomeBonus,
        }
    },
};
</script>
