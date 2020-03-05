<template>
    <div>
        <div class="row mb-3 mt-2">
            <div class="col-12 d-flex flex-row align-items-center">
                <h4 class="mb-1" v-html="rendered_data.index_label" />
                <div class="d-flex flex-row flex-wrap align-items-center">
                    <template v-if="rendered_data.can_create">
                        <template v-if="rendered_data.model_count>0">
                            <button
                                type="button"
                                class="btn btn-primary btn-sm btn-sm-block cursor-pointer px-3 pr-2 mx-4 mb-1"
                                @click="openModal('create')"
                                v-html="rendered_data.store_button_label"
                            ></button>
                        </template>
                    </template>
                </div>
            </div>
        </div>
        <template v-if="rendered_data.model_count>0">
            <div class="row">
                <div class="col-12">
                    <crud-table
                        :rendered_data="rendered_data"
                        @onEdit="openModal('edit')"
                        :form="form"
                        :resourceData="resourceData"
                    />
                </div>
            </div>
        </template>
        <template v-else>
            <div class="d-flex flex-column row align-items-center justify-items-center">
                <div class="col-md-6 col-sm-12 text-center">
                    <h4 class="text-center mt-5">
                        <template v-if="rendered_data.icon">
                            <h1 style="opacity: .3;font-size: 250px;">
                                <span :class="rendered_data.icon"></span>
                            </h1>
                        </template>
                        <div v-html="rendered_data.nothing_stored_text"></div>
                        <small
                            style="font-size: 15px;"
                            v-html="rendered_data.nothing_stored_subtext"
                        ></small>
                    </h4>
                    <template v-if="rendered_data.can_create">
                        <button
                            type="button"
                            class="btn btn-primary btn-sm-block cursor-pointer mb-3 mt-3"
                            @click="openModal('create')"
                            v-html="rendered_data.store_button_label"
                        ></button>
                    </template>
                </div>
            </div>
        </template>
        <crud-modal
            ref="modal"
            :rendered_data="rendered_data"
            :form="form"
            :errors="errors"
            @onSubmit="loadView"
            @onDestroy="loadView"
            :resourceData="resourceData"
        />
    </div>
</template>
<script>
export default {
    props: ["resource", "params", "breadcrumb"],
    data() {
        return {
            attempts: 0,
            resourceRoute: null,
            resourceName: null,
            resourceData: {},
            form: {
                id: null,
                resource_id: null
            },
            errors: {},
            rendered_data: {},
        }
    },
    components: {
        "crud-modal": require("./partials/-ResourceFieldModal.vue").default,
        "crud-table": require("./partials/-ResourceCrudTable.vue").default,
    },
    async created() {
        this.loadView()
    },
    methods: {
        getRedirectUrl() {
            let url = window.location.href
            return url.substring(url.indexOf("/admin/"), url.length).replace("/admin/", "")
        },
        loadView() {
            this.attempts++
            this.resourceName = this.resource.charAt(0).toLowerCase() + this.resource.slice(1)
            this.resourceName = this.resourceName.replace(/([A-Z])/g, " $1").split(' ').join('-').toLowerCase()
            this.resourceRoute = laravel.vstack.resource_field_route.replace("%%resource%%", this.resourceName)
            let params = this.params
            this.$http.post(this.resourceRoute, params).then(res => {
                this.rendered_data = res.data
                this.loadParameters()
            }).catch(er => {
                console.log(er)
                if (this.attempts > 5) return console.log("timeout")
                this.loadView()
            })
        },
        loadParameters() {
            for (let i in this.rendered_data.params) {
                this.$set(this.form, i, this.rendered_data.params[i])
            }
        },
        cleanForm() {
            let param_indexes = Object.keys(this.rendered_data.params)
            for (let i in this.rendered_data.crud_fields) {
                if (!param_indexes.includes(this.rendered_data.crud_fields[i].options.field))
                    this.$set(this.form, this.rendered_data.crud_fields[i].options.field, this.rendered_data.crud_fields[i].options.default)
            }
            this.$set(this.form, "id", undefined)
        },
        openModal(type) {
            switch (type) {
                case "create":
                    this.cleanForm()
                    this.$refs.modal.show()
                    break
                case "edit":
                    this.$refs.modal.show()
                    break
                default:
                    return console.log("404 page not exist")
                    break
            }
        }
    }
}
</script>