<template>
    <div class="d-flex justify-content-start py-2" style="padding-left: 13px" v-if="has_ids">
        <div class="dropdown">
            <button
                class="btn btn-primary dropdown-toggle"
                type="button"
                id="dropdownMenuButton"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
            >
                Ações
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#" v-for="(action, i) in actions" :key="i" @click.prevent="runAction(action)">{{
                    action.title
                }}</a>
            </div>
        </div>
        <el-dialog :title="running_action.title" :visible.sync="running_action.visible" width="50%">
            <template v-if="running_action.visible">
                <form v-on:submit.prevent="submit">
                    <div class="mb-4">{{ running_action.message }}</div>
                    <template v-if="running_action.inputs.length > 0">
                        <div class="row" v-for="(input, i) in running_action.inputs" :key="i">
                            <div class="col-12 mb-2">
                                <template v-if="['text', 'number'].includes(input.type)">
                                    <label>{{ input.title }}</label>
                                    <input class="form-control" v-model="form[input.id]" :required="input.required" />
                                </template>
                                <template v-if="['select'].includes(input.type)">
                                    <label>{{ input.title }}</label>
                                    <el-select
                                        class="w-100"
                                        filterable
                                        v-model="form[input.id]"
                                        :required="input.required"
                                        :multiple="input.multiple"
                                        placeholder=""
                                    >
                                        <el-option
                                            v-for="(op, i) in input.options"
                                            :label="op.label"
                                            :value="op.value"
                                            :key="i"
                                            placeholder=""
                                        />
                                    </el-select>
                                    <input
                                        v-model="form[input.id]"
                                        :required="input.required"
                                        style="position: relative; top: -34px; z-index: -1"
                                    />
                                </template>
                                <template v-if="['checkbox'].includes(input.type)">
                                    <el-checkbox class="mt-3" v-model="form[input.id]" :label="input.title" border />
                                </template>
                                <template v-if="['textarea'].includes(input.type)">
                                    <label>{{ input.title }}</label>
                                    <textarea
                                        class="form-control"
                                        :rows="input.rows"
                                        v-model="form[input.id]"
                                        :required="input.required"
                                    />
                                </template>
                                <template v-if="['custom'].includes(input.type)">
                                    <v-runtime-template :template="input.template" :id="i" />
                                </template>
                            </div>
                        </div>
                    </template>
                    <div class="row mt-5">
                        <div class="col-12 d-flex justify-content-end align-items-center">
                            <button class="btn btn-primary sm-btn-block">{{ running_action.run_btn }}</button>
                        </div>
                    </div>
                </form>
            </template>
        </el-dialog>
    </div>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template";
export default {
    props: ["resource_id", "ids", "actions"],
    data() {
        return {
            selected_ids: [],
            running_action: {
                visible: false,
                title: "",
                message: "",
                inputs: [],
            },
            form: {},
        };
    },
    components: {
        "v-runtime-template": VRuntimeTemplate,
    },
    created() {
        this.$nextTick(() => {
            this.initiateAllInput();
            this.initiateEventListener();
        });
    },
    computed: {
        has_ids() {
            return this.selected_ids.length > 0;
        },
    },
    methods: {
        submit() {
            let loading = this.$loading();
            let url = window.location.href.split("?")[0];
            this.$http
                .post(`${url}/action/${this.running_action.id}`, { ids: this.selected_ids, ...this.form })
                .then(({ data }) => {
                    if (data.success) return window.location.reload();
                    else {
                        loading.close();
                        if (data.message) this.$message(data.message);
                    }
                })
                .catch((er) => {
                    console.log(er);
                    loading.close();
                    this.errors = er.response.data.errors;
                    this.$validationErrorMessage(er);
                });
        },
        runAction(action) {
            this.running_action.visible = true;
            this.running_action.id = action.id;
            this.running_action.title = action.title;
            this.running_action.message = action.message;
            this.running_action.inputs = action.inputs;
            this.running_action.run_btn = action.run_btn;
            this.form = {};
        },
        toggleSelectedId(checked_value, id) {
            if (checked_value) this.selected_ids.push(id);
            else this.selected_ids = this.selected_ids.filter((x) => x != id);
        },
        initiateAllInput() {
            this.$waitForEl(`#${this.resource_id}_action_select_all`).then(() => {
                setTimeout(() => {
                    const input = document.querySelector(`#${this.resource_id}_action_select_all`);
                    input.addEventListener("change", (event) => {
                        const checked_value = event.target.checked;
                        this.ids.map((id) => {
                            const checkbox = document.querySelector(`#${this.resource_id}_action_select_${id}`);
                            checkbox.checked = checked_value;
                            this.toggleSelectedId(checkbox.checked, id);
                        });
                    });
                }, 500);
            });
        },
        initiateEventListener() {
            this.$waitForEl(".select_action_box").then(() => {
                setTimeout(() => {
                    const inputs = [...document.querySelectorAll(".select_action_box")].map((input) => {
                        input.addEventListener("change", (event) => {
                            const checkbox = event.target;
                            const id = Number(checkbox.id.replace(`${this.resource_id}_action_select_`, ""));
                            this.toggleSelectedId(checkbox.checked, id);
                        });
                    });
                }, 500);
            });
        },
    },
};
</script>
