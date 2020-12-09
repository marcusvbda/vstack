import Vue from 'vue'
import axios from 'axios'
const axiosRetry = require('axios-retry')
axiosRetry(axios, {
	retries: 3,
	shouldResetTimeout: true,
	retryCondition: (_error) => true
})
Vue.prototype.$http = axios
