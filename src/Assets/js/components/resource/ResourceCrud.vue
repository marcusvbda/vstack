<template>
    <div :class="`row crud-type-${crud_type.template}`" id="crud-view">
        <div class="col-12">
            <form class="needs-validation m-0" novalidate v-on:submit.prevent>
                <template v-if="dialog">
                    <div class="row">
                        <div class="col-12">
                            <template v-for="(card, i) in data.fields">
                                <v-runtime-template :key="i" :template="card.view"  :id="`resource-crud-card-${card.label}`"/>
                            </template>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <template v-if="crud_type.template == 'page'">
                        <div class="row" id="resource-crud-page">
                            <div class="col-12 col-lg-9">
                                <template v-for="(card, i) in data.fields">
                                    <v-runtime-template :key="i" :template="card.view" :id="`resource-crud-card-${card.label}`" />
                                </template>
                            </div>
                            <div class="col-12 col-lg-3 fields-tab" id="resource-card-section-list">
                                <div class="row flex-column" :style="{ top: 10, position: 'sticky' }">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body d-none d-md-block d-lg-block" v-if="showPills">
                                                <div class="row" v-if="!right_card_content">
                                                    <div class="col-12">
                                                        <ul class="d-flex flex-column mb-0 pl-3">
                                                            <li v-for="(card, i) in namedCards" :key="i" :id="`resource-card-section-list-link-${card.label}`">
                                                                <a
                                                                    class="f-12 link"
                                                                    :href="`#${card.label}`"
                                                                    v-html="card.label"
                                                                />
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <v-runtime-template v-else :template="right_card_content" />
                                            </div>
                                            <div
                                                class="card-footer flex-wrap d-flex flex-row justify-content-end 
                                            p-2 align-items-center"
                                            >
                                                <el-button-group>
                                                    <el-button
                                                        v-if="first_btn"
                                                        :size="first_btn.size"
                                                        :type="first_btn.type"
                                                        @click="submit(first_btn.field)"
                                                        :loading="action_btn_loading"
                                                        id="resource-crud-btn-first"
                                                    >
                                                        <span v-html="first_btn.content" />
                                                    </el-button>
                                                    <el-button
                                                        v-if="second_btn"
                                                        :size="second_btn.size"
                                                        :type="second_btn.type"
                                                        @click="submit(second_btn.field)"
                                                        :loading="action_btn_loading"
                                                        id="resource-crud-btn-second"
                                                    >
                                                        <span v-html="second_btn.content" />
                                                    </el-button>
                                                </el-button-group>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-if="crud_type.template == 'wizard'">
                        <div class="row"  id="resource-crud-page">
                            <div class="col-12  col-lg-9 d-flex flex-column">
                                <div :class="`d-flex ${wizardContentClass}`">
                                    <el-steps
                                        :active="wizard_step"
                                        finish-status="success"
                                        :direction="wizardDirection"
                                        class="step-resource-crud py-0"
                                        :simple="isSimple"
                                        id="resource-wizard-steps"
                                    >
                                        <template v-for="(step, i) in data.fields">
                                            <el-step
                                                :key="`step_wizard_${i}`"
                                                :class="`resource-step ${getResourceStepStatus(i)}`"
                                                :id="`resource-wizard-steps-${step.label}`"
                                            >
                                                <div slot="icon" v-html="step.icon ? step.icon : i + 1" />
                                                <div slot="title" v-if="step.label" v-html="step.label" />
                                                <div v-if="step.description" slot="description" v-html="step.description" />
                                            </el-step>
                                        </template>
                                    </el-steps>
                                    <div :class="`flex-grow-1 ${wizardCrudClass}`"  >
                                        <transition :name="wizardTransitionName">
                                            <v-runtime-template
                                                :template="data.fields[wizard_step].view"
                                                :id="`resource-crud-card-${data.fields[wizard_step].label}`"
                                            />
                                        </transition>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3  fields-tab">
                                <div class="row flex-column" :style="{ top: 10, position: 'sticky' }">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body d-none d-md-block d-lg-block pb-0" v-if="showPills">
                                                <div class="row" v-if="!right_card_content">
                                                    <div class="col-12">
                                                        <el-timeline class="pl-0">
                                                            <el-timeline-item
                                                                v-for="(step, i) in namedCards"
                                                                :key="i"
                                                                :type="getTimelineItemColor(i)"
                                                                :timestamp="step.description"
                                                            >
                                                                {{ step.label }}
                                                            </el-timeline-item>
                                                        </el-timeline>
                                                    </div>
                                                </div>
                                                <v-runtime-template v-else :template="right_card_content" />
                                            </div>
                                            <div
                                                class="card-footer flex-wrap d-flex flex-row justify-content-end 
                                            p-2 align-items-center"
                                            >
                                                <el-button-group>
                                                    <template v-if="wizard_step == data.fields.length - 1">
                                                        <el-button
                                                            v-if="first_btn"
                                                            :size="first_btn.size"
                                                            :type="first_btn.type"
                                                            @click="submit(first_btn.field)"
                                                            :loading="action_btn_loading"
                                                        >
                                                            <span v-html="first_btn.content" />
                                                        </el-button>
                                                        <el-button
                                                            v-if="second_btn"
                                                            :size="second_btn.size"
                                                            :type="second_btn.type"
                                                            @click="submit(second_btn.field)"
                                                            :loading="action_btn_loading"
                                                        >
                                                            <span v-html="second_btn.content" />
                                                        </el-button>
                                                    </template>
                                                    <template v-else>
                                                        <el-button
                                                            v-if="wizard_step != 0"
                                                            :size="first_btn.size"
                                                            type="warning"
                                                            @click="previousStep"
                                                            :loading="loading_wizard_previous || action_btn_loading"
                                                        >
                                                            <span>
                                                                <div class="d-flex flex-row">
                                                                    <i class="el-icon-back mr-2"></i>
                                                                    Voltar
                                                                </div>
                                                            </span>
                                                        </el-button>
                                                        <el-button
                                                            :size="second_btn.size"
                                                            type="primary"
                                                            @click="nextStep"
                                                            :loading="loading_wizard_next || action_btn_loading"
                                                            id="resource-btn-next-step"
                                                        >
                                                            <span>
                                                                <div class="d-flex flex-row">
                                                                    <i class="el-icon-right mr-2"></i>
                                                                    Prosseguir
                                                                </div>
                                                            </span>
                                                        </el-button>
                                                    </template>
                                                </el-button-group>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </template>
                <slot name="aftercreate" v-if="['CREATE', 'CLONE'].includes(pageType)"></slot>
                <slot name="afteredit" v-if="['EDIT'].includes(pageType)"></slot>
            </form>
        </div>
    </div>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template";
import { mapGetters } from "vuex";

export default {
    props: [
        "crud_type",
        "data",
        "redirect",
        "params",
        "raw_type",
        "acl",
        "first_btn",
        "second_btn",
        "dialog",
        "right_card_content",
        "content_id",
        "content"
    ],
    data() {
        return {
            window_width: null,
            resourceData: {},
            form: {},
            errors: {},
            wizard_step: 0,
            wizardTransitionName: "",
            loading_wizard_next: false,
            loading_wizard_previous: false
        };
    },
    components: {
        "v-runtime-template": VRuntimeTemplate
    },
    computed: {
        ...mapGetters("resource", ["action_btn_loading"]),
        isSimple() {
            if (this.crud_type.template != "wizard") {
                return false;
            }
            if (this.crud_type.style == "row") {
                return true;
            }
            if (this.crud_type.style == "timeline") {
                return false;
            }
            return false;
        },
        wizardStepSide() {
            if (this.crud_type.template != "wizard") {
                return "";
            }
            if (this.crud_type.position == "top") {
                return "center";
            }
            return this.crud_type.position;
        },
        wizardDirection() {
            if (this.crud_type.template != "wizard") {
                return "";
            }
            if (this.window_width <= 991) {
                return "horizontal";
            }
            return ["left", "right"].includes(this.crud_type.position) && !this.isSimple ? "vertical" : "horizontal";
        },
        showPills() {
            return this.namedCards.length > 1 || this.right_card_content;
        },
        pageType() {
            return this.raw_type.toUpperCase();
        },
        namedCards() {
            return this.data.fields.filter(x => x.label);
        },
        wizardContentClass() {
            let justifyClasses = {
                left: "justify-content-start",
                right: "flex-row-reverse"
            };
            if (this.wizardDirection == "vertical") {
                return `flex-column flex-lg-row ${justifyClasses[this.wizardStepSide]}`;
            }
            if (this.wizardDirection === "horizontal") {
                return "flex-column";
            }
            return "";
        },
        wizardCrudClass() {
            let classes = {
                left: "ml-4",
                right: "mr-4"
            };
            if (this.wizardDirection === "horizontal") {
                return "mt-2";
            }
            return classes[this.crud_type.position] ?? "";
        }
    },
    async created() {
        this.$nextTick(() => {
            this.getScreenSize();
            this.initForm();
        });
    },
    methods: {
        getScreenSize() {
            this.window_width = window.innerWidth;
        },
        initScreenSizesWatcher() {
            window.onresize = () => {
                this.getScreenSize();
            };
        },
        getTimelineItemColor(index) {
            if (this.wizard_step == index) {
                return "secondary";
            }
            if (this.wizard_step < index) {
                return "";
            }
            if (this.wizard_step > index) {
                return "success";
            }
            return "";
        },
        handleActionBtnLoading(val) {
            this.action_btn_loading = val;
        },
        clikedStepWizard(index) {
            let stepStatus = this.getResourceStepStatus(index);
            if (stepStatus == "done") {
                this.wizard_step = index;
            }
        },
        getResourceStepStatus(index) {
            if (index < this.wizard_step) {
                return "done";
            }
            if (index == this.wizard_step) {
                return "current";
            }
            if (index > this.wizard_step) {
                return "blocked";
            }
            return "";
        },
        initForm() {
            this.initFields();
            this.loadParams();
            this.initWizardEvents();
            this.$set(this.form, "resource_id", this.data.resource_id);
            if (this.data.id && this.pageType == "EDIT") {
                this.$set(this.form, "id", this.data.id);
            }
        },
        initWizardEvents() {
            if (this.crud_type.template == "wizard") {
                this.$waitForEls(".resource-step").then(elements => {
                    elements.forEach((item, index) => {
                        [".el-step__head", ".el-step__main"].forEach(child => {
                            item.querySelectorAll(child).forEach(attr => {
                                attr.addEventListener("click", () => {
                                    this.clikedStepWizard(index);
                                });
                            });
                        });
                    });
                });
            }
        },
        initFields() {
            let fields = [];
            for (let i in this.data.fields) {
                let card = this.data.fields[i];
                for (let y in card.inputs) {
                    fields.push(card.inputs[y]);
                }
            }
            for (let i in fields) {
                if (fields[i].options) {
                    let field_name = fields[i].options.field;
                    let field_value = this.processFieldValue(field_name, fields[i].options);
                    if (field_name) {
                        this.$set(
                            fields[i].options.type == "resource-field" ? this.resourceData : this.form,
                            field_name,
                            field_value
                        );
                    }
                }
            }
        },
        processFieldValue(name, options) {
            let value = null;
            if (!["null", ""].includes(String(options.value))) {
                value = this.processFieldPerType(
                    options.type,
                    typeof options.value != "object" ? String(options.value) : options.value
                );
            } else {
                value = this.processFieldPerType(options.type, options.default);
            }
            return value;
        },
        processFieldPerType(type, value) {
            const actions = {
                tags: () => {
                    if (Array.isArray(value)) {
                        return value.map(x => String(x));
                    }
                    return value ? value.split(",") : [];
                },
                check: () => String(value) == "true",
                upload: () => (value ? (Array.isArray(value) ? value.map(x => String(x)) : value.split(",")) : []),
                daterange: () => {
                    if (Array.isArray(value)) {
                        return value.map(x => new Date(String(x)));
                    }
                    return value ? value.split(",").map(x => new Date(String(x))) : [];
                }
            };
            return actions[type] ? actions[type]() : value;
        },
        loadParams() {
            let paramKeys = Object.keys(this.params);
            for (let i in paramKeys) {
                if (paramKeys[i] != "redirect_back") {
                    this.$set(this.form, paramKeys[i], this.params[paramKeys[i]]);
                }
            }
        },
        previousStep() {
            this.loading_wizard_previous = true;
            this.wizardTransitionName = "fade";
            this.wizard_step--;
            this.loading_wizard_previous = false;
            setTimeout(() => {
                this.wizardTransitionName = "";
            }, 500);
        },
        nextStep() {
            this.loading_wizard_next = true;
            if (this.wizard_step == this.data.fields.length - 1) {
                return this.submit();
            }
            let page_type = this.form.id ? "edit" : "create";
            this.$http
                .post(`${this.data.store_route}-wizard-step-validation`, {
                    ...this.form,
                    wizard_step: this.wizard_step,
                    page_type
                })
                .then(() => {
                    this.wizardTransitionName = "fade";
                    this.wizard_step++;
                    this.loading_wizard_next = false;
                    setTimeout(() => {
                        this.wizardTransitionName = "";
                    }, 500);
                })
                .catch(er => {
                    this.makeFormValidationErrors(er);
                    this.loading_wizard_next = false;
                });
        },
        makeFormValidationErrors(er) {
            let errors = er.response.data.errors;
            this.errors = errors;
            try {
                let message = Object.keys(errors)
                    .map(key => `<li>${errors[key][0]}</li>`)
                    .join("");
                this.$message({
                    dangerouslyUseHTMLString: true,
                    showClose: true,
                    message: `<ul>${message}</ul>`,
                    type: "error"
                });
            } catch {
                return;
            }
        },
        submit(clicked_btn = "save_and_back") {
            this.$confirm(`Confirma ${this.data.page_type} ?`, "Confirmação", {
                confirmButtonText: "Sim",
                cancelButtonText: "Não",
                type: "warning"
            }).then(() => {
                let loading = this.$loading({ text: "Salvando ..." });
                this.$http
                    .post(this.data.store_route, { ...this.form, clicked_btn })
                    .then(({ data }) => {
                        if (data.success) {
                            return (window.location.href = data.route);
                        } else {
                            if (data.message) {
                                this.$message({ showClose: true, message: data.message.text, type: data.message.type });
                            }
                            loading.close();
                        }
                    })
                    .catch(er => {
                        this.makeFormValidationErrors(er);
                        loading.close();
                    });
            });
        }
    }
};
</script>
<style lang="scss" scoped>
#crud-view {
    .fields-tab {
        .nav-link {
            font-size: 12px;
        }
    }
    .f-12 {
        font-size: 12px;
    }

    .el-button {
        flex-direction: row;
        display: flex;
    }

    .step-resource-crud {
        .resource-step {
            opacity: 0.8;
            &.current {
                cursor: default;
                opacity: 1;
            }
            &.blocked {
                cursor: no-drop;
            }
            &.done {
                cursor: pointer;
                &:hover {
                    transition: 0.4s;
                    opacity: 1;
                }
            }
        }
    }
}
</style>
