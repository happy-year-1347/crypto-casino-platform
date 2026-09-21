<template>
    <BaseLayout>
        <TermsContent title="Restricted Countries" :content="restrictedCountries" />
    </BaseLayout>
</template>

<script>

import BaseLayout from "@/Layouts/BaseLayout.vue";
import TermsContent from "./TermsContent.vue";
import restrictedCountries from "./restricted-countries.js";

export default {
    props: [],
    components: {BaseLayout, TermsContent},
    data() {
        return {
            restrictedCountries,
        }
    },
};
</script>
