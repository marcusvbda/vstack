<template>
    <div class="grapes-preview-window" v-bind:class="{ 'is-invalid': errors }">
        <iframe
            v-show="started"
            @load="onLoadIframe"
            ref="iframe"
            src="/vstack/grapes-editor"
            width="100%"
            frameborder="0"
            :style="{ height: `${height}px!important` }"
            class="dark:border-none"
        />
        <div class="invalid-feedback" v-if="errors">
            <ul class="text-sm text-red-700">
                <li v-for="(e, i) in errors" :key="i" v-html="e" />
            </ul>
        </div>
        <div class="shimmer w-full" :style="{ height }" v-if="!started" />
    </div>
</template>

<script>
import presetWebPage from 'grapesjs-preset-webpage';
import presetNewsletter from 'grapesjs-preset-newsletter';
import gpsCustomCode from 'grapesjs-custom-code';
import variables from '../../../../../../../../resources/sass/_variables.scss';
import BR from '../libs/grapes_translate';

export default {
    props: {
        height: {
            type: Number,
            default: 1000,
        },
        value: {
            type: String,
            default: null,
        },
        settings: {
            type: Object,
            default: () => ({}),
        },
        blocks: {
            type: Object,
            default: () => {},
        },
        errors: {
            type: Array,
            default: () => [],
        },
        mode: {
            type: String,
            default: 'webpage',
        },
    },
    data() {
        return {
            started: false,
            iframeWindow: null,
            content: this.value,
        };
    },
    computed: {
        pluginsOptions() {
            return {
                webpage: presetWebPage,
                newsletter: presetNewsletter,
            };
        },
        plugins() {
            return [this.pluginsOptions[this.mode], gpsCustomCode];
        },
    },
    watch: {
        content(val) {
            this.$emit('input', val);
        },
    },
    methods: {
        onLoadIframe() {
            this.initiVariables();
            this.setGrapesOptions();
            Promise.all([
                this.setTheme(),
                this.startEditor(),
                this.createExtraBlocks(),
                this.setInitialValues(),
                this.createModel(),
            ]).then(() => {
                this.started = true;
            });
        },
        setGrapesOptions() {
            this.iframeWindow.grapesOptions = {
                showOffsets: 1,
                noticeOnUnload: 0,
                fromElement: true,
                storageManager: { autoload: 0 },
                plugins: this.plugins,
                allowScripts: 1,
                assetManager: {
                    headers: {
                        'X-CSRF-TOKEN': laravel.general.csrf_token
                            ? laravel.general.csrf_token
                            : '',
                    },
                    upload: laravel.vstack.default_upload_route + '?grapes=1',
                },
                ...this.settings,
                i18n: {
                    locale: 'BR',
                    detectLocale: false,
                    messages: { BR },
                },
            };
        },
        initiVariables() {
            this.iframeWindow = this.$refs.iframe.contentWindow;
        },
        startEditor() {
            this.iframeWindow.startEditor();
        },
        createExtraBlocks() {
            Object.keys(this.blocks).forEach((key) => {
                this.iframeWindow.grapesEditor.BlockManager.add(
                    key,
                    this.blocks[key]
                );
            });
        },
        setInitialValues() {
            this.iframeWindow.grapesEditor.setComponents(this.content);
        },
        createModel() {
            this.iframeWindow.grapesEditor.on('change', () => {
                if (this.started) {
                    this.processModel();
                }
            });
        },
        minify(content) {
            content = content
                .replace(/\\>[\r\n ]+\\</g, '><')
                .replace(/(<.*?>)|\s+/g, (m, $1) => ($1 ? $1 : ' '))
                .trim();
            return content;
        },
        processModel() {
            const content = this.iframeWindow.grapesEditor.runCommand(
                'gjs-get-inlined-html'
            );
            if (content) {
                this.content = this.minify(content ?? '');
            }
        },
        setTheme() {
            let doc = this.$refs.iframe.contentDocument;
            doc.body.innerHTML =
                doc.body.innerHTML +
                `
            <style>  
                .gjs-four-color{ 
                    color : ${variables.primary};
                }
                .gjs-four-color-h:hover {
                    color : ${variables.primary};
                }
                .gjs-one-bg {
                    background-color : ${variables.quaternary};
                }
            </style>`;
        },
    },
};
</script>
<style lang="scss">
.gjs-four-color {
    color: rgb(92, 151, 235);
}

.gjs-four-color-h:hover {
    color: rgb(92, 151, 235);
}

.grapes-preview-window {
    display: flex;
    flex-direction: column;
    height: 100% !important;

    iframe {
        height: 100% !important;
        overflow: hidden;
        padding: 0.1;
        border: 1px solid #e2e2e2;
    }

    &.is-invalid {
        iframe {
            border: 1px solid #dc3545;
        }

        .invalid-feedback {
            display: block;
        }
    }
}
</style>
