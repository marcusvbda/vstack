import ElementUi from 'element-ui'
import lang from 'element-ui/lib/locale/lang/pt-br'
import VueCurrencyInput from 'vue-currency-input'
import Vue from 'vue'
Vue.use(VueCurrencyInput)
Vue.use(ElementUi, { locale: lang })
