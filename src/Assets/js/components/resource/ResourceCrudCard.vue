<template>
    <div :id="label" :index="index">
        <div class="card crud-card mb-4 p-0">
            <CrudCardHeader :label="label" :description="description">
                <div class="d-flex justify-content-end" v-if="advanced">
                    <el-checkbox v-model="showing" class="mb-1">Visualizar Informações</el-checkbox>
                </div>
            </CrudCardHeader>
            <div class="card-body p-0">
                <div class="row" v-if="real_showing">
                    <div class="col-12">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["label", "description", "advanced", "inputs", "index"],
    data: () => ({
        showing: false
    }),
    computed: {
        real_showing() {
            if (!this.advanced) return true;
            return this.showing;
        }
    }
};
</script>
<style lang="scss">
.crud-card {
    .card-header {
        &.crud-card-header {
            background-color: white;
            border-bottom: unset;
        }

    }

    .label-section {

        .card-title {
            color: #212529;
            margin-top: 8px;
            font-style: normal;
            font-weight: 700;
            font-size: 16px;
            letter-spacing: -0.02em;
            line-height: 148%;

            &.header-title {
                font-size: 15px;
                color: black;
            }
        }

        .card-description {
            font-style: normal;
            font-weight: 500;
            font-size: 14px;
            letter-spacing: -0.01em;
            color: #495057;
            margin-bottom: 16px;
            line-height: 144%;
        }
    }

    .table-crud {
        tbody {
            td.w-25 {
                width: 35% !important;

                .input-title {
                    font-family: 'Montserrat';
                    font-style: normal;
                    font-weight: 700;
                    line-height: 148%;
                    letter-spacing: -0.02em;
                    color: #495057;
                    margin-bottom: 4px;

                    small.text-muted {
                        position: unset !important;
                        top: unset !important;
                    }
                }

                small.text-muted {
                    font-family: 'Montserrat';
                    font-style: normal;
                    font-weight: 500;
                    font-size: 12px;
                    line-height: 144%;
                    letter-spacing: -0.01em;
                    color: #495057 !important;
                    opacity: 0.64;
                }
            }
        }
    }
}
</style>