import Vue from 'vue'
const files = require.context('./', true, /(\/)(?!.*\/)(?!-.*$).*\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

require("./libs/charts")
require("./libs/cookies")
require("./libs/vmask")
require("./libs/hash")
require("./libs/linkPreview")
require('summernote')
require("./libs/pace")
require('jquery-ui-dist/jquery-ui')
require('bootstrap')
require('./libs/axios')
require('./libs/element')
require('./libs/echo')
require('./libs/loadash')
require('./libs/pace')
require('./libs/helpers')
Vue.config.productionTip = false
Vue.config.devtools = true
const vue = new Vue({
    data() {
        return {
            root_loading: false
        }
    },
    el: '#app',
    created() {
        this.init()
        this.$pace.start()
    },
    mounted() {
        this.$pace.stop()
    },
    methods: {
        init() {
            let body = document.querySelector("body")
            body.style.display = 'block'

            if (laravel.user) {
                if (laravel.user.code) {
                    this.$http.post(`${laravel.general.root_url}/admin/vstack/notifications/${laravel.user.code}`, {}).then(res => {
                        res = res.data
                        res.notifications.filter(not => not.alert_type == 'vstack_alert').map(not => {
                            setTimeout(() => {
                                this.$message({ showClose: true, message: not.data.message, type: not.data.type })
                            })
                        })
                    })
                    if (laravel.user.code && laravel.chat.pusher_key) {
                        Echo.private(`App.User.${laravel.user.id}`).notification(n => {
                            setTimeout(() => {
                                this.$message({ showClose: true, message: n.message, type: n._type })
                                this.$http.delete(`${laravel.general.root_url}/admin/vstack/notifications/${laravel.user.code}/${n.id}/destroy`, {})
                            })
                        })
                    }
                }
            }
        },
    }
})
window.vue = vue