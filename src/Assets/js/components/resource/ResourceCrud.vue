<template>
    <div class="row" id="crud-view">
        <div class="col-12">
            <form class="needs-validation m-0" novalidate v-on:submit.prevent="submit" @keypress.13.prevent>
                <div class="row">
                    <div class="col-md-9 col-sm-12 col-sm-12">
                        <template v-for="(card, i) in data.fields">
                            <v-runtime-template :key="i" :template="card.view" :id="`${card.label}`" />
                        </template>
                    </div>
                    <div class="col-md-3 col-sm-12 fields-tab">
                        <div class="row flex-column" :style="{ top: 10, position: 'sticky' }">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body d-none d-md-block d-lg-block" v-if="showPills">
                                        <div class="row">
                                            <div class="col-12">
                                                <ul class="d-flex flex-column mb-0 pl-3">
                                                    <li v-for="(card, i) in namedCards" :key="i">
                                                        <a class="f-12 link" :href="`#${card.label}`" v-html="card.label" />
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer flex-wrap d-flex flex-row justify-content-between p-2 align-items-center">
                                        <a :href="data.list_route" class="mr-5 link d-none d-md-block d-lg-block f-12">
                                            <span class="d-flex align-items-center"> <span class="el-icon-back mr-2" />Voltar </span>
                                        </a>
                                        <button class="btn btn-primary btn-sm btn-sm-block f-12 d-flex align-items-center" type="sumit">
                                            <span class="el-icon-success mr-2" />Salvar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <slot name="aftercreate" v-if="pageType == 'CREATE'"></slot>
                <slot name="afteredit" v-if="pageType != 'CREATE'"></slot>
            </form>
        </div>
    </div>
</template>
<script>
import VRuntimeTemplate from 'v-runtime-template'
export default {
    props: ['data', 'redirect', 'params', 'breadcrumb'],
    data() {
        return {
            resourceData: {},
            form: {},
            errors: {},
        }
    },
    components: {
        'v-runtime-template': VRuntimeTemplate,
    },
    computed: {
        showPills() {
            return this.namedCards.length > 1
        },
        pageType() {
            return this.data.id ? 'EDIT' : 'CREATE'
        },
        namedCards() {
            return this.data.fields.filter((x) => x.label)
        },
    },
    async created() {
        this.initForm()
    },
    methods: {
        initForm() {
            this.initFields()
            this.loadParams()
            this.$set(this.form, 'resource_id', this.data.resource_id)
            if (this.data.id) this.$set(this.form, 'id', this.data.id)
        },
        initFields() {
            let fields = []
            for (let i in this.data.fields) {
                let card = this.data.fields[i]
                for (let y in card.inputs) {
                    fields.push(card.inputs[y])
                }
            }
            for (let i in fields) {
                if (fields[i].options) {
                    let field_name = fields[i].options.field
                    let field_value = this.processFieldValue(field_name, fields[i].options)
                    if (field_name) {
                        this.$set(fields[i].options.type == 'resource-field' ? this.resourceData : this.form, field_name, field_value)
                    }
                }
            }
        },
        processFieldValue(name, options) {
            let value = null
            if (!['null', ''].includes(String(options.value))) {
                value = this.processFieldPerType(options.type, typeof options.value != 'object' ? String(options.value) : options.value)
            } else {
                value = this.processFieldPerType(options.type, options.default)
            }
            return value
        },
        processFieldPerType(type, value) {
            switch (type) {
                case 'tags':
                    if (Array.isArray(value)) return value.map((x) => String(x))
                    return value ? value.split(',') : []
                    break
                case 'check':
                    return String(value) == 'true'
                    break
                case 'upload':
                    return value ? (Array.isArray(value) ? value.map((x) => String(x)) : value.split(',')) : []
                    break
                default:
                    return value
                    break
            }
            return value
        },
        loadParams() {
            let paramKeys = Object.keys(this.params)
            for (let i in paramKeys) if (paramKeys[i] != 'redirect_back') this.$set(this.form, paramKeys[i], this.params[paramKeys[i]])
        },
        submit() {
            this.$confirm(`Confirma ${this.data.page_type} ?`, 'Confirmação', {
                confirmButtonText: 'Sim',
                cancelButtonText: 'Não',
                type: 'warning',
            })
                .then(() => {
                    let loading = this.$loading()
                    this.$http
                        .post(this.data.store_route, this.form)
                        .then((res) => {
                            let data = res.data
                            if (data.message) this.$message({ showClose: true, message: data.message.text, type: data.message.type })
                            if (data.success) return (window.location.href = data.route)
                            loading.close()
                        })
                        .catch((er) => {
                            let errors = er.response.data.errors
                            this.errors = errors
                            loading.close()
                        })
                })
                .catch(() => false)
        },
    },
}
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
}
</style>
