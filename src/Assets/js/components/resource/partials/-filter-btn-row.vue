<template>
    <tbody class="border">
        <tr v-if="f.label">
            <td
                class="p-2 cursor-pointer"
                style="position: relative"
                @click="visible = !visible"
                :id="`resource-filter-item-${index}`"
            >
                <span class="badge-number" v-if="hasBadge" v-html="'!'" />
                <div class="w-full">
                    <div
                        :class="`flex  justify-between text-bold w-full ${
                            hasBadge ? 'pl-3' : ''
                        }`"
                    >
                        <label
                            class="mb-0 text-neutral-600"
                            style="font-size: 13px; font-weight: bold"
                            v-html="f.label ? f.label : k"
                        />
                        <span
                            :class="`el-icon-arrow-${!visible ? 'down' : 'up'}`"
                        />
                    </div>
                </div>
            </td>
        </tr>
        <tr v-if="visible">
            <td class="p-2" style="padding-bottom: 20px">
                <div class="shimmer select w-full" v-if="!loaded" />
                <v-runtime-template
                    v-else
                    :key="k"
                    :template="unescape(f.view)"
                    :id="`resource-filter-item-${index}-input`"
                />
            </td>
        </tr>
    </tbody>
</template>
<script>
import VRuntimeTemplate from 'v-runtime-template';
import { mapGetters, mapMutations } from 'vuex';

export default {
    props: ['filter', 'k', 'data', 'f', 'index', 'makeNewRoute'],
    data() {
        return {
            visible: this.hasContent(this.filter, this.index) ? true : false,
            timer: null,
            loaded: false,
        };
    },
    components: {
        'v-runtime-template': VRuntimeTemplate,
    },
    watch: {
        visible() {
            this.initFilter();
        },
    },
    computed: {
        ...mapGetters('resource', ['filter_options']),
        option_model_index() {
            return (this.f?.model ? this.f.model : this.f.index)
                .replaceAll('\\', '_')
                .toLowerCase();
        },
        hasBadge() {
            return (
                this.index != 'per_page' &&
                this.hasContent(this.filter, this.index)
            );
        },
    },
    created() {
        this.initFilter();
    },
    methods: {
        ...mapMutations('resource', ['addFilterOptions']),
        initFilter() {
            if (!this.visible || this.loaded) {
                return;
            }
            if (this.f.component != 'select-filter' || !this.f.model) {
                return (this.loaded = true);
            }
            const results = this.filter_options[this.option_model_index] ?? [];
            if (results.length) {
                return this.processOptionData(results);
            }

            this.$http
                .post('/vstack/json-api', {
                    filter_class_name: this.f.class_name,
                    model: this.f.model,
                })
                .then(({ data }) => {
                    this.processOptionData(data);
                });
        },
        processOptionData(data) {
            let payload = {};
            payload[this.option_model_index] = data;
            this.addFilterOptions(payload);
            const options = data.map((x) => {
                return {
                    value: x[this.f.model_fields.value],
                    label: x[this.f.model_fields.label],
                };
            });
            this.f.options = options;
            const options_views = this.f.view.replace(
                '</el-select>',
                `<el-option v-for="(option,i) in f.options" :key='i' :value='option.value' :label='option.label' /></el-select>`
            );
            this.f.view = options_views;
            this.loaded = true;
        },
        unescape(htmlStr) {
            htmlStr = htmlStr.replace(/&lt;/g, '<');
            htmlStr = htmlStr.replace(/&gt;/g, '>');
            htmlStr = htmlStr.replace(/&quot;/g, '"');
            htmlStr = htmlStr.replace(/&#039;/g, "'");
            htmlStr = htmlStr.replace(/&amp;/g, '&');
            return htmlStr;
        },
        hasContent(filter, key) {
            if (!filter) {
                return false;
            }
            if (filter[key]) {
                if (Array.isArray(filter[key])) {
                    return filter[key].filter((x) => x).length > 0
                        ? true
                        : false;
                }
                return true;
            }
            return false;
        },
        showConfirm() {
            this.$emit('show-confirm-btn', true);
        },
    },
};
</script>
