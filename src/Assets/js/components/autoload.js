import Vue from 'vue'
const files = require.context('./', true, /(\/)(?!.*\/)(?!-.*$).*\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

require("./libs/charts")
require("./libs/vmask")
