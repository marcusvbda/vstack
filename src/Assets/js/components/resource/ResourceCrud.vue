<template>
    <div class="flex flex-col relative" ref="bar">
        <div
            class="view-overflow absolute z-999 w-full h-full"
            v-if="pageType === 'VIEW'"
        />
        <div
            class="vstack-crud-card mb-3 p-3 dark:bg-gray-800 dark:border-none"
            ref="topbar"
            id="save-topbar"
            v-if="(first_btn || second_btn) && pageType !== 'VIEW'"
        >
            <div class="flex justify-end items-center gap-5 flex-wrap">
                <button
                    v-if="first_btn"
                    :size="first_btn.size"
                    :class="`vstack-btn ${first_btn.class}`"
                    @click="submit(first_btn.field)"
                    :loading="action_btn_loading"
                    :disabled="action_btn_loading"
                >
                    <span v-html="first_btn.content" />
                </button>
                <button
                    v-if="second_btn"
                    :size="second_btn.size"
                    :class="`vstack-btn ${second_btn.class}`"
                    @click="submit(second_btn.field)"
                    :loading="action_btn_loading"
                    :disabled="action_btn_loading"
                >
                    <span v-html="second_btn.content" />
                </button>
            </div>
        </div>
        <div class="w-full">
            <form
                class="needs-validation m-0"
                novalidate
                v-on:submit.prevent
                id="resource-crud-page"
                v-if="initialized"
            >
                <template v-for="(card, i) in data.fields">
                    <v-runtime-template
                        :key="i"
                        :template="card.view"
                        :id="`resource-crud-card-${card.label}`"
                    />
                </template>
                <slot
                    name="aftercreate"
                    v-if="['CREATE', 'CLONE'].includes(pageType)"
                />
                <slot name="afteredit" v-if="['EDIT'].includes(pageType)" />
            </form>
        </div>
    </div>
</template>
<script>
import VRuntimeTemplate from 'v-runtime-template';
import { mapGetters, mapMutations } from 'vuex';

export default {
    props: [
        'crud_type',
        'data',
        'redirect',
        'params',
        'raw_type',
        'acl',
        'first_btn',
        'second_btn',
        'content_id',
        'content',
        'ids',
        'has_befores_store',
        'loading_message',
        'resource_id',
        'hash',
    ],
    data() {
        return {
            window_width: null,
            form: {},
            errors: {},
            loading: false,
            submitting: false,
            initialized: false,
        };
    },
    components: {
        'v-runtime-template': VRuntimeTemplate,
    },
    computed: {
        ...mapGetters('resource', ['action_btn_loading']),
        showPills() {
            return this.namedCards.length > 1 || this.right_card_content;
        },
        pageType() {
            return this.raw_type.toUpperCase();
        },
        namedCards() {
            return this.data.fields.filter((x) => x.label);
        },
    },
    created() {
        this.setResourceId(this.resource_id);
        this.removeLoadingEl();
        this.getScreenSize();
        this.initForm();
        this.initCrudSectioEvent();
    },
    methods: {
        ...mapMutations('resource', ['setActionBtnLoading', 'setResourceId']),
        initCrudSectioEvent() {
            this.$nextTick(() => {
                const element = this.$refs.topbar;
                if (!element) return;
                let breakpoint = element.getBoundingClientRect().top;
                window.addEventListener('scroll', () => {
                    if (element) {
                        const mustFix =
                            breakpoint > 0 &&
                            document.body.scrollTop >= breakpoint;
                        if (mustFix) {
                            if (!element.classList.contains('fixed-top')) {
                                element.classList.add('fixed-top');
                            }
                        } else {
                            if (element.classList.contains('fixed-top')) {
                                element.classList.remove('fixed-top');
                            }
                        }
                    }
                });
            });
        },
        removeLoadingEl() {
            const el_name = '#loading-section #cud-loader';
            this.$waitForEl(el_name).then(() => {
                document.querySelector(el_name).remove();
            });
        },
        getScreenSize() {
            this.window_width = window.innerWidth;
        },
        initScreenSizesWatcher() {
            window.onresize = () => {
                this.getScreenSize();
            };
        },
        handleActionBtnLoading(val) {
            this.action_btn_loading = val;
        },
        initForm() {
            this.initFields();
            this.loadParams();
            this.$set(this.form, 'resource_id', this.data.resource_id);
            if (this.data.id && this.pageType == 'EDIT') {
                this.$set(this.form, 'id', this.data.id);
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
                    let field = fields[i];
                    let field_name = field.options.field;
                    let field_type = field.options.type;

                    let field_value = this.processFieldValue(
                        field_name,
                        field.options
                    );
                    if (field_type === 'slider') {
                        field_value = parseInt(field_value);
                    }

                    if (field_name) {
                        if (
                            field_type === 'belongsTo' &&
                            Array.isArray(field_value)
                        ) {
                            field_value = field_value.map((x) => String(x));
                        }

                        this.$set(this.form, field_name, field_value);
                    }
                }
            }

            this.initialized = true;
        },
        processFieldValue(name, options) {
            let value = options.value;
            if (!['null', '', 'undefined'].includes(String(options.value))) {
                let option_value = this.content?.id ? value : options.default;
                if (!['object', 'array'].includes(typeof option_value)) {
                    option_value = String(option_value);
                }
                value = this.processFieldPerType(options.type, option_value);
            } else {
                value = this.content?.id ? options.value : options.default;

                let option_value = value ? value : options.default;
                if (Array.isArray(option_value)) {
                    option_value = option_value.filter((x) => x);
                }
                if (
                    !['object', 'array', 'undefined'].includes(
                        typeof option_value
                    ) &&
                    ![null, false].includes
                ) {
                    option_value = String(option_value);
                }
                value = this.processFieldPerType(
                    options.type,
                    [null, undefined].includes(option_value)
                        ? null
                        : option_value
                );
            }
            return value;
        },
        processFieldPerType(type, value) {
            const actions = {
                tags: () => {
                    if (Array.isArray(value)) {
                        return value.map((x) => String(x));
                    }
                    return value ? value.split(',') : [];
                },
                check: () => String(value) == 'true',
                upload: () =>
                    value
                        ? Array.isArray(value)
                            ? value.map((x) => String(x))
                            : value.split(',')
                        : [],
                daterange: () => {
                    if (Array.isArray(value)) {
                        return value.map((x) => new Date(String(x)));
                    }
                    return value
                        ? value.split(',').map((x) => new Date(String(x)))
                        : [];
                },
            };
            if (actions[type]) {
                return actions[type]();
            }
            return value;
        },
        loadParams() {
            let paramKeys = Object.keys(this.params);
            for (let i in paramKeys) {
                if (paramKeys[i] != 'redirect_back') {
                    this.$set(
                        this.form,
                        paramKeys[i],
                        this.params[paramKeys[i]]
                    );
                }
            }
        },
        makeFormValidationErrors(er) {
            let errors = er?.response?.data?.errors || null;
            if (!errors) {
                this.$message({
                    dangerouslyUseHTMLString: true,
                    showClose: true,
                    message: er?.response?.data?.message || er.message,
                    type: 'error',
                });
                return;
            }
            this.errors = errors;
            try {
                let message = Object.keys(errors)
                    .map((key) => `<li>${errors[key][0]}</li>`)
                    .join('');
                this.$message({
                    dangerouslyUseHTMLString: true,
                    showClose: true,
                    message: `<ul>${message}</ul>`,
                    type: 'error',
                });
            } catch {
                this.$message({
                    dangerouslyUseHTMLString: true,
                    showClose: true,
                    message: 'Ocorreu um erro ao salvar os dados',
                    type: 'error',
                });
            }
        },
        checkStore(clicked_btn) {
            if (!this.has_befores_store) {
                return this.submit(clicked_btn, true);
            }

            this.setActionBtnLoading(true);
            this.$http
                .post(this.data.checkout_route, { ...this.form, clicked_btn })
                .then(({ data }) => {
                    if (data?.success === false) {
                        this.setActionBtnLoading(false);
                        return this.$message.error(data.message);
                    }

                    if (data?.success === true) {
                        return this.submit(clicked_btn, true);
                    }

                    if (data?.confirm) {
                        this.loading_confirm = false;
                        return this.$confirm(
                            data.confirm?.message || 'Confirmar ?',
                            data.confirm?.title || 'Confirmação',
                            {
                                confirmButtonText:
                                    data.confirm?.buttons?.yes || 'Sim',
                                cancelButtonText:
                                    data.confirm?.buttons?.no || 'Não',
                                type: data.confirm?.type || 'warning',
                            }
                        ).then(() => {
                            this.setActionBtnLoading(false);
                            this.submit(clicked_btn, true);
                        });
                    }
                });
        },
        submit(clicked_btn = 'save_and_back', checked = false) {
            if (this.pageType === 'VIEW') return;

            if (!checked && this.raw_type != 'action') {
                return this.checkStore(clicked_btn);
            }
            const loading_text = this.loading_message;
            const loading = this.$loading({
                text: loading_text,
                background: 'white',
            });
            const payload = {
                ...this.form,
                clicked_btn,
                redirect_hash: this.hash,
            };
            if (this.raw_type == 'action') {
                payload.ids = this.ids;
            }
            this.$http
                .post(this.data.store_route, payload)
                .then(({ data }) => {
                    if (this.raw_type == 'action') {
                        if (data.success) return window.location.reload();
                        else {
                            loading.close();
                            if (data.message) {
                                this.$message(data.message);
                            }
                        }
                    } else {
                        if (data.success) {
                            return (window.location.href = data.route);
                        } else {
                            if (data.message) {
                                this.$message({
                                    showClose: true,
                                    message: data.message.text,
                                    type: data.message.type,
                                    dangerouslyUseHTMLString: true,
                                });
                            }
                            loading.close();
                        }
                    }
                })
                .catch((er) => {
                    this.setActionBtnLoading(false);
                    this.makeFormValidationErrors(er);
                    loading.close();
                });
        },
    },
};
</script>
<style lang="scss">
#save-topbar.fixed-top {
    position: fixed;
    top: 0;
    z-index: 999;
    left: 0;
    right: 0;
}
</style>
