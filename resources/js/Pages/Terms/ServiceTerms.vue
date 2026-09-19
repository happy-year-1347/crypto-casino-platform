<template>
    <BaseLayout>
        <TermsContent title="Terms of Service" :content="terms" />
    </BaseLayout>
</template>

<script>

import BaseLayout from "@/Layouts/BaseLayout.vue";
import TermsContent from "./TermsContent.vue";
import terms from "./service-terms.js";

export default {
    props: [],
    components: {BaseLayout, TermsContent},
    data() {
        return {
            terms,
        }
    },
};
</script>
