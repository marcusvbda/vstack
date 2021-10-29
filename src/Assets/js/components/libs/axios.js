import Vue from "vue";
import axios from "axios";
import axiosRetry from "axios-retry";
axiosRetry(axios, {
    retries: 0,
    shouldResetTimeout: true,
    retryCondition: () => true,
});
Vue.prototype.$http = axios;
