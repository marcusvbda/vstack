<template>
    <tr>
        <template v-for="(item,i) in cols">
            <td :key="i" v-if="loading">
                <div
                    class="shimmer"
                    :style="{
                            width : w ? w : '100%',
                            height : h ? h : '100%'
                        }"
                />
            </td>
        </template>
        <template v-for="(key,i) in Object.keys(content)">
            <td :key="i" v-if="!loading">
                <div class="d-flex flex-column">
                    <template v-if="i ==0">
                        <b>
                            <a
                                :href="`{${resource_route}/${row_code}`"
                                class="link"
                                v-html="content[key]"
                            />
                        </b>
                    </template>
                    <template v-else>
                        <div v-html="content[key]" />
                    </template>

                    <resource-crud-buttons
                        v-if="i==0"
                        :data="{
                        code : row_code,
                        route : `${resource_route}/${row_code}`,
                        can_view : can_view,
                        can_update : can_update,
                        can_delete : can_delete
                    }"
                        :id="row_id"
                    />
                </div>
            </td>
        </template>
    </tr>
</template>
<script>
export default {
    props: ["type", "can_update", "can_delete", "can_view", "row_code", "resource_route", "cols", "row_id", "type", "resource_id", "h", "w"],
    data() {
        return {
            loading: true,
            content: {},
            attempts: 0
        }
    },
    created() {
        setTimeout(() => {
            this.getContent()
        }, 2000)
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