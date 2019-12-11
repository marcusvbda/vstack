<template>
    <div class="navbar-nav mb-0 globalsearch w-100" >
        <el-autocomplete v-model="filter" :fetch-suggestions="querySearchAsync" 
            placeholder="Pesquisar ..." class="w-100"
            @select="handleSelect"
        >
            <i class="el-icon-search el-input__icon" slot="suffix"></i>
            <template slot-scope="{ item }">
                    <div class="value">{{ item.resource }}</div>
                    <span class="_link">{{ item.name }}</span>
            </template>
        </el-autocomplete> 
    </div>
</template>
<script>
export default {
    props:['data','route'],
    data() {
        return {
            links: [],
            filter: null,
            timeout:  null,
            timeout : null
        }
    },
    methods : {
        querySearchAsync(queryString, cb) {
            if(!queryString) return
            clearTimeout(this.timeout)
            this.timeout = setTimeout( _ => {
                this.$http.post(this.route,{filter : queryString}).then( res => {
                    res = res.data
                    cb(res.data)
                    clearTimeout(this.timeout)
                }).catch( er => {
                    cb([])
                })
            },1500)
        },
        handleSelect(item) {
            window.location.href=item.link
        }
    }
}
</script>
<style scoped lang="scss">
    .globalsearch {
        // margin-left:112px;
        width: 300px;
        padding-left : 200px;
        padding-right : 200px;
    }
    .el-scrollbar {
        .el-autocomplete-suggestion__list {
            li {
                line-height: normal;
                .value {
                    text-overflow: ellipsis;
                    overflow: hidden;
                    color: gray;
                    font-weight: 600;
                    font-size: 15px;
                    border-bottom: 1px solid #f1f1f1;
                }
                ._link {
                    color: gray;
                    font-size: 15px;
                    margin-bottom:20px;
                }
            }
        }
    }
</style>