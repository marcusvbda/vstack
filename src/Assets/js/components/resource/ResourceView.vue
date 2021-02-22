<template>
    <div id="crud-view">
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <slot name="title"></slot>
                </div>
                <resource-buttons-view
                    :can_delete="data.can_delete"
                    :can_update="data.can_update"
                    :update_route="getRoute('update_route')"
                    :route_destroy="getRoute('route_destroy')"
                    :breadcrumb="breadcrumb"
                />
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-9 col-sm-12">
                <div class="row" v-for="(card, i) in data.fields" :key="i">
                    <div class="col-12">
                        <div class="card mb-3" :id="`${card.label}`">
                            <div v-if="card.label" class="card-header crud-card-header">
                                <b class="crud-title" v-html="card.label"></b>
                            </div>
                            <div class="card-body p-0">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-crud mb-0">
                                            <tbody>
                                                <tr v-for="(field, i) in card.inputs" :key="i">
                                                    <td class="w-25" v-if="i.indexOf('IGNORE__') < 0">
                                                        <b class="input-title" v-html="i"></b>
                                                    </td>
                                                    <td style="max-width: 0px">
                                                        <v-runtime-template
                                                            :key="i"
                                                            :template="`<div style='overflow-x: auto;'>${field === null ? '' : field}</div>`"
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import VRuntimeTemplate from 'v-runtime-template'
export default {
    props: ['data', 'breadcrumb', 'form'],
    data() {
        return {
            loading: false,
            resourceData: {},
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
    methods: {
        getRoute(route) {
            return this.data[route]
        },
    },
}
</script>
