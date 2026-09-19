<template>
    <BaseLayout>
        <TermsContent title="Bonus Terms" :content="bonusTerms" />
    </BaseLayout>
</template>

<script>

import BaseLayout from "@/Layouts/BaseLayout.vue";
import TermsContent from "./TermsContent.vue";
import bonusTerms from "./bonus-terms.js";

export default {
    props: [],
    components: {BaseLayout, TermsContent},
    data() {
        return {
            bonusTerms,
        }
    },
};
</script>
