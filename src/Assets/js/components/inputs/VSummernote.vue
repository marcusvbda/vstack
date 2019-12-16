<template>
    
    <div>
        <div class="form-group row mb-3">
            <label class="col-sm-2 col-form-label" v-if="label"><span v-html="label ? label : ''"></span></label>
            <div class="col-sm-10" v-bind:class="{'col-sm-10' : label,'col-sm-12':!label}">
                <div v-loading="loading" class="w-100" v-bind:class="{'summernote-is-invalid' : errors}">
                    <textarea ref="textarea" style="display:none" v-model="text" :name="name"></textarea>
                    <div class="invalid-feedback" v-if="errors">
                        <ul class="pl-3 mb-0">
                            <li v-for="(e,i) in errors">{{e}}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        disabled :
        {
            type: Boolean,
            default: false
        },
        disableresize:
        {
            type: Boolean,
            default: false
        },
        name:
        {
            type: String,
            default: 'text'
        },
        label:
        {
            type: String,
            default: 'text'
        },
        placeholder: {
            type: String,
            default: ''
        },
        height: {
            type: Number,
            default: 350
        },
        minHeight: {
            type: Number,
            default: 200
        },
        maxHeight: {
            type: Number,
            default: 700
        },
        focus: {
            type: Boolean,
            default: true
        },
        errors :{
            type: Array,
            default: null
        },
        uploadroute : {
            type: String,
            default: 'text'
        },
        toolbar :{
            type: Array,
            default: () =>  [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['insert', ['link', 'picture']],
                ['misc', ['codeview','fullscreen']],
                ['height', ['height']]
            ]
        }
    },
    data() {
		return {
			text : null,
            initialized : false,
            loading : false,
            options : {
                lang : "pt-BR",
                placeholder: this.placeholder,
                popover: {},
                toolbar: this.toolbar,
                height: this.height,
                disableResizeEditor: this.disableresize,
                minHeight: this.minHeight,
                maxHeight: this.maxHeight,
                focus: this.focus,
                prettifyHtml: true,
                codemirror: {
                    theme: 'default',
                    mode: "text/html",
                    lineNumbers: true,
                    tabMode: 'indent'
                },
                callbacks: {
                    onInit: () => {
                        this.$emit('onInit')
                    },
                    onEnter: () => {
                        this.$emit('onEnter')
                    },
                    onFocus: () => {
                        this.$emit('onFocus')
                    },
                    onBlur: () => {
                        this.$emit('onBlur')
                    },
                    onKeyup: (e) => {
                        this.$emit('onKeyup', e)
                    },
                    onKeydown: (e) => {
                        this.$emit('onKeydown', e)
                    },
                    onPaste: (e) => {
                        this.$emit('onPaste', e)
                    },
                    onImageUpload: (files) => {
                        this.$emit('onImageUpload', files)
                    },
                    onChange: (contents) => {
                        this.$emit('onChange', contents)
                        this.$emit('input', contents)
                    },
                    onImageUpload: (image) => {
                        this.loading = true
                        this.uploadImage(image[0])
                    }
                }
            }
		}
	},
    async created() {
        $( document ).ready( _ => {
            var params = Object.assign({}, this.options)
            if (this.disabled) {
                $(this.$refs.textarea).summernote('disable')
            }
            // $(".note-group-image-url").remove()
            $(this.$refs.textarea).summernote(this.options)
            this.run('code',this.$attrs.value)
        })
    },
    beforeDestroy: function () {
        $(this.$refs.textarea).summernote('destroy')
    },
    methods: {
        uploadImage:function(image)
        {
            let data = new FormData()
            data.append("file", image)
            let self = this
            $.ajax({
                url: this.uploadroute,
                cache: false,
                contentType: false,
                processData: false,
                data: data,
                type: "POST",
                success: function(response) 
                {
                    self.loading = false
                    var image = $('<img>').attr('src', response.path)
                    $(self.$refs.textarea).summernote("insertNode", image[0])
                },
                error: function(data) {
                    console.log(data)
                    self.loading = false
                }
            })
        },
        run: function (code, value) {
            if (typeof value === undefined) {
                return $(this.$refs.textarea).summernote(code,"")
            } else {
                
                return $(this.$refs.textarea).summernote(code, value)
            }
        }
    }
}
</script>
<style lang="scss">
.note-editor {
    &.note-frame {
        border-radius: 3px;
        border: 1px solid #ced4da;
    }
}
.summernote-is-invalid {
    .note-editor {
        &.note-frame {
            border: 1px solid #dc3545;
        }
    }
    .invalid-feedback {
        display : block;
    }
}
</style>