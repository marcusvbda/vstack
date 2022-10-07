<template>
    <tr>
        <td class="w-25">
            <div class="d-flex flex-column">
                <span class="input-title" v-if="label" v-html="label ? label : ''" />
                <small v-if="description" class="mt-1 text-muted">
                    <span v-html="description"></span>
                </small>
            </div>
        </td>
        <td class="align-middle" v-loading="loading" element-loading-text="Carregando ..."
            element-loading-spinner="hidden">
            <div v-html="processed_value" v-if="!loading" />
        </td>
    </tr>
</template>
<script>
export default {
    props: ["label", "description", "field", "value", "type", "options", "model", "format"],
    data() {
        return {
            loading: true,
            option_list: [],
        };
    },
    created() {
        this.init();
    },
    computed: {
        processed_value() {
            const actions = {
                belongsTo: () => {
                    if (this.option_list.length) {
                        if (typeof this.option_list[1] == "object") {
                            let found = this.option_list.find((option) => option.id.toString() == this.value.toString());
                            if (!found) {
                                return "";
                            }
                            if (found.name) {
                                return found.name;
                            }
                            if (found.value) {
                                return found.value;
                            }
                            return this.value;
                        }
                        return this.value;
                    }
                    return "";
                },
                check: () => {
                    return this.$getEnabledIcons(this.value);
                },
                datetimerange: () => {
                    return (Array.isArray(this.value) ? this.value : [])
                        .map((date) => {
                            return this.formatDate(this.value);
                        })
                        .join(" - ");
                },
                date: () => {
                    return this.formatDate(this.value);
                },
                datetime: () => {
                    return this.formatDate(this.value);
                },
                upload: () => {
                    let images = (Array.isArray(this.value) ? this.value : [])
                        .map((image) => {
                            return `<img src='${image.url}' />'`;
                        })
                        .join("");
                    return `<div class='upload-image-preview'>${images}</div>`;
                },
            };
            return actions[this.type] ? actions[this.type]() : this.value;
        },
    },
    methods: {
        formatDate(dt) {
            let date = this.$moment(dt);
            if (!date.isValid()) {
                return "";
            }
            return date.format(this.format);
        },
        async init() {
            if (this.type == "belongsTo") {
                if (this.model) {
                    let { data } = await this.$http.post("/vstack/json-api", {
                        model: this.model,
                        order_by: ["id", "desc"],
                    });
                    this.option_list = data;
                } else {
                    this.option_list = this.options;
                }
            }
            this.loading = false;
        },
    },
};
</script>
<style lang="scss">
.upload-image-preview {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: flex-start;

    img {
        height: 145px;
        border-radius: 6px;
        border: 1px solid #c3c3c3;

        +img {
            margin-left: 5px;
        }
    }
}
</style>
