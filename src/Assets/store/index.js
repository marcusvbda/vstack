import Vue from "vue";
import Vuex from "vuex";

import resource from "./modules/resource.module";

Vue.use(Vuex);

export default function() {
    const Store = new Vuex.Store({
        modules: {
            resource
        }
    });

    return Store;
}
