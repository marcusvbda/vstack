<template>
    <el-dialog :title="label" :visible.sync="dialogVisible" width="60%" :close-on-press-escape="false" ref="el" :close-on-click-modal="false">
        <span>{{ description }}</span>
        <template v-if="!visible">
            <div class="flex flex-col justify-center items-center">
                <el-spinner class="mb-4 mt-8" />
            </div>
        </template>
        <div class="flex flex-col justify-end items-end mt-4" v-else>
            <el-dropdown trigger="click" :hide-on-click="false" v-if="Object.keys(groupedFilters).length">
                <el-button type="primary" size="small">
                    Filtra {{ label }}<i class="el-icon-arrow-down el-icon--right" />
                </el-button>
                <el-dropdown-menu slot="dropdown" ref="filterMenu">
                    <div class="py-1 px-2 flex flex-col" v-for="(fs, group) in groupedFilters" :key="group">
                        <small class="text-gray-300 text-xs px-4 mb-2">{{ group }}</small>
                        <div
                            @click="toggleOption(f)"
                            v-for="(f, y) in fs"
                            :key="y"
                            :label="f.value"
                            :disabled="f.disabled"
                            :class="[
                                'py-2 px-5 cursor-pointer hover:bg-gray-100 transition duration-300',
                                'ease-in-out flex items-center',
                                f.disabled ? 'opacity-30 cursor-not-allowed' : '',
                            ]"
                        >
                            <span class="el-icon-none bg-gray-500 mr-3" v-if="f.disabled">
                                <i class="el-icon-minus text-white" />
                            </span>
                            <template v-else>
                                <span class="el-icon-none bg-blue-500 mr-3" v-if="filter[f.index].includes(f.value)">
                                    <i class="el-icon-check text-white" />
                                </span>
                                <span class="el-icon-none mr-3" v-else />
                            </template>
                            <span class="text-xs">
                                {{ f.label }}
                            </span>
                        </div>
                    </div>
                    <div class="flex justify-between px-4 py-2 items-center gap-4">
                        <el-button size="mini" type="primary" class="text-xs" @click="confirmFilter">Confirmar</el-button>
                        <a href="#" class="text-xs" @click="cleanFilter">Limpar</a>
                    </div>
                </el-dropdown-menu>
            </el-dropdown>
            <el-select
                class="w-full mt-3"
                v-model="value"
                :multiple="multiple !== undefined"
                filterable
                remote
                reserve-keyword
                placeholder="Digite para buscar"
                :remote-method="remoteMethod"
                :loading="loading"
            >
                <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value">
                    <slot name="option" :item="item" />
                </el-option>
            </el-select>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button @click="dialogVisible = false">{{ cancelBtnText }}</el-button>
            <el-button type="primary" @click="confirm">{{ confirmBtnText }}</el-button>
        </span>
    </el-dialog>
</template>
<script>
export default {
    props: {
        label: {
            type: String,
            default: 'Seletor',
        },
        description: {
            type: String,
            default: 'Selecione um item',
        },
        filters: {
            type: Array,
            default: () => [],
        },
        model: {
            type: String,
            default: '',
        },
        multiple: {
            type: Boolean,
            default: undefined,
        },
        confirmBtnText: {
            type: String,
            default: 'Confirmar',
        },
        cancelBtnText: {
            type: String,
            default: 'Cancelar',
        },
    },
    data() {
        return {
            dialogVisible: false,
            filter: {},
            visible: true,
            value: null,
            loading: true,
            options: [],
        };
    },
    computed: {
        groupedFilters() {
            return (this.filters || []).reduce((acc, filter) => {
                if (!acc[filter.group]) {
                    acc[filter.group] = [];
                }
                acc[filter.group].push(filter);
                return acc;
            }, {});
        },
    },
    created() {
        this.init();
    },
    methods: {
        init() {
            this.filter = {};
            this.value = '';
            this.loading = true;
            this.options = [];
            if (this.multiple !== undefined) {
                this.value = [];
            }
            this.initFilter();
        },
        remoteMethod(val) {
            this.loading = true;
            if (!val) return;
            
            const payload = {
                model: this.model,
                filters: {
                    where: [["name", "like", `%${val}%`]],
                },
            };

            if (Object.keys(this.groupedFilters).length > 0) {
                let whereIn = Object.keys(this.filter).map((f) => [f, this.filter[f]]);
                whereIn = whereIn.filter((x) => x[1].length > 0);
                if (payload.filters.where_in) {
                    payload.filters.where_in = [...payload.filters.where_in, ...whereIn];
                } else {
                    payload.filters.where_in = whereIn;
                }
            }

            this.$http.post(`/vstack/json-api`, payload).then(({ data }) => {
                this.options = data.map((item) => ({
                    label: item.name,
                    value: item.id,
                    original: item,
                }));
                this.loading = false;
            });
        },
        toggleOption(option) {
            if (this.filter[option.index].includes(option.value)) {
                this.filter[option.index] = this.filter[option.index].filter((f) => f !== option.value);
            } else {
                this.filter[option.index].push(option.value);
            }
        },
        cleanFilter() {
            this.initFilter();
        },
        confirmFilter() {
            this.visible = false;
            setTimeout(() => (this.visible = true));
            this.remoteMethod();
        },
        initFilter() {
            this.filters.forEach((filter) => {
                if (!this.filter[filter.index]) {
                    this.$set(this.filter, filter.index, []);
                }
                this.filter[filter.index].push(filter.value);
            });
        },
        open() {
            this.init();
            this.dialogVisible = true;
        },
        close() {
            this.dialogVisible = false;
        },
        confirm() {
               this.$emit(
                "on-confirm",
                this.options.filter((x) =>
                    Array.isArray(this.value) ? this.value.includes(x.original.id) : x.original.id === this.value
                )
            );
            this.close();
        },
    },
};
</script>
<style lang="scss" scoped>
.el-icon-none {
    border: 1px solid rgb(84, 84, 84);
    height: 16px;
    border-radius: 3px;
    width: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bolder;
}
</style>
