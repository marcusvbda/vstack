<template>
    <div class="row" id="crud-view">
        <div class="col-12">
            <form class="needs-validation m-0" novalidate v-on:submit.prevent="submit" @keypress.13.prevent>
                <div class="row">
                    <template v-if="dialog">
                        <div class="col-12">
                            <template v-for="(card, i) in data.fields">
                                <v-runtime-template :key="i" :template="card.view" :id="`${card.label}`" />
                            </template>
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-md-9 col-sm-12 col-sm-12">
                            <template v-for="(card, i) in data.fields">
                                <v-runtime-template :key="i" :template="card.view" :id="`${card.label}`" />
                            </template>
                        </div>
                        <div class="col-md-3 col-sm-12 fields-tab" data-aos="fade-up">
                            <div class="row flex-column" :style="{ top: 10, position: 'sticky' }">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body d-none d-md-block d-lg-block" v-if="showPills">
                                            <div class="row" v-if="!right_card_content">
                                                <div class="col-12">
                                                    <ul class="d-flex flex-column mb-0 pl-3">
                                                        <li v-for="(card, i) in namedCards" :key="i">
                                                            <a class="f-12 link" :href="`#${card.label}`" v-html="card.label" />
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <v-runtime-template v-else :template="right_card_content" />
                                        </div>
                                        <div class="card-footer flex-wrap d-flex flex-row justify-content-end p-2 align-items-center">
                                            <el-button-group>
                                                <el-button v-if="first_btn" :size="first_btn.size" :type="first_btn.type" @click="submit(first_btn.field)">
                                                    <span v-html="first_btn.content" />
                                                </el-button>
                                                <el-button v-if="second_btn" :size="second_btn.size" :type="second_btn.type" @click="submit(second_btn.field)">
                                                    <span v-html="second_btn.content" />
                                                </el-button>
                                            </el-button-group>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <slot name="aftercreate" v-if="['CREATE', 'CLONE'].includes(pageType)"></slot>
                <slot name="afteredit" v-if="['EDIT'].includes(pageType)"></slot>
            </form>
        </div>
    </div>
</template>
<script>
import VRuntimeTemplate from 'v-runtime-template'
export default {
    props: ['data', 'redirect', 'params', 'raw_type', 'acl', 'first_btn', 'second_btn', 'dialog', 'right_card_content', 'content'],
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
            return this.namedCards.length > 1 || this.right_card_content
        },
        pageType() {
            return this.raw_type.toUpperCase()
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
            if (this.data.id && this.pageType == 'EDIT') {
                this.$set(this.form, 'id', this.data.id)
            }
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
                case 'daterange':
                    if (Array.isArray(value)) return value.map((x) => new Date(String(x)))
                    return value ? value.split(',').map((x) => new Date(String(x))) : []
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
        submit(clicked_btn = 'save_and_back') {
            this.$confirm(`Confirma ${this.data.page_type} ?`, 'Confirmação', {
                confirmButtonText: 'Sim',
                cancelButtonText: 'Não',
                type: 'warning',
            }).then(() => {
                let loading = this.$loading({ text: 'Salvado ...' })
                this.$http
                    .post(this.data.store_route, { ...this.form, clicked_btn })
                    .then(({ data }) => {
                        if (data.success) {
                            return (window.location.href = data.route)
                        } else {
                            if (data.message) {
                                this.$message({ showClose: true, message: data.message.text, type: data.message.type })
                            }
                            loading.close()
                        }
                    })
                    .catch((er) => {
                        let errors = er.response.data.errors
                        this.errors = errors
                        loading.close()
                        try {
                            let message = Object.keys(errors)
                                .map((key) => `<li>${errors[key][0]}</li>`)
                                .join('')
                            this.$message({ dangerouslyUseHTMLString: true, showClose: true, message: `<ul>${message}</ul>`, type: 'error' })
                        } catch {
                            return
                        }
                    })
            })
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
