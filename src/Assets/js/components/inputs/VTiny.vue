<template>
    <CustomResourceComponent :label="label" :description="description">
        <div class="flex flex-col">
            <slot name="prepend-slot" />
            <div
                class="inline-editor"
                :style="`min-height : ${tiny_options.height}px;`"
            >
                <Editor :init="tiny_options" v-model="val" />
            </div>
            <small
                class="text-neutral-400 text-right"
                v-html="limitText"
                v-if="show_value_length"
            />
            <slot name="append-slot" />
        </div>
    </CustomResourceComponent>
</template>
<script>
import Editor from '@tinymce/tinymce-vue';
export default {
    props: [
        'label',
        'placeholder',
        'errors',
        'disabled',
        'description',
        'maxlength',
        'value',
        'show_value_length',
        'height',
    ],
    data() {
        return {
            val: null,
            protected: true,
            tiny_options: {
                height: this.height,
                menubar: false,
                statusbar: false,
                toolbar_location: 'top',
                inline: true,
                placeholder: this.placeholder,
                language: 'pt_BR',
                plugins: [
                    'advlist lists link image charmap preview insertdatetime media table paste code help wordcount',
                ],
                toolbar:
                    'undo redo | bold italic underline strikethrough |\
                    fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify |\
                    outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak |\
                    charmap emoticons | insertfile image media code link',
            },
        };
    },
    watch: {
        val(val) {
            return this.$emit('input', val);
        },
        value(val) {
            this.val = val;
        },
    },
    components: {
        Editor,
    },
    computed: {
        rest() {
            return this.max - (this.val || '').length;
        },
        limitText() {
            return `${this.rest}/${this.max}`;
        },
        max() {
            return parseInt(this.maxlength ? this.maxlength : 0);
        },
    },
    created() {
        this.val = this.value;
    },
};
</script>

<style lang="scss">
.tox-notifications-container {
    display: none !important;
}

.inline-editor {
    .mce-content-body {
        &:before {
            left: 30px !important;
            color: #dfdfdf !important;
        }

        position: relative;
        min-height: inherit;
        padding: 20px 30px;
        border: 1px solid #d7d7d7;
        border-radius: 3px;

        &:hover {
            transition: 0.4s;
            border: 1px solid #2f2f2f82;
        }
    }
}

.tox-editor-container {
    .tox-editor-header {
        border-color: #d7d7d7 !important;

        .tox-toolbar__primary {
            .tox-toolbar__group {
                background-color: #2f2f2f !important;
                border-bottom: 1px solid #2f2f2f !important;

                .tox-split-button {
                    &:hover {
                        box-shadow: 0 0 0 1px #565656 inset;
                    }

                    &:active {
                        .tox-tbtn {
                            background-color: #565656 !important;
                        }
                    }
                }

                .tox-tbtn {
                    &:active {
                        .tox-tbtn {
                            background-color: #565656 !important;
                        }
                    }

                    &.tox-split-button__chevron svg {
                        fill: rgba(255, 255, 255, 0.729) !important;
                    }

                    .tox-icon svg {
                        fill: rgba(255, 255, 255, 0.729);
                    }

                    &.tox-tbtn--disabled {
                        .tox-icon svg {
                            fill: rgba(255, 255, 255, 0.215);
                        }
                    }

                    &:hover {
                        background-color: #565656 !important;
                    }

                    &.tox-tbtn--enabled {
                        background-color: #565656 !important;
                    }
                }
            }

            .tox-toolbar__group {
                border-right: 1px solid #2f2f2f !important;
            }
        }
    }
}
</style>
