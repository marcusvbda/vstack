<template>
    <div>
        <div
            v-if="loading"
            class="shimmer"
            :style="{
                width : w ? w : '100%',
                height : h ? h : '100%'
            }"
        />
        <template v-else>
            <div v-html="content" />
        </template>
    </div>
</template>
<script>
export default {
    props: ["type", "row_id", "type", "table_key", "resource_id", "h", "w"],
    data() {
        return {
            loading: true,
            content: "",
            attempts: 0
        }
    },
    created() {
        this.getContent()
    },
    methods: {
        getContent() {
            switch (this.type) {
                case "resourceTableContent":
                    return this.getResourceTableContent()
                    break
                case "resourceTableIndex":
                    return this.getResourceTableIndex()
                    break
            }

        },
        getResourceTableIndex() {
            this.attempts++
            this.$http.post(`/vstack/${this.resource_id}/get-partial-content`, {
                type: this.type
            }).then(resp => {
                resp = resp.data
                this.content = resp.html
                this.loading = false
            }).catch(er => {
                if (this.attempts <= 3) return this.getResourceTableIndex()
                this.loading = false
                console.log(er)
            })
        },
        getResourceTableContent() {
            this.attempts++
            this.$http.post(`/vstack/${this.resource_id}/get-partial-content`, {
                row_id: this.row_id,
                table_key: this.table_key,
                type: this.type
            }).then(resp => {
                resp = resp.data
                this.content = resp
                this.loading = false
            }).catch(er => {
                if (this.attempts <= 3) return this.getResourceTableContent()
                this.loading = false
                console.log(er)
            })
        }
    }
}
</script>
<style lang="scss" scoped>
@-webkit-keyframes placeholderShimmer {
    0% {
        background-position: -468px 0;
    }

    100% {
        background-position: 468px 0;
    }
}

.shimmer {
    background: #f6f7f8;
    background-image: linear-gradient(
        to right,
        #f6f7f8 0%,
        #edeef1 20%,
        #f6f7f8 40%,
        #f6f7f8 100%
    );
    background-repeat: no-repeat;
    background-size: 800px "100%";
    display: inline-block;
    position: relative;

    -webkit-animation-duration: 1s;
    -webkit-animation-fill-mode: forwards;
    -webkit-animation-iteration-count: infinite;
    -webkit-animation-name: placeholderShimmer;
    -webkit-animation-timing-function: linear;
}
</style>