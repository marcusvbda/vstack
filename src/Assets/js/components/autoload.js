import Vue from "vue";

const files = require.context("./", true, /(\/)(?!.*\/)(?!-.*$).*\.vue$/i);
files.keys().map((key) => Vue.component(key.split("/").pop().split(".")[0], files(key).default));

import moment from "moment";
require("./libs/charts");
require("./libs/cookies");
require("./libs/vmask");
require("./libs/hash");
require("./libs/linkPreview");
require("./libs/pace");
require("jquery-ui-dist/jquery-ui");
require("bootstrap");
import axios from "./libs/axios";
import { Model } from 'vue-api-query'
Vue.prototype.$http = axios;
Model.$http = axios;
require("./libs/element");
require("./libs/loadash");
require("./libs/pace");
require("./libs/helpers");
require("./libs/marked");
require("@fortawesome/fontawesome-free/js/all.js");
Vue.config.productionTip = false;
Vue.config.devtools = true;
Vue.prototype.$moment = moment;

const debug = require("console-development");
Vue.prototype.$debug = debug;
import io from "socket.io-client";
Vue.prototype.$io = io;
import PortalVue from "portal-vue";
Vue.use(PortalVue);
import getDefaultStore from "../../store";

const vue_settings = {
    store: null,
    data() {
        return {
            root_loading: false,
        };
    },
    created() {
        this.init();
        this.$pace.start();
    },
    mounted() {
        this.$pace.stop();
    },
    methods: {
        init() {
            const theme = localStorage.getItem('theme') || 'dark';
            if (theme != 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            let body = document.querySelector("body");
            body.style.display = "block";
        },
    },
};
import Vuex from "vuex";
Vue.use(Vuex);

window.VueApp = {
    div: "#app",
    settings: vue_settings,
    app: null,
    store_modules: getDefaultStore(),
    setStore(newStore) {
        this.store = newStore;
    },
    setSettings(newSettings) {
        this.settings = newSettings;
    },
    setStoreModules(newModules) {
        this.store_modules = newModules;
    },
    appendStoreModule(moduleName, newModule) {
        this.store_modules.modules[moduleName] = newModule;
    },
    initStore() {
        this.settings.store = new Vuex.Store(this.store_modules);
    },
    setAxios(axiosEngine) {
        Vue.prototype.$http = axiosEngine;
        Model.$http = axiosEngine;
    },
    start() {
        this.initStore();
        this.app = new Vue(this.settings);
        this.app.$mount(this.div);
    },
    getAxiosClient() {
        return this.app.$http;
    }
};
