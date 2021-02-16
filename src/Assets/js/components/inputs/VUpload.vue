<template>
    <tr>
        <td class="w-25">
            <div class="d-flex flex-column">
                <span class="title" v-if="label" v-html="label ? label : ''" />
                <small v-if="description" class="mt-1" style="color: gray">
                    <span v-html="description"></span>
                </small>
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                <el-upload
                    multiple
                    :limit="!multiple ? 1 : limit"
                    ref="uploader"
                    :disabled="fileList.length >= limit"
                    v-bind:class="{ disabled: fileList.length >= limit }"
                    :action="uploadroute"
                    :list-type="listtype"
                    :file-list="fileList"
                    :on-success="handleAvatarSuccess"
                    :headers="header"
                >
                    <div slot="file" slot-scope="{ file }">
                        <img class="el-upload-list__item-thumbnail" :src="file.url" alt="" />
                        <div class="el-upload-list__item-actions">
                            <span class="el-upload-list__item-preview" v-if="preview != undefined" @click="handlePictureCardPreview(file)">
                                <i class="el-icon-zoom-in"></i>
                            </span>
                            <span v-if="!disabled" class="el-upload-list__item-delete" @click="handleRemove(file)">
                                <i class="el-icon-delete"></i>
                            </span>
                        </div>
                    </div>
                    <template>
                        <div class="d-flex align-items-center justify-content-center h-100" v-if="listtype == 'picture-card'">
                            <i class="el-icon-plus"></i>
                        </div>
                        <div v-if="['text', 'picture'].includes(listtype)" class="d-flex align-items-center justify-content-center h-100">
                            <button type="button" class="btn btn-primary btn-sm-block"><i class="el-icon-plus"></i> Selecione o arquivo</button>
                        </div>
                    </template>
                </el-upload>
                <el-dialog :visible.sync="dialogVisible" v-if="preview != undefined">
                    <img width="100%" :src="dialogImageUrl" alt="" />
                </el-dialog>
            </div>
        </td>
    </tr>
</template>
<script>
export default {
    props: ['label', 'field', 'preview', 'listtype', 'multiple', 'disabled', 'limit', 'uploadroute'],
    data() {
        return {
            dialogImageUrl: '',
            dialogVisible: false,
            fileList: [],
            header: { 'X-CSRF-TOKEN': laravel.general.csrf_token ? laravel.general.csrf_token : '' },
        }
    },
    mounted() {
        let value = this.$attrs.value ? this.$attrs.value : []
        value.filter((x) => x).forEach((item) => this.fileList.push({ url: item }))
    },
    watch: {
        fileList(val) {
            return this.$emit(
                'input',
                val.map((x) => (x.url ? x.url : x))
            )
        },
    },
    methods: {
        handleAvatarSuccess(res, file) {
            this.fileList.push(file.response.path)
        },
        handleRemove(file, fileList) {
            let files = this.$refs.uploader.uploadFiles
            files = files.filter((f) => {
                return f.uid != file.uid
            })
            this.fileList = files.map((x) => (x.response ? x.response.path : x.url))
            this.$refs.uploader.uploadFiles = files
        },
        handlePictureCardPreview(file) {
            this.dialogImageUrl = file.response ? file.response.path : file.url
            this.dialogVisible = true
        },
    },
}
</script>
<style lang="scss">
.disabled {
    .el-upload {
        &.el-upload--picture-card {
            display: none;
        }
    }
}
</style>
