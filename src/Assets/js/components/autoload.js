import Vue from 'vue'
const files = require.context('./', true, /(\/)(?!.*\/)(?!-.*$).*\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
import moment from 'moment'
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
Vue.prototype.$moment = moment
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

				}
			}
		},
	}
})
window.vue = vue
