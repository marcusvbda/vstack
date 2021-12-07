<template>
    <div class="row mb-3">
        <transition name="fade">
            <div class="col-12 d-flex flex-row flex-wrap align-items-center" v-if="!loading">
                <ElTag
                    :style="{ '--color': t.color }"
                    class="resource-tag"
                    size="mini"
                    :closable="only_view == undefined"
                    v-for="(t, i) in tags"
                    :key="t.id"
                    :hit="true"
                    @close="handleClose(t, i)"
                >
                    {{ t.name }}
                </ElTag>
                <button class="ml-2 btn-new-tag" title="Nova Tag" @click="dialogVisible = true" v-if="only_view == undefined">
                    <span class="el-icon-price-tag" />
                </button>
                <ElDialog title="Adicionar Tags" :visible.sync="dialogVisible" width="30%">
                    <div class="row">
                        <div class="col-12">
                            <ElSelect
                                class="w-100"
                                v-model="newTag"
                                filterable
                                allow-create
                                default-first-option
                                placeholder="Selecione ou Digite a tag que deseja adicionar"
                            >
                                <ElOption v-for="t in filteredOption" :key="t.id" :label="t.name" :value="t.name">
                                    <ElTag
                                        :style="{ borderColor: t.color, color: t.color, fontWeight: 700 }"
                                        class="resource-tag"
                                        size="mini"
                                        :key="t.id"
                                        color="transparent"
                                        :hit="true"
                                    >
                                        {{ t.name }}
                                    </ElTag>
                                </ElOption>
                            </ElSelect>
                        </div>
                    </div>
                    <span slot="footer" class="dialog-footer" v-if="canSave">
                        <ElButton @click="dialogVisible = false">
                            Cancelar
                        </ElButton>
                        <ElButton type="primary" @click="addTag" v-loading="submiting" :disabled="submiting">
                            Adicionar
                        </ElButton>
                    </span>
                </ElDialog>
            </div>
        </transition>
    </div>
</template>

<script>
export default {
    props: ["resource", "resource_code", "only_view", "default_tags", "default_options"],
    data() {
        return {
            dialogVisible: false,
            newTag: "",
            attempts: 0,
            attempt_options: 0,
            loading: true,
            loading_options: true,
            tags: [],
            options: [],
            submiting: false
        };
    },
    computed: {
        filteredOption() {
            return this.options.filter(x => !this.tags.map(x => x.id).includes(x.id));
        },
        canSave() {
            if (!this.newTag) return false;
            if (this.tags.filter(x => x.name == this.newTag).length > 0) return false;
            return true;
        }
    },
    created() {
        if (Array.isArray(this.default_tags)) {
            this.tags = this.default_tags;
            this.loading_options = false;
            this.loading = false;
        } else {
            if (this.default_tags === null) {
                this.tags = [];
                this.loading_options = false;
                this.loading = false;
            } else {
                this.getTags();
            }
        }
        if (this.only_view == undefined) {
            if (Array.isArray(this.default_options)) {
                this.options = this.default_options;
                this.loading_options = false;
            } else {
                this.getOptions();
            }
        }
    },
    methods: {
        handleClose(tag, index) {
            this.$confirm("Remover tag ?", "Confirmação", {
                confirmButtonText: "Sim",
                cancelButtonText: "Não",
                type: "warning"
            }).then(() => {
                this.$http.delete(`/admin/${this.resource}/${this.resource_code}/tags/destroy/${tag.id}`).then(() => {
                    this.tags.splice(index, 1);
                });
            });
        },
        getTags() {
            this.attempts++;
            this.$http.get(`/admin/${this.resource}/${this.resource_code}/tags`, {}, { retries: 3 }).then(({ data }) => {
                this.tags = data;
                this.loading = false;
            });
        },
        getOptions() {
            this.attempt_options++;
            this.$http.get(`/admin/${this.resource}/tags/options`, {}, { retries: 3 }).then(({ data }) => {
                this.options = data;
                this.loading_options = false;
            });
        },
        addTag() {
            this.submiting = true;
            this.$http
                .post(`/admin/${this.resource}/${this.resource_code}/tags/add`, {
                    name: this.newTag
                })
                .then(resp => {
                    resp = resp.data;
                    this.tags.push(resp);
                    this.loading = false;
                    this.dialogVisible = false;
                    this.$nextTick(() => {
                        this.newTag = "";
                        this.submiting = false;
                    });
                })
                .catch(er => {
                    this.loading = false;
                    console.log(er);
                });
        }
    }
};
</script>
<style lang="scss" scoped>
.btn-new-tag {
    cursor: pointer;
    border: 1px solid #adadad;
    color: #adadad;
    border-radius: 100%;
    height: 30px;
    width: 30px;
    opacity: 0.8;
    transition: 0.4s;
    &:hover {
        opacity: 1;
    }
    &:focus {
        outline: unset;
    }
}
.resource-tag {
    border-color: var(--color);
    background-color: var(--color);
    color: white;
    font-weight: 700;
}
</style>
