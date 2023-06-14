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
import 'bootstrap-vue/dist/bootstrap-vue.css';
// import './style/index.scss';
import 'bootstrap/dist/css/bootstrap.css';

Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(VueAnime);
Vue.use(VueApexCharts);
Vue.use(ElementUI);
Vue.config.productionTip = false;
Vue.component('Apexchart', VueApexCharts);
new Vue({
  el: '#app',
  i18n,
  router,
  store,
  render: h => h(App),
});

