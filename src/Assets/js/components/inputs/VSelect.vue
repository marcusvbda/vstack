<template>
    <CustomResourceComponent :label="label" :description="description">
        <div class="d-flex flex-column">
            <slot name="prepend-slot" />
            <div class="input-group v-select" v-bind:class="{ 'is-invalid': errors }">
                <template v-if="multiple && !allow_create && type == 'radio'">
                    <template v-if="loading">
                        <div class="shimmer select mb-3" />
                        <div class="shimmer select mb-3" />
                        <div class="shimmer select mb-3" />
                    </template>
                    <div class="d-flex flex-row" v-else>
                        <el-checkbox-group class="vstack-hasmany" v-model="value">
                            <el-checkbox-button v-for="(op, i) in options" :label="op.id" :key="i">
                                {{ op.name }}
                            </el-checkbox-button>
                        </el-checkbox-group>
                        <a class="px-3 text-center f-12" @click.prevent="toggleMarked" href="#">
                            {{ !marked ? 'Marcar' : 'Desmarcar' }} todas as opções
                        </a>
                    </div>
                </template>
                <template v-else>
                    <div class="shimmer select" v-if="loading" />
                    <el-select :allow-create="allow_create" :disabled="disabled" v-else :size="size ? size : 'large'"
                        :allow_create="allow_create" class="w-100" clearable v-model="value" filterable ref="select"
                        :placeholder="placeholder" v-loading="loading" :loading="loading"
                        @keyup.enter.native="selectCreate" loading-text="Carregando..." :multiple="multiple"
                        :popper-append-to-body="false">
                        <el-option v-for="(item, i) in options" :key="i" :label="item.name" :value="String(item.id)">
                            <div class="w-100 d-flex" v-html="item.name"></div>
                        </el-option>
                    </el-select>
                </template>
                <div class="invalid-feedback" v-if="errors">
                    <ul class="pl-3 mb-0">
                        <li v-for="(e, i) in errors" :key="i" v-html="e" />
                    </ul>
                </div>
            </div>
            <slot name="append-slot" />
        </div>
    </CustomResourceComponent>
</template>
<script>
import { mapGetters, mapMutations } from "vuex";

export default {
    props: [
        "placeholder",
        "label",
        "route_list",
        "list_model",
        "disabled",
        "errors",
        "optionlist",
        "required",
        "size",
        "multiple",
        "relation",
        "allowcreate",
        "option_list",
        "description",
        "all_options_label",
        "model_fields",
        "field_index",
        "allow_create",
        'model_filter',
        "type"
    ],
    data() {
        return {
            loading: true,
            started: false,
            options: [],
            value: this.multiple ? [] : null,
            marked: false
        };
    },
    created() {
        this.initialize();
    },
    watch: {
        "$attrs.value"(val) {
            this.value = val;
        },
        value(val) {
            if (this.started) {
                this.$emit("input", val);
                if (Array.isArray(this.value)) {
                    this.marked = this.value.length === this.options.length
                }
            }
        }
    },
    computed: {
        ...mapGetters("resource", ["field_options"]),
        option_model_index() {
            return (this.list_model ? this.list_model : "").replaceAll("\\", "_").toLowerCase();
        }
    },
    methods: {
        ...mapMutations("resource", ["addFieldOptions"]),
        selectCreate() {
            if (!this.allow_create) {
                return;
            }
            const typed_value = this.$refs.select.query;
            if (!this.multiple) {
                this.value = typed_value;
            } else {
                this.value.push(typed_value)
            }
        },
        toggleMarked() {
            if (!this.marked) {
                this.value = this.options.map(x => x.id);
            } else {
                this.value = [];
            }
            this.marked = !this.marked;
        },
        initialize() {
            if (this.list_model) {
                this.initOptions(() => {
                    this.value = this.$attrs.value ? this.$attrs.value : this.multiple ? [] : null;
                    if (!Array.isArray(this.value)) {
                        this.value = this.value ? String(this.value) : null;
                    } else {
                        this.value = this.value.map(x => String(x));
                    }
                    this.finishInit();
                });
            } else {
                for (let i in this.option_list) {
                    let value = this.option_list[i];
                    if (typeof value == "string") {
                        this.options.push({ id: value, name: value });
                    }
                    if (typeof value == "object") {
                        this.options.push({ id: String(value.id), name: value.value });
                    }
                }
                this.value = this.$attrs.value ? this.$attrs.value : this.multiple ? [] : null;
                if (Array.isArray(this.value)) {
                    this.value = this.value.map(x => String(x));
                }
                this.$nextTick(() => {
                    this.finishInit();
                })
            }
        },
        finishInit() {
            this.loading = false;
            this.started = true;
        },
        initOptions(callback) {
            if (this.optionlist || !this.list_model) {
                this.options = this.optionlist ? this.optionlist : [];
                return callback();
            }

            if (this.field_options[this.option_model_index] == "loading" || !this.field_options[this.option_model_index]) {
                this.$watch(`field_options.${this.option_model_index}`, val => {
                    if (Array.isArray(val)) {
                        this.options = val
                        this.$nextTick(() => {
                            callback();
                        });
                    }
                });

                if (!this.field_options[this.option_model_index]) {
                    let field_op = {};
                    field_op[this.option_model_index] = "loading"
                    this.addFieldOptions(field_op);

                    const payload = {
                        params: { model: this.list_model, model_fields: this.model_fields, model_filter: this.model_filter }
                    }
                    return this.$http
                        .post(this.route_list, payload)
                        .then((res) => {
                            res = res.data;
                            let field_op = {};
                            field_op[this.option_model_index] = res.data.map(x => {
                                return { ...x, id: String(x.id) };
                            });
                            this.addFieldOptions(field_op);
                        })
                        .catch((er) => {
                            this.loading = false;
                            console.log(er);
                        });
                }
            } else {
                this.options = this.field_options[this.option_model_index]
                this.$nextTick(() => {
                    callback();
                });
            }


        },
    },
};
</script>
