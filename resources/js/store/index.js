import Vue from 'vue';
import Vuex from 'vuex';
import getters from './getters';

Vue.use(Vuex);

// const modulesFiles = require.context('./modules', false, /\.js$/);

// const modules = modulesFiles.keys().reduce((modules, modulePath) => {
//   const moduleName = (modulePath.replace(/^\.\/(.*)\.\w+$/, '$1')).replace(/-([a-z])/g, g => g[1].toUpperCase());
//   const value = modulesFiles(modulePath);
//   modules[moduleName] = value.default;
//   return modules;
// }, {});

import app from './modules/app';
import user from './modules/user';
import loading from './modules/loading';

const modules = {
  app,
  user,
  loading,
};

const store = new Vuex.Store({
  modules,
  getters,
});

export default store;
