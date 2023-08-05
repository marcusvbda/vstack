<template>
    <CustomResourceComponent :label="label" :description="description">
        <div class="d-flex flex-column">
            <slot name="prepend-slot" />
            <input
                v-if="!loading"
                class="el-input__inner"
                :value="processed_defaultValue"
            />
            <slot name="append-slot" />
        </div>
    </CustomResourceComponent>
</template>
<script>
export default {
    props: [
        'label',
        'description',
        'field',
        'defaultValue',
        'type',
        'options',
        'model',
        'format',
    ],
    data() {
        return {
            loading: true,
            option_list: [],
        };
    },
    created() {
        this.init();
    },
    computed: {
        processed_defaultValue() {
            const actions = {
                belongsTo: () => {
                    if (this.option_list.length) {
                        const indexes = Array.isArray(this.defaultValue)
                            ? this.defaultValue.map((x) => String(x))
                            : [String(this.defaultValue)];

                        let found = this.option_list.filter((option) =>
                            indexes.includes(option.id.toString())
                        );

                        if (!found.length) return '';

                        const foundValue = found
                            .map((x) => x?.value)
                            .filter((x) => x)
                            .join(', ');
                        const foundName = found
                            .map((x) => x?.name)
                            .filter((x) => x)
                            .join(', ');
                        return foundName || foundValue;
                    }
                    return '';
                },
                check: () => {
                    return this.$getEnabledIcons(this.defaultValue);
                },
                datetimerange: () => {
                    return (
                        Array.isArray(this.defaultValue)
                            ? this.defaultValue
                            : []
                    )
                        .map((date) => {
                            return this.formatDate(this.defaultValue);
                        })
                        .join(' - ');
                },
                date: () => {
                    return this.formatDate(this.defaultValue);
                },
                datetime: () => {
                    return this.formatDate(this.defaultValue);
                },
                upload: () => {
                    let images = (
                        Array.isArray(this.defaultValue)
                            ? this.defaultValue
                            : []
                    )
                        .map((image) => {
                            return `<img src='${image.url}' />'`;
                        })
                        .join('');
                    return `<div class='upload-image-preview'>${images}</div>`;
                },
            };
            return actions[this.type]
                ? actions[this.type]()
                : this.defaultValue;
        },
    },
    methods: {
        formatDate(dt) {
            let date = this.$moment(dt);
            if (!date.isValid()) {
                return '';
            }
            return date.format(this.format);
        },
        async init() {
            if (this.type == 'belongsTo') {
                if (this.model) {
                    let { data } = await this.$http.post('/vstack/json-api', {
                        model: this.model,
                        order_by: ['id', 'desc'],
                        where: [['id', '=', this.defaultValue]],
                    });
                    this.option_list = data;
                } else {
                    this.option_list = this.options;
                }
            }
            this.loading = false;
        },
    },
};
</script>
<style lang="scss">
.upload-image-preview {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: flex-start;

    img {
        height: 145px;
        border-radius: 6px;
        border: 1px solid #c3c3c3;

        + img {
            margin-left: 5px;
        }
    }
}
</style>
