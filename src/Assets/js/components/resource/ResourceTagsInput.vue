<template>
    <div class="row mb-3">
        <transition name="fade">
            <div class="col-12 d-flex flex-row flex-wrap align-items-center" v-if="!loading">
                <el-tag
                    :style="{ '--color': t.color }"
                    class="resource-tag"
                    size="mini"
                    :closable="only_view == undefined"
                    v-for="(t, i) in tags"
                    :key="t.id"
                    color="transparent"
                    :hit="true"
                    @close="handleClose(t, i)"
                    >{{ t.name }}</el-tag
                >
                <button class="ml-2 btn-new-tag" title="Nova Tag" @click="dialogVisible = true" v-if="only_view == undefined">
                    <span class="el-icon-price-tag" />
                </button>
                <el-dialog title="Adicionar Tags" :visible.sync="dialogVisible" width="30%">
                    <div class="row">
                        <div class="col-12">
                            <el-select
                                class="w-100"
                                v-model="newTag"
                                filterable
                                allow-create
                                default-first-option
                                placeholder="Selecione ou Digite a tag que deseja adicionar"
                            >
                                <el-option v-for="t in filteredOption" :key="t.id" :label="t.name" :value="t.name">
                                    <el-tag
                                        :style="{ borderColor: t.color, color: t.color, fontWeight: 700 }"
                                        class="resource-tag"
                                        size="mini"
                                        :key="t.id"
                                        color="transparent"
                                        :hit="true"
                                        >{{ t.name }}</el-tag
                                    >
                                </el-option>
                            </el-select>
                        </div>
                    </div>
                    <span slot="footer" class="dialog-footer" v-if="canSave">
                        <el-button @click="dialogVisible = false">Cancelar</el-button>
                        <el-button type="primary" @click="addTag" v-loading="submiting" :disabled="submiting">Adicionar</el-button>
                    </span>
                </el-dialog>
            </div>
        </transition>
    </div>
</template>

<script>
export default {
    props: ['resource', 'resource_code', 'only_view'],
    data() {
        return {
            dialogVisible: false,
            newTag: '',
            attempts: 0,
            attempt_options: 0,
            loading: true,
            loading_options: true,
            tags: [],
            options: [],
            submiting: false,
        }
    },
    computed: {
        filteredOption() {
            return this.options.filter((x) => !this.tags.map((x) => x.id).includes(x.id))
        },
        canSave() {
            if (!this.newTag) return false
            if (this.tags.filter((x) => x.name == this.newTag).length > 0) return false
            return true
        },
    },
    created() {
        this.getTags()
        if (this.only_view == undefined) return this.getOptions()
    },
    methods: {
        addTag() {
            this.dialogVisible = false
            this.$nextTick(() => (this.newTag = ''))
        },
        handleClose(tag, index) {
            this.$confirm('Remover tag ?', 'Confirmação', {
                confirmButtonText: 'Sim',
                cancelButtonText: 'Não',
                type: 'warning',
            }).then(() => {
                this.$http.delete(`/admin/${this.resource}/${this.resource_code}/tags/destroy/${tag.id}`).then(() => {
                    this.tags.splice(index, 1)
                })
            })
        },
        getTags() {
            this.attempts++
            this.$http
                .get(`/admin/${this.resource}/${this.resource_code}/tags`)
                .then((resp) => {
                    resp = resp.data
                    this.tags = resp
                    this.loading = false
                })
                .catch((er) => {
                    if (this.attempts <= 3) return this.getTags()
                    this.loading = false
                    console.log(er)
                })
        },
        getOptions() {
            this.attempt_options++
            this.$http
                .get(`/admin/${this.resource}/tags/options`)
                .then((resp) => {
                    resp = resp.data
                    this.options = resp
                    this.loading_options = false
                })
                .catch((er) => {
                    if (this.attempt_options <= 3) return this.getTags()
                    this.loading_options = false
                    console.log(er)
                })
        },
        addTag() {
            this.submiting = true
            this.$http
                .post(`/admin/${this.resource}/${this.resource_code}/tags/add`, {
                    name: this.newTag,
                })
                .then((resp) => {
                    resp = resp.data
                    this.tags.push(resp)
                    this.loading = false
                    this.dialogVisible = false
                    this.$nextTick(() => {
                        this.newTag = ''
                        this.submiting = false
                    })
                })
                .catch((er) => {
                    this.loading = false
                    console.log(er)
                })
        },
    },
}
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
    color: var(--color);
    font-weight: 700;
}
</style>
