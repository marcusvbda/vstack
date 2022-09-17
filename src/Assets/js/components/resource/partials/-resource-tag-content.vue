<template>
    <div>
        <div class="ml-2 shimmer primary" v-if="loading" :style="{height:16, width: 140}" />
        <span class=" ml-2" v-else v-html="content" />
    </div>
</template>
<script>
import { mapGetters, mapMutations } from "vuex";
export default {
    props: ["filter"],
    data() {
        return {
            loading: true,
            content: this.filter.content
        }
    },
    created() {
        this.init();
    },
    computed: {
        ...mapGetters("resource", ["filter_options"]),
        option_model_index() {
            return (this.filter.original?.model ? this.filter.original.model : this.filter.index).replaceAll("\\", "_").toLowerCase();
        }
    },
    methods: {
        ...mapMutations("resource", ["addFilterOptions"]),
        processOptionData(data) {
            const values = this.filter.get_value.split(",");
            let payload = {};
            payload[this.option_model_index] = data;
            this.addFilterOptions(payload);

            const filtered = data.filter(x => {
                const value = x[this.filter.original.model_fields.value];
                return values.includes(String(value)) || values.includes(value);
            });

            this.content = filtered.map(x => x[this.filter.original.model_fields.label]).join(", ");

            this.loading = false;
        },
        init() {
            if (!this.filter.original.model && this.content) {
                return this.loading = false;
            } else {
                const results = this.filter_options[this.option_model_index] ?? [];
                if (results.length) {
                    return this.processOptionData(results);
                }

                this.$http
                    .post("/vstack/json-api", {
                        model: this.filter.original.model,
                    })
                    .then(({ data }) => {
                        this.processOptionData(data);
                    });
            }
        }
    }
}
</script>
<style lang="scss">
.shimmer.primary {
    background: linear-gradient(to right, #3397ff 0%, #aed4fa 20%, #6db3fa 40%, #7eb6f0 100%);
    background-color: #409eff;
    border: 1px solid #409eff;
}
</style>