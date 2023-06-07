import Vue from 'vue';
import { config } from '@vue/test-utils';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import VueApexCharts from 'vue-apexcharts';
import 'jest-canvas-mock';
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(VueApexCharts);
config.mocks.$t = key => key;
Vue.config.productionTip = false;
config.showDeprecationWarnings = false;
Vue.component('Apexchart', VueApexCharts);
// Fix Error Error: Not implemented: window.scrollTo
const noop = () => {};
Object.defineProperty(window, 'scrollTo', { value: noop, writable: true });
