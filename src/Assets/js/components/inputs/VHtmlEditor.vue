<template>
    <tr>
        <td colspan="2">
            <div class="d-flex flex-column card-title">
                <span class="input-title" v-if="label" v-html="label ? label : ''" />
                <small v-if="description" class="mt-1 text-muted">
                    <span v-html="description"></span>
                </small>
            </div>
            <div :id="id" ref="content" />
        </td>
    </tr>
</template>
<script>
import 'grapesjs/dist/css/grapes.min.css'

// import 'grapesjs-preset-newsletter/dist/grapesjs-preset-newsletter.css'
import grapesJsCustomCode from 'grapesjs-custom-code'
import grapesPresetNewsLetter from 'grapesjs-preset-newsletter'
import grapesPluginForms from 'grapesjs-plugin-forms'
import grapesPresetWebpage from 'grapesjs-preset-webpage'
import grapesjs from 'grapesjs'
// import pt from 'grapesjs/locale/pt'
export default {
    props: {
        default: {
            type: Object,
            default: () => undefined,
        },
        uploadroute: {
            type: String,
            default: laravel.vstack.default_upload_route,
        },
        label: {
            type: String,
            default: '',
        },
        mode: {
            type: String,
            default: 'webpage',
        },
        id: {
            type: String,
            default: 'gjs',
        },
        height: {
            type: String,
            default: '800px',
        },
    },
    data() {
        return {
            editor: null,
            block_manager: null,
            content: this.default != undefined ? this.default : this.$attrs.value,
            pluginsArray: {
                newsletter: [grapesPresetNewsLetter],
                webpage: [grapesPresetWebpage, grapesJsCustomCode, grapesPluginForms],
            },
        }
    },
    watch: {
        content: {
            deep: true,
            handler(val) {
                this.$emit('input', val)
            },
        },
    },
    created() {
        this.$nextTick(() => {
            this.init()
        })
    },
    methods: {
        init() {
            this.editor = grapesjs.init({
                container: `#${this.id}`,
                height: this.height,
                plugins: this.pluginsArray[this.mode],
                storageManager: {
                    autosave: true,
                    autoload: false,
                    stepsBeforeSave: 0,
                    contentTypeJson: true,
                },
                assetManager: {
                    upload: this.uploadroute,
                    uploadName: 'files',
                    headers: { 'X-CSRF-TOKEN': laravel.general.csrf_token ? laravel.general.csrf_token : '' },
                    multiUpload: false,
                },
                style: this.content.css ?? '',
                components: this.content.body ?? '',
            })
            this.block_manager = this.editor.BlockManager

            this.editor.on('change', (e) => {
                this.getContent()
            })
        },
        getContent() {
            let content = {}
            content.css = this.editor.getCss()
            content.body = this.editor.getHtml()
            this.content = content
        },
    },
}
</script>