<template>
    <CustomResourceComponent :label="label" :description="description" custom_class="display-revert">
        <div class="flex flex-col">
            <slot name="prepend-slot" />
            <div class="input-group v-select" v-bind:class="{ 'is-invalid': errors }">
                <template v-if="canShowEntity">
                    <template v-if="multiple && !allow_create && type == 'radio'">
                        <template v-if="loading">
                            <div class="shimmer select mb-3" />
                            <div class="shimmer select mb-3" />
                            <div class="shimmer select mb-3" />
                        </template>
                        <div class="flex" v-else>
                            <el-checkbox-group class="vstack-hasmany" v-model="value">
                                <el-checkbox-button v-for="(op, i) in options" :label="op.id" :key="i">
                                    {{ op.name }}
                                </el-checkbox-button>
                            </el-checkbox-group>
                            <a class="px-3 text-center f-12" @click.prevent="toggleMarked" href="#">
                                {{ !marked ? 'Marcar' : 'Desmarcar' }} todas as
                                opções
                            </a>
                        </div>
                    </template>
                    <template v-else>
                        <div class="shimmer select" v-if="loading" />
                        <el-select :allow-create="allow_create" :disabled="disabled" v-else :size="size ? size : 'large'"
                            :allow_create="allow_create" class="w-full" clearable v-model="value" filterable ref="select"
                            :placeholder="placeholder" v-loading="loading" :loading="loading"
                            @keyup.enter.native="selectCreate" loading-text="Carregando..." :multiple="multiple"
                            :popper-append-to-body="false">
                            <el-option-group v-for="group in groups" :key="group.label" :label="group.label">
                                <el-option v-for="(item, i) in group.options" :key="i" :label="item.name"
                                    :value="String(item.id)" style="height: unset !important;">
                                    <VRuntimeTemplate v-if="option_template" :template-props="{
                                        index: i,
                                        item,
                                        value: value,
                                        options: options,
                                    }" :template="option_template" />
                                    <div v-else class="w-full flex" v-html="item.name"></div>
                                </el-option>
                            </el-option-group>
                        </el-select>
                    </template>
                </template>
                <span v-else class="text-sm text-gray-500">
                    {{ entity_parent_message }}
                </span>
                <div class="invalid-feedback" v-if="errors">
                    <ul class="text-sm text-red-700">
                        <li v-for="(e, i) in errors" :key="i" v-html="e" />
                    </ul>
                </div>
            </div>
            <slot name="append-slot" />
        </div>
    </CustomResourceComponent>
</template>
<script>
import { mapGetters, mapMutations } from 'vuex';
import VRuntimeTemplate from 'v-runtime-template';
export default {
    props: [
        'placeholder',
        'label',
        'route_list',
        'list_model',
        'disabled',
        'errors',
        'optionlist',
        'group_by',
        'required',
        'size',
        'multiple',
        'relation',
        'allowcreate',
        'option_list',
        'description',
        'all_options_label',
        'model_fields',
        'field_index',
        'allow_create',
        'model_filter',
        'type',
        'option_template',
        'entity_parent',
        'entity_parent_message',
        'page_type',
    ],
    components: {
        VRuntimeTemplate,
    },
    data() {
        return {
            loading: true,
            started: false,
            options: [],
            value: this.multiple ? [] : null,
            marked: false,
        };
    },
    created() {
        let val = this.$attrs.value
            ? this.$attrs.value
            : this.multiple
                ? []
                : null;
        if (!Array.isArray(val)) {
            val = val ? String(val) : null;
        } else {
            val = val.map((x) => String(x));
        }
        this.value = val;
        if (this.entity_parent && ['create', 'edit'].includes(this.page_type)) {
            this.$watch(`$parent.form.${this.entity_parent}`, () => {
                this.loading = true;
                this.value = this.multiple ? [] : null;
                this.initialize();
            });
            if (this?.$parent?.form?.[this.entity_parent]) {
                this.initialize();
            }
        } else {
            this.initialize();
        }
    },
    watch: {
        '$attrs.value'(val) {
            this.value = val;
        },
        value(val) {
            if (this.started) {
                this.$emit('input', val);
                if (Array.isArray(this.value)) {
                    this.marked = this.value.length === this.options.length;
                }
            }
        },
    },
    computed: {
        ...mapGetters('resource', ['field_options', 'resource_id']),
        groups() {
            if (!this.group_by) return [{ label: '', options: this.options }]
            let groups = [];
            for (let i in this.options) {
                const index = this.group_by + "_grouping";
                let option = this.options[i];
                let group = groups.find((x) => x.label == option[index]);
                if (!group) {
                    group = { label: option[index], options: [] };
                    groups.push(group);
                }
                group.options.push(option);
            }
            return groups;
        },
        option_model_index() {
            return (this.list_model ? this.list_model : '')
                .replaceAll('\\', '_')
                .toLowerCase();
        },
        canShowEntity() {
            if (
                this.entity_parent &&
                this.entity_parent_message &&
                (!this.$parent.form[this.entity_parent] ||
                    !this.$parent.form[this.entity_parent]?.length)
            ) {
                return false;
            }
            return true;
        },
    },
    methods: {
        ...mapMutations('resource', ['addFieldOptions']),
        selectCreate() {
            if (!this.allow_create) {
                return;
            }
            const typed_value = this.$refs.select.query;
            if (!this.multiple) {
                this.value = typed_value;
            } else {
                this.value.push(typed_value);
            }
        },
        toggleMarked() {
            if (!this.marked) {
                this.value = this.options.map((x) => x.id);
            } else {
                this.value = [];
            }
            this.marked = !this.marked;
        },
        initialize() {
            if (this.list_model) {
                this.initOptions(() => {
                    this.finishInit();
                });
            } else {
                for (let i in this.option_list) {
                    let value = this.option_list[i];
                    if (typeof value == 'string') {
                        this.options.push({ id: value, name: value });
                    }
                    if (typeof value == 'object') {
                        this.options.push({
                            id: String(value.id),
                            name: value.value,
                        });
                    }
                }
                this.value = this.$attrs.value
                    ? this.$attrs.value
                    : this.multiple
                        ? []
                        : null;
                if (Array.isArray(this.value)) {
                    this.value = this.value.map((x) => String(x));
                }
                this.$nextTick(() => {
                    this.finishInit();
                });
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

            if (
                this.field_options[this.option_model_index] == 'loading' ||
                !this.field_options[this.option_model_index] ||
                this.entity_parent
            ) {
                this.$watch(
                    `field_options.${this.option_model_index}`,
                    (val) => {
                        if (Array.isArray(val)) {
                            this.options = val;
                            this.$nextTick(() => {
                                callback();
                            });
                        }
                    }
                );

                if (
                    !this.field_options[this.option_model_index] ||
                    this.entity_parent
                ) {
                    let field_op = {};
                    field_op[this.option_model_index] = 'loading';

                    if (
                        this.entity_parent &&
                        !this.$parent?.form?.[this.entity_parent]
                    ) {
                        return;
                    }

                    this.addFieldOptions(field_op);

                    let parentCondition = [];
                    if (this.entity_parent && this.canShowEntity) {
                        if (this.$parent?.form?.[this.entity_parent]) {
                            parentCondition = [
                                this.entity_parent,
                                '=',
                                this.$parent?.form?.[this.entity_parent],
                            ];
                        }
                    }

                    let model_fields = this.model_fields
                    if (this.group_by) {
                        model_fields[this.group_by + "_grouping"] = this.group_by
                    }

                    const payload = {
                        params: {
                            field_index: this.field_index,
                            resource_id: this.resource_id,
                            form: this.$parent?.form,
                            query: {
                                model: this.list_model,
                                model_fields,
                                model_filter: {
                                    ...this.model_filter,
                                    ...{
                                        where: [
                                            (
                                                this.model_filter?.where || []
                                            ).concat(parentCondition),
                                        ],
                                    },
                                },
                            },
                        },
                    };

                    return this.$http
                        .post(this.route_list, payload)
                        .then((res) => {
                            res = res.data;
                            let field_op = {};
                            field_op[this.option_model_index] = res.data.map(
                                (x) => {
                                    return { ...x, id: String(x.id) };
                                }
                            );
                            this.addFieldOptions(field_op);
                        })
                        .catch((er) => {
                            this.loading = false;
                            console.log(er);
                        });
                }
            } else {
                this.options = this.field_options[this.option_model_index];
                this.$nextTick(() => {
                    callback();
                });
            }
        },
    },
};
</script>
