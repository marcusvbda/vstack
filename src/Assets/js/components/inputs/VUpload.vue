<template>
    <CustomResourceComponent :label="label" :description="description">
        <el-progress v-if="loading" :text-inside="true" :stroke-width="18" class="mb-3" :percentage="progress" />
        <slot name="prepend-slot" />
        <div v-show="loading" class="shimmer resource-tree-item" :style="{ width: '100%', height: 130 }" />
        <div v-show="!loading" class="my-4">
            <div class="d-flex flex-column upload-resource-field input-group">
                <el-upload multiple :limit="!multiple ? 1 : limit" ref="uploader" :on-preview="handlePreview"
                    :onProgress="onProgress" :disabled="disabled" v-bind:class="{
                        disabled: fileList.length >= limit_value,
                        'hide-input': loading || fileList.length >= limit_value,
                        'is-invalid': errors,
                    }" :action="uploadroute" :list-type="`${is_image ? 'picture-card' : 'text'}`" :file-list="fileList"
                    :on-success="handleAvatarSuccess" :before-upload="handleBeforeUpload" :on-remove="handleRemove"
                    :before-remove="beforeRemove" :on-change="handleChange" :auto-upload="false" :headers="header"
                    v-if="renderComponent" @input.native="handleInput">
                    <template v-if="!is_image">
                        <el-button icon="el-icon-upload" type="primary" v-if="fileList.length < limit">
                            Fazer Upload
                        </el-button>
                    </template>
                    <template v-else>
                        <div slot="file" slot-scope="{ file }">
                            <div>
                                <img class="el-upload-list__item-thumbnail" :src="file.url" alt="" />
                                <div class="el-upload-list__item-actions">
                                    <span class="el-upload-list__item-preview" v-if="preview != undefined"
                                        @click="handlePreview(file)">
                                        <i class="el-icon-zoom-in"></i>
                                    </span>
                                    <span class="el-upload-list__item-delete" @click="handleRemove(file)">
                                        <i class="el-icon-delete"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <i class="el-icon-plus"></i>
                        </div>
                    </template>
                </el-upload>
                <small class="mt-2 text-muted text-size-alert">
                    {{ multiple ? "Os arquivos devem conter no máximo" : "O arquivo deve conter no máximo" }}
                    {{ $niceBytes(file_upload_limit_size) }}
                </small>
                <div class="invalid-feedback" v-if="errors">
                    <ul class="pl-3 mb-0">
                        <li v-for="(e, i) in errors" :key="i" v-html="e" />
                    </ul>
                </div>
            </div>
        </div>
        <slot name="append-slot" />
    </CustomResourceComponent>
</template>
<script>
import { mapMutations } from "vuex";
export default {
    props: {
        is_image: {
            type: Boolean,
            default: true,
        },
        auto_set_name: {
            type: Boolean,
            default: true,
        },
        label: String,
        field: String,
        preview: Boolean,
        multiple: Boolean,
        disabled: Boolean,
        limit: Number,
        uploadroute: String,
        description: String,
        sizelimit: Number,
        errors: [Array, Boolean, Object],
    },
    data() {
        return {
            progress: 0,
            initialized: false,
            new_filename: null,
            fileList: [],
            header: { "X-CSRF-TOKEN": laravel.general.csrf_token ? laravel.general.csrf_token : "" },
            loading: false,
            limit_value: this.multiple ? this.limit : 1,
            renderComponent: true,
            file_upload_limit_size: this.sizelimit ? this.sizelimit : laravel.vstack.file_upload_limit_size ?? 0
        };
    },
    mounted() {
        this.init();
    },
    watch: {
        loading(val) {
            this.setActionBtnLoading(val);
            this.progress = 0;
        },
    },
    methods: {
        ...mapMutations("resource", ["setActionBtnLoading"]),
        onProgress(event) {
            if (event.lengthComputable) {
                var percentComplete = event.loaded / event.total;
                this.progress = parseInt(Math.round(percentComplete * 100))
            }
        },
        handlePreview(file) {
            if (this.preview) {
                window.open(file.response.path, "_blank");
            }
        },
        beforeRemove() {
            if (this.ask_remove) {
                return this.$confirm(`Remover este arquivo ?`);
            }
        },
        init() {
            let value = this.$attrs.value ? this.$attrs.value : [];
            let items = [];
            value
                .filter((x) => x)
                .forEach((item) => {
                    if (typeof item == "string") {
                        items.push({
                            name: item,
                            uid: new Date().getTime(),
                            response: {
                                path: item,
                            },
                            url: item,
                        });
                    } else {
                        items.push(item);
                    }
                });
            this.setInputFiles(items);
            this.initialized = true;
        },
        setInputFiles(items) {
            this.fileList = items;
            this.$refs.uploader.uploadFiles = items;
            this.forceRerender();
            this.handleChange();
        },
        forceRerender() {
            this.renderComponent = false;
            this.$nextTick(() => {
                this.renderComponent = true;
            });
        },
        handleInput(event) {
            if (!this.auto_set_name && this.initialized) {
                this.loading = true;
                this.$prompt("Como deseja nomear este arquivo ?", "Nome", {
                    confirmButtonText: "Continuar",
                    cancelButtonText: "Cancelar",
                    inputPattern: /^.{1,}$/,
                    inputErrorMessage: "Digite um nome válido",
                })
                    .then(({ value }) => {
                        this.renameFile(value, () => this.$refs.uploader.submit());
                    })
                    .catch(() => {
                        const filename = event.target.value.split("\\").reverse()[0];
                        this.handleRemove(filename);
                        this.loading = false;
                    });
            } else {
                this.loading = true;
                this.renameFile("--RENAME-FILE--", () => this.$refs.uploader.submit());
            }
        },
        renameFile(name, callback) {
            setTimeout(() => {
                const index = this.$refs.uploader.uploadFiles.length - 1;
                const original_name = this.$refs.uploader.uploadFiles[index].raw.name;
                const ext = original_name.split('.').pop();
                const new_name = `${name}.${ext}`;
                const original_file = this.$refs.uploader.uploadFiles[index].raw;
                const new_file = new File([original_file], new_name);
                new_file.uid = original_file.uid;
                this.new_filename = new_name;
                this.$refs.uploader.uploadFiles[index].raw = new_file;
                this.$refs.uploader.uploadFiles[index].name;
                setTimeout(() => callback());
            });
        },
        handleChange() {
            return this.emitChanges();
        },
        emitChanges() {
            let files = this.$refs.uploader.uploadFiles;
            return this.$emit("input", files);
        },
        handleBeforeUpload(file) {
            if (file.size > this.file_upload_limit_size) {
                this.$message.error(`O arquivo deve conter no máximo ${this.$niceBytes(this.file_upload_limit_size)}`);
                this.loading = false;
                return false;
            }
            this.loading = true;
            return true;
        },
        handleAvatarSuccess(response, file) {
            file.url = response.path;
            let files = this.fileList;
            file.name = this.new_filename;
            files.push(file);
            this.setInputFiles(files);
            this.loading = false;
        },
        handleRemove(file) {
            let files = this.$refs.uploader.uploadFiles;
            files = files.filter((f) => {
                if (typeof file == "string") {
                    return f.name != file;
                } else {
                    return f.uid != file.uid;
                }
            });
            this.setInputFiles(files);
        }
    },
};
</script>
<style lang="scss">
.disabled {
    .el-upload {
        &.el-upload--picture-card {
            display: none;
        }
    }
}

.hide-input {
    overflow: hidden;

    .el-upload--picture-card {
        display: none;
    }
}

.upload-resource-field {
    .is-invalid {
        .el-upload--picture-card {
            border-color: #dc3545;
        }
    }
}

.el-upload-list__item {
    min-width: 148px;

    .el-icon-close {
        margin-top: 3px;
    }

    .el-icon-upload-success {
        margin-top: 8px;
    }
}
</style>
