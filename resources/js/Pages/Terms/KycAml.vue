<template>
    <BaseLayout>
        <TermsContent title="KYC and AML Policy" :content="kycAml" />
    </BaseLayout>
</template>

<script>

import BaseLayout from "@/Layouts/BaseLayout.vue";
import TermsContent from "./TermsContent.vue";
import kycAml from "./kyc-aml.js";

export default {
    props: [],
    components: {BaseLayout, TermsContent},
    data() {
        return {
            kycAml,
        }
    },
};
</script>
