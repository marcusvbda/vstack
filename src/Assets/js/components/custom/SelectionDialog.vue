<template>
    <div data-aos="fade-down" v-if="options.length">
        <slot />
        <el-dialog
            :title="title"
            :visible.sync="showing"
            center
            ref="selection"
            :lock-scroll="true"
            :close-on-click-modal="false"
            :append-to-body="true"
            :automatic-dropdown="true"
        >
            <div class="d-flex flex-column">
                <el-select v-model="value" filterable placeholder="Selecione ..." :multiple="multiple ? multiple : false" :clearable="true">
                    <el-option
                        v-for="item in options"
                        :key="item.value"
                        :value="item[value_index ? value_index : 'id']"
                        :label="item[label_index ? label_index : 'label']"
                    >
                        <slot name="item-label" :item="item" />
                    </el-option>
                </el-select>
            </div>
            <span slot="footer" class="el-dialog__footer d-flex justify-content-end p-1">
                <button class="btn btn-primary" :disabled="multiple ? !value.length : !value" @click="confirm">{{ btn_text ? btn_text : 'Confirmar' }}</button>
            </span>
        </el-dialog>
    </div>
</template>
<script>
export default {
    props: ['title', 'default', 'btn_text', 'multiple', 'options', 'value_index', 'label_index'],
    data() {
        return {
            showing: false,
            value: this.multiple ? [] : null,
        }
    },
    methods: {
        open() {
            this.value = this.default ? this.default : null
            this.showing = true
        },
        confirm() {
            this.showing = false
            return this.$emit('selected', this.value)
        },
    },
}
</script>
