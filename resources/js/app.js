import Vue from 'vue';
import App from './views/App.vue';
import i18n from './lang/index';
import router from './router';
import store from './store';
import './permission';
import VueAnime from 'vue-animejs';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import VueApexCharts from 'vue-apexcharts';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import locale from 'element-ui/lib/locale/lang/en';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import { WebCam } from 'vue-web-cam';
// import './style/index.scss';
import 'bootstrap/dist/css/bootstrap.css';
import VeeValidate from 'vee-validate';
import { ValidationProvider } from 'vee-validate';
import HighchartsVue from 'highcharts-vue';

Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(VueAnime);
Vue.use(VueApexCharts);
Vue.use(WebCam);
Vue.use(ElementUI, { locale });
Vue.use(VeeValidate, {
  inject: true,
  fieldsBagName: 'veeFields',
});
Vue.component('ValidationProvider', ValidationProvider);
Vue.config.productionTip = false;
Vue.component('Apexchart', VueApexCharts);
Vue.use(HighchartsVue);

new Vue({
  el: '#app',
  i18n,
  router,
  store,
  render: h => h(App),
});

