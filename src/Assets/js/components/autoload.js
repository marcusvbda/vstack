import Vue from "vue";
const files = require.context("./", true, /(\/)(?!.*\/)(?!-.*$).*\.vue$/i);
files.keys().map(key =>
    Vue.component(
        key
            .split("/")
            .pop()
            .split(".")[0],
        files(key).default
    )
);
import moment from "moment";
require("./libs/charts");
require("./libs/cookies");
require("./libs/vmask");
require("./libs/hash");
require("./libs/linkPreview");
require("summernote");
require("./libs/pace");
require("jquery-ui-dist/jquery-ui");
require("bootstrap");
require("./libs/axios");
require("./libs/element");
require("./libs/echo");
require("./libs/loadash");
require("./libs/pace");
require("./libs/helpers");
require("@fortawesome/fontawesome-free/js/all.js");
import gsap from "gsap";
import ScrollTrigger from "gsap/ScrollTrigger";
gsap.registerPlugin(ScrollTrigger);
Vue.prototype.$gsap = gsap;
Vue.config.productionTip = false;
Vue.config.devtools = true;
Vue.prototype.$moment = moment;
import PortalVue from "portal-vue";
Vue.use(PortalVue);
import AOS from "aos";
import "aos/dist/aos.css";
Vue.prototype.$aos = AOS;

const vue = new Vue({
    store: require("../../store").default,
    data() {
        return {
            root_loading: false
        };
    },
    el: "#app",
    created() {
        this.init();
        this.$pace.start();
    },
    mounted() {
        this.$pace.stop();
    },
    methods: {
        init() {
            this.$aos.init({
                disable: !laravel.vstack.animation_enabled,
                once: true
            });
            let body = document.querySelector("body");
            body.style.display = "block";

            if (laravel.user) {
                if (laravel.user.code) {
                    this.$http
                        .post(`${laravel.general.root_url}/admin/vstack/notifications/${laravel.user.code}`, {})
                        .then(res => {
                            res = res.data;
                            res.notifications
                                .filter(not => not.alert_type == "vstack_alert")
                                .map(not => {
                                    setTimeout(() => {
                                        this.$message({ showClose: true, message: not.data.message, type: not.data.type });
                                    });
                                });
                        });
                }
            }
        }
    }
});
window.vue = vue;
