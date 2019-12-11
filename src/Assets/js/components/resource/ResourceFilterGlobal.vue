<template>
    <form class="needs-validation mb-0" v-on:submit.prevent="submit" >
        <el-input size='medium' placeholder="Pesquisar ..." name="_" type="text" class="filter" v-model="filter" @change="submit" v-bind:class="{'withFilter':filter}"> 
            <i class="el-icon-search el-input__icon" slot="suffix"></i>
        </el-input>
    </form>
</template>
<script>
export default {
    props:['data'],
    data() {
        return {
            filter : this.data.value,
            timeout : null
        }
    },
    watch:{
        filter(val) {
            clearTimeout(this.timeout)
            this.timeout = setTimeout( _ => {
                this.submit()
                clearTimeout(this.timeout)
            },1500)
        }
    },
    methods : {
        submit() {
            this.makeNewRoute()
        },
        makeNewRoute() {
            let str_query = ""
            for(let i in this.data.query) {
                if((i!="page")&&(i!="_")) {
                    if(!["null",null].includes(this.data.query[i])) {
                        str_query += `${i}=${this.data.query[i]}&`
                    }
                }
            }
            str_query = str_query.slice( 0, -1 )
            str_query += `${str_query ? "&" : ""}_=${this.filter}`
            window.location.href =  `${this.data.filter_route}?${str_query}`
        }
    }
}
</script>