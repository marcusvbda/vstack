<template>
    <CustomResourceComponent :label="label" :description="description">
        <slot name="prepend-slot" />
        <div class="input-markdown">
            <div class="row-buttons" v-if="show_btns">
                <el-button-group>
                    <el-button @click="handler('undo')" size="small" :disabled="position == 0">
                        <i class="fas fa-undo" />
                    </el-button>
                    <el-button @click="handler('redo')" size="small" :disabled="position == hystory.length - 1">
                        <i class="fas fa-redo" />
                    </el-button>
                </el-button-group>
                <el-button-group class="spaced">
                    <el-button @click="handler('bold')" size="small">
                        <i class="fas fa-bold" />
                    </el-button>
                    <el-button @click="handler('italic')" size="small">
                        <i class="fas fa-italic" />
                    </el-button>
                    <el-button @click="handler('striket')" size="small">
                        <i class="fas fa-strikethrough" />
                    </el-button>
                    <el-button @click="handler('h')" size="small">
                        <i class="fas fa-heading" />
                    </el-button>
                </el-button-group>
                <el-button-group class="spaced">
                    <el-button @click="handler('left')" size="small">
                        <i class="fas fa-align-left" />
                    </el-button>
                    <el-button @click="handler('center')" size="small">
                        <i class="fas fa-align-center" />
                    </el-button>
                    <el-button @click="handler('right')" size="small">
                        <i class="fas fa-align-right" />
                    </el-button>
                </el-button-group>
                <el-button-group class="spaced">
                    <el-button @click="handler('table')" size="small">
                        <i class="fas fa-table" />
                    </el-button>
                    <el-button @click="handler('image')" size="small">
                        <i class="fas fa-images" />
                    </el-button>
                    <el-button @click="handler('link')" size="small">
                        <i class="fas fa-link" />
                    </el-button>
                </el-button-group>
                <el-button-group class="spaced">
                    <el-button @click="handler('list')" size="small">
                        <i class="fas fa-list" />
                    </el-button>
                    <el-button @click="handler('numberList')" size="small">
                        <i class="fas fa-list-ol" />
                    </el-button>
                    <el-button @click="handler('code')" size="small">
                        <i class="fas fa-code" />
                    </el-button>
                    <el-button @click="handler('quotes')" size="small">
                        <i class="fas fa-quote-right" />
                    </el-button>
                </el-button-group>
                <el-radio-group v-model="type" class="ml-auto spaced" size="small">
                    <el-radio-button label="editor">
                        <i class="fas fa-code" />
                    </el-radio-button>
                    <el-radio-button label="both">
                        <i class="fas fa-square" />
                    </el-radio-button>
                    <el-radio-button label="html">
                        <i class="fab fa-html5" />
                    </el-radio-button>
                </el-radio-group>
                <el-radio-group v-model="dir" class="spaced" size="small">
                    <el-radio-button label="row">
                        <i class="fas fa-arrows-alt-h" />
                    </el-radio-button>
                    <el-radio-button label="column">
                        <i class="fas fa-arrows-alt-v" />
                    </el-radio-button>
                </el-radio-group>
            </div>
            <div :class="`row-editor direction-${dir}`" :style="{
                minHeight: height * (dir == 'row' ? 1 : 2)
            }">
                <textarea class="editor" ref="editor" rows="10" v-model="markdown" :placeholder="placeholder"
                    v-if="['editor', 'both'].includes(type)" />
                <div class="preview" v-html="compiled" v-if="['html', 'both'].includes(type)" />
            </div>
        </div>
        <slot name="append-slot" />
    </CustomResourceComponent>
</template>
<script>
export default {
    props: {
        description: {
            type: String,
            default: ""
        },
        direction: {
            type: String,
            default: "row"
        },
        label: {
            type: String,
            default: ""
        },
        placeholder: {
            type: String,
            default: "Digite seu conteúdo markdown ou html"
        },
        height: {
            type: Number,
            default: 500
        },
        mode: {
            type: String,
            default: "both"
        },
        show_btns: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            markdown: this.$attrs.value ?? "",
            type: this.mode,
            dir: this.direction,
            hystory: [this.$attrs.value ?? ""],
            position: 0
        };
    },
    watch: {
        markdown(val) {
            this.$emit("input", val);
        }
    },
    computed: {
        positionMarkdown() {
            return this.hystory[this.position];
        },
        compiled() {
            return this.$marked(this.markdown);
        }
    },
    methods: {
        updateHystory() {
            this.hystory.push(this.markdown);
            this.position = this.hystory.length - 1;
        },
        handler(action) {
            const actions = {
                italic: () => {
                    this.insertAtCursor(`<i>Itálico</i>`);
                },
                bold: () => {
                    this.insertAtCursor(`<b>Negrito</b>`);
                },
                striket: () => {
                    this.insertAtCursor(`<del>Riscado</del>`);
                },
                h: () => {
                    this.insertAtCursor(`<h1>Grande</h1>`);
                },
                image: () => {
                    let url = window.prompt("Por favor insira a url da imagem", "https://");
                    this.insertAtCursor(`<img width="100" alt="Imagem" src="${url}">`);
                },
                link: () => {
                    let url = window.prompt("Por favor insira o link", "https://");
                    this.insertAtCursor(`<a href='${url}'>Seu Link</a> `);
                },
                list: () => {
                    this.insertAtCursor(`<ul><li>Item da Lista</li></ul>`);
                },
                numberList: () => {
                    this.insertAtCursor(`<ol><li>Item da Lista</li></ol>`);
                },
                code: () => {
                    this.insertAtCursor("<code>Código</code>");
                },
                quotes: () => {
                    this.insertAtCursor(`<blockquote>Citação</blockquote>`);
                },
                left: () => {
                    this.insertAtCursor(`<div align="left">Esquerda</div>`);
                },
                center: () => {
                    this.insertAtCursor(`<div align="center">Centro</div>`);
                },
                right: () => {
                    this.insertAtCursor(`<div align="right">Direita</div>`);
                },
                table: () => {
                    this.insertAtCursor(`<table><tr><th>Lorem</th><th>Ipsum</th></tr><tr><td>X</td><td>y</td></tr></table>`);
                },
                undo: () => {
                    if (this.position > 0) {
                        this.position--;
                        this.markdown = this.positionMarkdown;
                    }
                },
                redo: () => {
                    if (this.position < this.hystory.length) {
                        this.position++;
                        this.markdown = this.positionMarkdown;
                    }
                }
            };

            actions[action] ? actions[action]() : null;

            if (!["redo", "undo"].includes(action)) {
                this.updateHystory();
            }
        },
        insertAtCursor(val) {
            let el = this.$refs.editor;
            if (document.selection) {
                el.focus();
                let sel = document.selection.createRange();
                sel.text = val;
                this.markdown = sel.text;
            } else if (el.selectionStart || el.selectionStart == "0") {
                let startPos = el.selectionStart;
                let endPos = el.selectionEnd;
                el.value = el.value.substring(0, startPos) + val + el.value.substring(endPos, el.value.length);
                this.markdown = el.value;
            } else {
                el.value += val;
                this.markdown = el.value;
            }
        }
    }
};
</script>
<style lang="scss">
.input-markdown {
    display: flex;
    flex-direction: column;
    width: 100%;

    .row-editor {
        display: flex;
        width: 100%;

        &.direction-row {
            flex-direction: row;
        }

        &.direction-column {
            flex-direction: column;
        }

        flex-direction: row;

        .editor {
            flex: 1;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
            font-size: 14px;
            line-height: 1.5;
            resize: none;
            color: #0cc;
            background: #232828;
            border: 1px solid rgba(128, 128, 128, 0.33);
            border-radius: 0;
            line-break: anywhere;
        }

        .preview {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            line-height: 1.5;
            background: white;
            border: 1px solid rgba(128, 128, 128, 0.33);
            border-radius: 0;
            line-break: anywhere;

            blockquote {
                overflow: hidden;
                background: #f3f6f8;
                padding: 15px 13px 0;
                border-radius: 3px;
            }

            hr {
                border: none;
                border-bottom: 1px solid rgba(black, 0.2);
            }

            img {
                max-width: 100%;
                height: auto;
            }

            ul,
            ol {
                margin-left: 0;
                padding-left: 20px;

                li {
                    margin-left: 0;
                }
            }
        }
    }

    .row-buttons {
        display: flex;
        flex-direction: row;
        width: 100%;
        margin-bottom: 5px;

        .spaced {
            margin-left: 10px;
        }
    }
}
</style>
