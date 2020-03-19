<template>
    <div class="row">
        <div class="col-12">
            <form
                class="needs-validation m-0"
                novalidate
                v-on:submit.prevent="submit"
                @keypress.13.prevent
            >
                <div class="row">
                    <div class="col-md-2 pr-0 d-none d-md-block" v-if="showPills">
                        <ul
                            class="nav nav-pills nav-fill flex-column"
                            :style="{top: 10,position: 'sticky'}"
                        >
                            <li class="nav-item mb-2" v-for="(card,i) in namedCards" :key="i">
                                <a
                                    class="nav-link active"
                                    :href="`#${card.label}`"
                                    v-html="card.label"
                                ></a>
                            </li>
                        </ul>
                    </div>
                    <div v-bind:class="{'col-md-10 col-sm-12' : showPills,'col-12' : !showPills}">
                        <template v-for="(card,i) in data.fields">
                            <v-runtime-template
                                :key="i"
                                :template="card.view"
                                :id="`${card.label}`"
                            />
                        </template>
                    </div>
                </div>
                <div class="row">
                    <div
                        class="col-12 d-flex justify-content-end d-flex align-items-center flex-wrap"
                    >
                        <a :href="data.list_route" class="mr-5 text-danger link d-none d-lg-block">
                            <b>Cancelar</b>
                        </a>
                        <button
                            class="btn btn-primary btn-sm-block"
                            type="sumit"
                        >{{pageType=='CREATE' ? 'Cadastrar' : 'Alterar'}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template"
export default {
    props: ["data", "redirect", "params", "breadcrumb"],
    data() {
        return {
            resourceData: {},
            form: {},
            errors: {},
        }
    },
    components: {
        "v-runtime-template": VRuntimeTemplate
    },
    computed: {
        showPills() {
            return this.namedCards.length > 1
        },
        pageType() {
            return this.data.id ? "EDIT" : "CREATE"
        },
        namedCards() {
            return this.data.fields.filter(x => x.label)
        }
    },
    async created() {
        this.initForm()
    },
    methods: {
        getRedirect() {
            try {
                let keys = Object.keys(this.breadcrumb)
                let key = keys[keys.length - 2]
                return this.breadcrumb[key]
            } catch{
                let keys = Object.keys(this.breadcrumb)
                let key = keys[0]
                return this.breadcrumb[key]
            }
        },
        initForm() {
            this.initFields()
            this.loadParams()
            this.$set(this.form, "resource_id", this.data.resource_id)
            if (this.data.id) this.$set(this.form, "id", this.data.id)
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
                let field_name = fields[i].options.field
                let field_value = this.processFieldValue(field_name, fields[i].options)
                this.$set(fields[i].options.type == "resource-field" ? this.resourceData : this.form, field_name, field_value)
            }
        },
        processFieldValue(name, options) {
            let value = null
            if (!["null", ""].includes(String(options.value))) {
                value = this.processFieldPerType(options.type, String(options.value))
            } else {
                value = this.processFieldPerType(options.type, options.default)
            }
            return value
        },
        processFieldPerType(type, value) {
            switch (type) {
                case "tags":
                    if (Array.isArray(value)) return value.map(x => String(x))
                    return value ? value.split(",") : []
                    break
                case "check":
                    return String(value) == "true"
                    break
                case "upload":
                    return value ? (Array.isArray(value) ? value.map(x => String(x)) : value.split(",")) : []
                    break
                default:
                    return value
                    break
            }
            return value
        },
        loadParams() {
            let paramKeys = Object.keys(this.params)
            for (let i in paramKeys) if (paramKeys[i] != "redirect_back") this.$set(this.form, paramKeys[i], this.params[paramKeys[i]])
        },
        submit() {
            this.$confirm(`Confirma ${this.data.page_type} ?`, "Confirmação", {
                confirmButtonText: "Sim",
                cancelButtonText: "Não",
                type: 'warning'
            }).then(() => {
                let loading = this.$loading()
                this.$http.post(this.data.store_route, this.form).then(res => {
                    let data = res.data
                    if (data.message) this.$message({ showClose: true, message: data.message.text, type: data.message.type })
                    if (data.success) return window.location.href = this.getRedirect()
                    loading.close()
                }).catch(er => {
                    let errors = er.response.data.errors
                    this.errors = errors
                    loading.close()
                })
            }).catch(() => false)
        }
    }
}
</script>