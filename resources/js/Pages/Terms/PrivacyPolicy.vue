<template>
    <BaseLayout>
        <TermsContent title="Privacy Policy" :content="policy" />
    </BaseLayout>
</template>

<script>

import BaseLayout from "@/Layouts/BaseLayout.vue";
import TermsContent from "./TermsContent.vue";
import policy from "./privacy-policy.js";

export default {
    props: [],
    components: {BaseLayout, TermsContent},
    data() {
        return {
            policy,
        }
    },
};
</script>
