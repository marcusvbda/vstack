<template>
    <div class="p-4" v-if="has_ids">
        <el-dropdown trigger="click" size="small" @command="handleCommand">
            <el-button type="primary" class="vstack-btn primary">
                Ações<i class="el-icon-arrow-down el-icon--right ml-2"></i>
            </el-button>
            <el-dropdown-menu slot="dropdown">
                <el-dropdown-item
                    v-for="(action, i) in actions"
                    :key="i"
                    @click.prevent="runAction(action)"
                    :command="i"
                >
                    {{ action.title }}
                </el-dropdown-item>
            </el-dropdown-menu>
        </el-dropdown>

        <el-dialog
            :title="running_action.title"
            :visible.sync="running_action.visible"
            width="85%"
        >
            <template v-if="loading">
                <div class="flex">
                    <div class="w-full mb-2">
                        <div
                            class="shimmer mb-3"
                            :style="{ height: 50, width: '100%' }"
                        />
                        <div
                            class="shimmer mb-3"
                            :style="{ height: 40, width: '75%' }"
                        />
                        <div
                            class="shimmer mb-3"
                            :style="{ height: 100, width: '80%' }"
                        />
                        <div
                            class="shimmer mb-3"
                            :style="{ height: 20, width: '95%' }"
                        />
                        <div
                            class="shimmer mb-5"
                            :style="{ height: 30, width: '100%' }"
                        />
                        <div
                            class="shimmer ml-auto"
                            :style="{ height: 50, width: '30%' }"
                        />
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="mb-4">{{ running_action.message }}</div>
                <resource-crud
                    :data="running_action.crud_data"
                    :params="$getUrlParams()"
                    :ids="selected_ids"
                    :crud_type="{ template: 'page' }"
                    raw_type="action"
                    :content="{}"
                    :dialog="true"
                    ref="crud"
                />

                <div class="w-100 d-flex justify-content-end">
                    <button
                        v-if="running_action.submit_button"
                        :size="running_action.submit_button.size"
                        :type="running_action.submit_button.type"
                        @click="
                            $refs.crud.submit(
                                running_action.submit_button.field
                            )
                        "
                        :loading="action_btn_loading"
                        class="vstack-btn primary"
                        :disabled="action_btn_loading"
                    >
                        <div class="flex">
                            <span
                                v-html="running_action.submit_button.content"
                            />
                        </div>
                    </button>
                </div>
            </template>
        </el-dialog>
    </div>
</template>
<script>
import VRuntimeTemplate from 'v-runtime-template';
import { mapGetters } from 'vuex';

export default {
    props: ['resource_id', 'ids', 'actions'],
    data() {
        return {
            selected_ids: [],
            running_action: {
                visible: false,
                title: '',
                message: '',
                cards: [],
                submit_button: {},
            },
            loading: true,
        };
    },
    components: {
        'v-runtime-template': VRuntimeTemplate,
    },
    created() {
        this.$nextTick(() => {
            this.initiateAllInput();
            this.initiateEventListener();
        });
    },
    computed: {
        ...mapGetters('resource', ['action_btn_loading']),
        has_ids() {
            return this.selected_ids.length > 0;
        },
    },
    methods: {
        handleCommand(cmd) {
            this.runAction(this.actions[cmd]);
        },
        getContent() {
            let url = window.location.href.split('?')[0];
            this.$http
                .get(`${url}/get-action-content/${this.running_action.id}`)
                .then(({ data }) => {
                    this.running_action.crud_data = data.crud_data;
                    this.running_action.message = data.message;
                    this.running_action.run_btn = data.run_btn;
                    this.running_action.submit_button = data.submit_button;

                    this.loading = false;
                });
        },
        runAction(action) {
            this.running_action.visible = true;
            this.running_action.id = action.id;
            this.running_action.title = action.title;
            this.loading = true;
            this.getContent();
            this.form = {};
        },
        toggleSelectedId(checked_value, id) {
            if (checked_value) this.selected_ids.push(id);
            else this.selected_ids = this.selected_ids.filter((x) => x != id);
        },
        initiateAllInput() {
            this.$waitForEl(`#${this.resource_id}_action_select_all`).then(
                () => {
                    setTimeout(() => {
                        const input = document.querySelector(
                            `#${this.resource_id}_action_select_all`
                        );
                        input.addEventListener('change', (event) => {
                            const checked_value = event.target.checked;
                            this.ids.map((id) => {
                                const checkbox = document.querySelector(
                                    `#${this.resource_id}_action_select_${id}`
                                );
                                checkbox.checked = checked_value;
                                this.toggleSelectedId(checkbox.checked, id);
                            });
                        });
                    }, 500);
                }
            );
        },
        initiateEventListener() {
            this.$waitForEl('.select_action_box').then(() => {
                setTimeout(() => {
                    [...document.querySelectorAll('.select_action_box')].map(
                        (input) => {
                            input.addEventListener('change', (event) => {
                                const checkbox = event.target;
                                const id = Number(
                                    checkbox.id.replace(
                                        `${this.resource_id}_action_select_`,
                                        ''
                                    )
                                );
                                this.toggleSelectedId(checkbox.checked, id);
                            });
                        }
                    );
                }, 500);
            });
        },
    },
};
</script>
