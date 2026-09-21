<template>
    <BaseLayout>
        <TermsContent title="Responsible Gambling" :content="responsibleGambling" />
    </BaseLayout>
</template>

<script>

import BaseLayout from "@/Layouts/BaseLayout.vue";
import TermsContent from "./TermsContent.vue";
import responsibleGambling from "./responsible-gambling.js";

export default {
    props: [],
    components: {BaseLayout, TermsContent},
    data() {
        return {
            responsibleGambling,
        }
    },
};
</script>
