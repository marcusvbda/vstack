<template>
    <div>
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <slot name="title"></slot>
                </div>
                <button
                    @click.prevent="destroy"
                    v-if="data.can_delete"
                    class="ml-auto btn btn-danger btn-lg btn-sm-block mb-1"
                >
                    <span class="el-icon-delete text-white"></span>
                </button>
                <a
                    v-if="data.can_update"
                    :href="getRoute('update_route')"
                    class="btn btn-primary btn-lg btn-sm-block mb-1"
                >
                    <span class="el-icon-edit text-white"></span>
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-2 pr-0 d-none d-md-block" v-if="showPills">
                <ul
                    class="nav nav-pills nav-fill flex-column"
                    :style="{top: 10,position: 'sticky'}"
                >
                    <li class="nav-item mb-2" v-for="(card,i) in namedCards" :key="i">
                        <a class="nav-link active" :href="`#${card.label}`" v-html="card.label"></a>
                    </li>
                </ul>
            </div>
            <div v-bind:class="{'col-md-10 col-sm-12' : showPills,'col-12' : !showPills}">
                <div class="row" v-for="(card, i) in data.fields" :key="i">
                    <div class="col-12">
                        <div class="card mb-3" :id="`${card.label}`">
                            <div v-if="card.label" class="card-header">
                                <h5 v-html="card.label"></h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped mb-0">
                                            <tbody>
                                                <tr v-for="(field, i) in card.inputs" :key="i">
                                                    <td
                                                        style="width:25%;"
                                                        v-if="i.indexOf('IGNORE__')<0"
                                                    >
                                                        <span v-html="i"></span>
                                                    </td>
                                                    <td>
                                                        <v-runtime-template
                                                            :key="i"
                                                            :template="`<span>${field===null ? '' : field}</span>`"
                                                        />
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4" v-if="false">
            <div class="col-12">
                <div class="row">
                    <div v-bind:class="{'col-md-10 col-sm-12' : showPills,'col-12' : !showPills}">
                        <div class="row" v-for="(card, i) in data.fields" :key="i">
                            <div class="col-12 p-0">
                                <h4 v-if="card.label" v-html="card.label"></h4>
                                <div class="card">
                                    <div class="card-body p-0">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="table table-striped mb-0">
                                                    <tbody>
                                                        <tr
                                                            v-for="(field, i) in card.inputs"
                                                            :key="i"
                                                        >
                                                            <template
                                                                v-if="!['custom'].includes(field.type)"
                                                            >
                                                                <td
                                                                    style="width:25%;"
                                                                    v-if="i.indexOf('IGNORE__')<0"
                                                                >
                                                                    <span v-html="i"></span>
                                                                </td>
                                                                <td>
                                                                    <v-runtime-template
                                                                        :key="i"
                                                                        :template="`<span>${field===null ? '' : field}</span>`"
                                                                    />
                                                                </td>
                                                            </template>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template"
export default {
    props: ["data", "breadcrumb"],
    data() {
        return {
            loading: false,
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
    methods: {
        getRoute(route) {
            return this.data[route]
        },
        getDestroyRedirect() {
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
        destroy() {
            this.$confirm(`Confirma Exclusão ?`, "Confirmação", {
                confirmButtonText: "Sim",
                cancelButtonText: "Não",
                type: 'error'
            }).then(() => {
                this.loading = this.$loading()
                this.$http.delete(this.data.route_destroy, {}).then(res => {
                    res = res.data
                    return window.location.href = this.getDestroyRedirect()
                }).catch(er => {
                    this.loading.close()
                    this.$message({
                        message: er.response.data.message,
                        type: 'error'
                    })
                })
            }).catch(() => false)
        }
    }
}
</script>